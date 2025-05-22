<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class OAuthGoogleController extends Controller
{
    /**
     * Handle the OAuth Google authentication
     */
   
    private function base64UrlEncode(string $data): string
     {
         $base64Url = strtr(base64_encode($data), '+/', '-_');
     
         return rtrim($base64Url, '=');
     }
   
    private function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, ['-' => '+', '_' => '/']));
    }

    private function secret($plaintext, $type)
     {
     
         if (!empty($plaintext)) {
             $iv_text = '1973022119801989';
             $key_text = "21januari1973";
             $ciphering = "AES-256-CFB";
             //hash $secret_key dengan algoritma sha256 
             $key = hash("sha512", $key_text);
     
             //iv(initialize vector), encrypt iv dengan encrypt method AES-256-CBC (16 bytes)
             $iv     = substr(hash("sha512", $iv_text), 0, 16);
     
             if ($type == 'encryption') {
                 $encryption = $this->base64UrlEncode(openssl_encrypt($plaintext, $ciphering, $key, 0, $iv));
                 return $encryption;
             } elseif ($type == 'decryption') {
                 $decryption = openssl_decrypt($this->base64UrlDecode($plaintext), $ciphering, $key, 0, $iv);
                 return $decryption;
             }
         }
     }
     
    public function authenticate(Request $request)
    {
        $code = trim($request->get('code'));
        $autentikasi = trim($request->get('autentikasi'));
        $access_level = trim($request->get('access_level'));

        if (!empty($code) && !empty($autentikasi)) {
            if ($this->secret($code, 'decryption') === 'login-sso-itp') {
                $data = json_decode($this->secret($autentikasi, 'decryption'));
                
                if ($data) {
                    // Store the code in session for future API calls
                    session(['sso_code' => $code]);
                    
                    // Get user email from the decoded data
                    $userEmail = $data->Email;
                    if (!$userEmail || !str_ends_with(strtolower($userEmail), '@itp.ac.id')) {
                        return redirect('/login')->with('swal_error', 'Hanya email dengan domain @itp.ac.id yang diperbolehkan');
                    }
                    
                    // Call API to get user data
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $code,
                    ])->get("https://webhook.itp.ac.id/api/users", [
                        'order_by' => 'Nama',
                        'sort' => 'desc',
                        'limit' => 5,
                        'search' => $userEmail
                    ]);
                    
                    if ($response->successful()) {
                        $userData = $response->json();
                        
                        if (!empty($userData) && is_array($userData)) {
                            $user = $userData[0];
                            
                            // Store complete user data in session
                            session(['api_user_data' => $user]);
                            
                            // Determine if user is admin based on position
                            $isAdmin = false;
                            $isSuperUser = false;
                            
                            if (isset($user['Posisi']) && isset($user['Posisi']['Nama'])) {
                                $isAdmin = (strpos($user['Posisi']['Nama'], 'Bagian Teknologi Informasi dan Komunikasi') !== false);
                            }
                            
                            // Check if user is a super user based on JabatanID
                            if (isset($user['JabatanID']) && in_array($user['JabatanID'], [37, 2, 3, 4, 5])) {
                                $isSuperUser = true;
                            }
                              $isAdmin = false;
                            
                            // Determine user level
                            $userLevel = 2; // Default: regular user
                            if ($isAdmin) {
                                $userLevel = 1; // Admin
                            } elseif ($isSuperUser) {
                                $userLevel = 3; // Super user
                            }
                            
                            // Find or create user in our database
                            $localUser = User::updateOrCreate(
                                ['email' => $user['EmailG']],
                                [
                                    'name' => $user['Nama'],
                                    'password' => bcrypt($user['Password']), // Note: This is not secure, consider a better approach
                                    'level' => $userLevel,
                                    'jabatan_id' => $user['JabatanID'] ?? null,
                                ]
                            );
                            
                            // Login the user
                            Auth::login($localUser);
                            
                            // Regenerate session
                            request()->session()->regenerate();
                            
                            return redirect('/dashboard');
                            
                        }else {
                            return redirect('/login')->with('swal_error', 'Data pengguna tidak terdaftar');
                        }
                    }
                    
                 // If we reach here, something went wrong with the API call
                 return redirect('/login')->with('swal_error', 'Gagal mengambil data pengguna');
                }
            } else {
                return redirect('/login')->with('swal_error', 'Kode otentikasi tidak valid');
            }
        }
        
        return redirect('/login')->with('swal_error', 'Parameter otentikasi tidak valid');
    }
}
