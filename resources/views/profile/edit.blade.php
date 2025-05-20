@extends('layouts.main')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profile</h1>
    </div>

    @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') === 'profile-updated' ? 'Profile updated successfully' : session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-xl-7 col-lg-8">
            <div class="card shadow mb-4 profile-card">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Data Diri</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Profile Actions:</div>
                            <a class="dropdown-item" href="#" id="changePhotoBtn"><i class="fas fa-camera fa-sm fa-fw mr-2 text-gray-400"></i>Change Photo</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="mb-4">
                        <div class="">
                            <div class="profile-photo-wrapper">
                                <img class="profile-photo rounded-circle" id="profilePhoto"
                                   src="{{ asset('sb-admin/img/undraw_profile.svg') }}" wididth="120" height="120"
                                    alt="{{ $user->name }}">
                              
                            </div>
                        </div>
                        <h4 class="font-weight-bold text-gray-800 mt-3 user-name">{{ $user->name }}</h4>
                        <p class="text-gray-600 user-email"><i class="fas fa-envelope mr-2"></i>{{ $user->email }}</p>
                        <div class="user-status">
                            <span class="badge badge-success pulse"><i class="fas fa-circle mr-1"></i>Active</span>
                        </div>
                    </div>
                    
                    @if(isset($apiUserData))
                    <div class="user-details">
                        <div class="detail-item d-flex flex-column flex-sm-row justify-content-between py-2 border-bottom">
                            <span class="text-gray-600"><i class="fas fa-briefcase mr-2"></i>POSISI</span>
                            <span class="">{{ $apiUserData['Posisi']['Nama'] ?? '' }}</span>
                        </div>
                        <div class="detail-item d-flex flex-column flex-sm-row justify-content-between py-2 border-bottom">
                            <span class="text-gray-600"><i class="fas fa-user-tie mr-2"></i>JABATAN</span>
                            <span class="">{{ $apiUserData['Jabatan']['Nama'] ?? '' }}</span>
                        </div>
                        <div class="detail-item d-flex flex-column flex-sm-row justify-content-between py-2 border-bottom">
                            <span class="text-gray-600"><i class="fas fa-id-card mr-2"></i>NIK</span>
                            <span class="">{{ $apiUserData['NIK'] ?? '-' }}</span>
                        </div>
                        <div class="detail-item d-flex flex-column flex-sm-row justify-content-between py-2 border-bottom">
                            <span class="text-gray-600"><i class="fas fa-id-badge mr-2"></i>NIDN</span>
                            <span class="">{{ $apiUserData['NIDN'] ?? '-' }}</span>
                        </div>
                        <div class="detail-item d-flex flex-column flex-sm-row justify-content-between py-2 border-bottom">
                            <span class="text-gray-600"><i class="fas fa-home mr-2"></i>HOMEBASE</span>
                            <span class="">{{ $apiUserData['Homebase'] ?? '-' }}</span>
                        </div>
                    </div>
                    @endif
                    
                 
                </div>
            </div>
            
            <!-- Social Media Links -->
      
        </div>

        <!-- Additional Information -->
        <div class="col-xl-5 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tentang Saya</h6>
                </div>
                <div class="card-body">
                    <p>Karyawan ITP</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="skill-item mb-3">
                                <h6 class="font-weight-bold">Kreatif <span class="float-right">100%</span></h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="skill-item mb-3">
                                <h6 class="font-weight-bold">Inovatif <span class="float-right">100%</span></h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="skill-item mb-3">
                                <h6 class="font-weight-bold">Terampil <span class="float-right">100%</span></h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="skill-item mb-3">
                                <h6 class="font-weight-bold">Religius <span class="float-right">100%</span></h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                  <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Media Sosial</h6>
                </div>
                <div class="card-body">
                    <div class="social-buttons">
                        <a href="#" class="btn btn-circle btn-primary"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-circle btn-info"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-circle btn-danger"><i class="fab fa-google"></i></a>
                        <a href="#" class="btn btn-circle btn-dark"><i class="fab fa-github"></i></a>
                        <a href="#" class="btn btn-circle btn-primary"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
          
        </div>
    </div>



@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
/* Profile page custom styles */
.profile-card {
    border-radius: 15px;
    overflow: hidden;
}

