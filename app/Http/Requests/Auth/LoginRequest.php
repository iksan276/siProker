<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Get the input credentials
        $email = $this->input('email');
        $password = $this->input('password');
        
        // Get the SSO code from session
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            // If no SSO code is available, try standard authentication
            if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
            
            RateLimiter::clear($this->throttleKey());
            return;
        }
        
        // Call API to get user data using the SSO code
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("http://localhost:8000/api/users", [
            'order_by' => 'Nama',
            'sort' => 'desc',
            'limit' => 5,
            'search' => $email
        ]);
        
        if (!$response->successful()) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Failed to connect to authentication service.',
            ]);
        }
        
        $userData = $response->json();
        
        if (empty($userData) || !is_array($userData)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        
        // Find the user with matching email
        $apiUser = null;
        foreach ($userData as $user) {
            if (isset($user['EmailG']) && $user['EmailG'] === $email) {
                $apiUser = $user;
                break;
            }
        }
        
        if (!$apiUser) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        
        // Compare the password from API with the input password
        if (!isset($apiUser['Password']) || $apiUser['Password'] !== $password) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => trans('auth.password'),
            ]);
        }
        
        // Authentication successful - find or create the user in our database
        $isAdmin = false;
        if (isset($apiUser['Posisi']) && isset($apiUser['Posisi']['Nama'])) {
            $isAdmin = (strpos($apiUser['Posisi']['Nama'], 'BITKom') !== false);
        }
        
        $localUser = User::updateOrCreate(
            ['email' => $apiUser['EmailG']],
            [
                'name' => $apiUser['Nama'],
                'password' => bcrypt($apiUser['Password']), // Store hashed password
                'level' => $isAdmin ? 1 : 2, // 1 for admin, 2 for regular user
            ]
        );
        
        // Login the user
        Auth::login($localUser, $this->boolean('remember'));
        
        RateLimiter::clear($this->throttleKey());
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
