@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Profile Settings</h1>
<p class="mb-4">Ubah your account's profile information and password.</p>

<div class="row">
    <!-- Profile Information Card -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
            </div>
            <div class="card-body">
                <p>Ubah your account's profile information and email address.</p>
                
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>
    
    <!-- Update Password Card -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
            </div>
            <div class="card-body">
                <p>Ensure your account is using a long, random password to stay secure.</p>
                
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
    
    <!-- Delete Account Card -->
    <div class="col-lg-12">
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Delete Account</h6>
            </div>
            <div class="card-body">
                <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