.profile-photo-container {
    display: inline-block;
    position: relative;
    margin: 0 auto;
}

.profile-photo-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
    overflow: hidden;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.profile-photo-wrapper:hover .profile-photo-overlay {
    opacity: 1;
}

.profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 4px solid #4e73df;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.profile-photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
        width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 50%;
}

.profile-photo-overlay i {
    color: white;
    font-size: 2rem;
}

.user-name {
    margin-top: 1rem;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.user-email {
    color: #858796;
    font-size: 0.9rem;
}

.user-status {
    margin-top: 0.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 50px;
    margin: 0.25rem;
    transition: all 0.3s ease;
}

.pulse {
    animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

.user-details .detail-item {
    padding: 0.75rem 0;
    transition: all 0.3s ease;
    border-bottom: 1px solid #e3e6f0;
}

.user-details .detail-item:last-child {
    border-bottom: none;
}

.user-details .detail-item:hover {
    background-color: #f8f9fc;
    transform: translateX(5px);
}

.user-details .detail-item i {
    color: #4e73df;
}

.card {
    border: none;
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* Form styling */
.form-control {
    border-radius: 10px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn {
    border-radius: 10px;
    padding: 0.5rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.btn-icon-split {
    padding: 0;
    overflow: hidden;
    display: inline-flex;
    align-items: stretch;
    justify-content: center;
}

.btn-icon-split .icon {
    background: rgba(0, 0, 0, 0.15);
    display: inline-block;
    padding: 0.6rem 0.75rem;
}

.btn-icon-split .text {
    display: inline-block;
    padding: 0.6rem 1.2rem;
}

.btn-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
}

.btn-circle:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.social-buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

/* Timeline styling */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline:before {
    content: '';
    position: absolute;
    height: 100%;
    width: 2px;
    background: #e3e6f0;
    left: 32px;
    top: 0;
}

.timeline-item {
    position: relative;
    padding-left: 70px;
    padding-bottom: 1.5rem;
}

.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    text-align: center;
    line-height: 40px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-content {
    background: #f8f9fc;
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    transform: translateX(5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.timeline-date {
    display: block;
    font-size: 0.8rem;
    color: #858796;
    margin-top: 0.5rem;
}

/* Progress bars */
.skill-item h6 {
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}

.progress {
    height: 8px;
    border-radius: 10px;
    background-color: #eaecf4;
    margin-bottom: 1rem;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 1s ease-in-out;
}

/* Modal styling */
.modal-content {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    background-color: #f8f9fc;
    border-bottom: none;
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: none;
    padding: 1.5rem;
}

.upload-area {
    border: 2px dashed #4e73df;
    border-radius: 10px;
    padding: 2rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    background-color: #f8f9fc;
}

.avatar-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 1rem;
}

.avatar-option {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.avatar-option:hover {
    transform: scale(1.1);
    border-color: #4e73df;
}

/* Animations */
.animate__animated {
    animation-duration: 0.5s;
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0px);
    }
}

.float {
    animation: float 3s ease-in-out infinite;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize with animation
    $('.card').addClass('animate__animated animate__fadeIn');
    $('.profile-photo-wrapper').addClass('float');
    
    // Progress bar animation
    setTimeout(function() {
        $('.progress-bar').each(function() {
            const width = $(this).attr('aria-valuenow') + '%';
            $(this).css('width', width);
        });
    }, 500);
    
    // Photo upload modal
    $('#changePhotoBtn, .profile-photo-wrapper').on('click', function() {
        $('#photoUploadModal').modal('show');
    });
    
    // Upload area click
    $('#uploadArea').on('click', function() {
        $('#fileUpload').click();
    });
    
    // File upload handling
    $('#fileUpload').on('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('#profilePhoto').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // Avatar selection
    $('.avatar-option').on('click', function() {
        const avatarSrc = $(this).attr('src');
        $('#profilePhoto').attr('src', avatarSrc);
        $('.avatar-option').css('border-color', 'transparent');
        $(this).css('border-color', '#4e73df');
    });
    
    // Drag and drop functionality
    const uploadArea = document.getElementById('uploadArea');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        uploadArea.classList.add('bg-light');
    }
    
    function unhighlight() {
        uploadArea.classList.remove('bg-light');
    }
    
    uploadArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('#profilePhoto').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endpush

@endsection
