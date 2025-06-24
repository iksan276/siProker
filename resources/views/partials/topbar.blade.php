<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-1 text-white">
        <i class="fa fa-bars"></i>
    </button>
    <!-- SIPROKER Title with Animation -->
    <div class="siproker-title mr-3">
        <span class="siproker-text">SIPROKER</span>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

     

        <!-- Nav Item - Alerts -->
       <!-- Nav Item - Alerts (Update kondisi untuk semua user) -->
@if(auth()->user()->isAdmin() || auth()->user()->level == 3 || auth()->user()->level == 2)
<li class="nav-item dropdown no-arrow mx-1" style="z--index: 9999;">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter - Alerts -->
        <span class="badge badge-danger badge-counter" id="notification-count" style="display: none;">0</span>
    </a>
    <!-- Dropdown - Alerts -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
        aria-labelledby="alertsDropdown" style="width: 380px; max-height: 400px; overflow-y: auto;">
        <h6 class="dropdown-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Pusat Notifikasi</span>
            <button class="btn btn-sm btn-link text-white p-0" id="mark-all-read" style="font-size: 11px; text-decoration: underline;">
                Tandai Semua Dibaca
            </button>
        </h6>
        <div id="notification-list">
            <div class="text-center p-3">
                <i class="fas fa-spinner fa-spin"></i> Memuat notifikasi...
            </div>
        </div>
    </div>
</li>
@endif


        <!-- Network Status Indicator -->
        <div class="check-internet"></div>
        
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline  small text-white">{{ Auth::user()->name }}</span>
                <img class="img-profile rounded-circle" src="{{ asset('sb-admin/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ url('/profile') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400" ></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- End of Topbar -->

<!-- Add CSS for SIPROKER title animation and notifications -->
<style>
      .topbar {
        background: linear-gradient(135deg, #4c71dd 0%, #4c71dd 100%) !important;
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 10px 40px rgba(59, 130, 246, 0.2), 0 4px 20px rgba(139, 92, 246, 0.15);
        position: relative;
        z-index: 1;
    }
    
    /* Animated background overlay */
    .topbar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 3s infinite;
        pointer-events: none;
    }
    .topbar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 150%;
    background: url('../../asset/photo-1551288049-bebda4e38f71.jpeg') center/cover;
    opacity: 0.1;
    animation: float 20s ease-in-out infinite;
    z-index: -1;
}

@keyframes float {
    0%, 100% { 
        transform: translateY(0px) translateX(0px) scale(1); 
    }
    25% { 
        transform: translateY(-10px) translateX(5px) scale(1.02); 
    }
    50% { 
        transform: translateY(-5px) translateX(-5px) scale(1.01); 
    }
    75% { 
        transform: translateY(-15px) translateX(3px) scale(1.03); 
    }
}

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    
    /* SIPROKER Title Enhanced */
    .siproker-text {
        font-size: 32px;
        font-weight: 900;
        background: linear-gradient(45deg, #ffffff, #f8fafc, #e2e8f0, #ffffff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
        animation: gradient-flow 4s ease-in-out infinite, glow-pulse 3s ease-in-out infinite, float 6s ease-in-out infinite;
        text-shadow: 0 0 30px rgba(255, 255, 255, 0.4);
        letter-spacing: 3px;
        position: relative;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
        font-family: 'Poppins', sans-serif;
    }
    
    .siproker-text::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        animation: shine 4s ease-in-out infinite;
        pointer-events: none;
        border-radius: 8px;
    }
    
    .siproker-text::after {
        content: 'SIPROKER';
        position: absolute;
        top: 0;
        left: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: pulse-glow 2s ease-in-out infinite alternate;
       
    }
    
    @keyframes gradient-flow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    @keyframes glow-pulse {
        0%, 100% { 
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
        }
        50% { 
            filter: drop-shadow(0 6px 20px rgba(255, 255, 255, 0.3));
        }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-2px); }
    }
    
    @keyframes shine {
        0% { transform: translateX(-100%) skewX(-15deg); }
        100% { transform: translateX(200%) skewX(-15deg); }
    }
    
    @keyframes pulse-glow {
        0% { opacity: 0.3; }
        100% { opacity: 0.6; }
    }
    
    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    /* Notification styles */
    .notification-item {
        border-bottom: 1px solid #e3e6f0;
        transition: all 0.3s ease;
        cursor: pointer;
        padding: 12px 20px;
    }
    
    .notification-item:hover {
        background-color: #f8f9fc;
        transform: translateX(2px);
    }
    
    .notification-item.unread {
        background-color: #e7f3ff;
        border-left: 4px solid #4e73df;
        font-weight: 500;
    }
    
    .notification-item.read {
        opacity: 0.8;
    }
    
    .notification-date {
        font-size: 11px;
        color: #858796;
        margin-bottom: 2px;
    }
    
    .notification-title {
        font-weight: 600;
        color: #5a5c69;
        font-size: 13px;
        margin-bottom: 3px;
    }
    
    .notification-description {
        font-size: 12px;
        color: #6e707e;
        margin-bottom: 3px;
        line-height: 1.4;
    }
    
    .notification-sender {
        font-size: 11px;
        color: #858796;
        font-style: italic;
    }
    
    .notification-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .badge-counter {
        font-size: 10px;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse-badge 2s infinite;
    }
    
    @keyframes pulse-badge {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .dropdown-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        margin: 0;
        padding: 12px 20px;
    }
    
    .empty-notifications {
        text-align: center;
        padding: 30px 20px;
        color: #858796;
    }
    
    .empty-notifications i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    /* Loading animation */
    .loading-notifications {
        text-align: center;
        padding: 20px;
        color: #858796;
    }
    
    /* Toast notification styles */
    .toast {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .toast-header {
        border-radius: 8px 8px 0 0;
    }
</style>

<!-- Notification JavaScript -->
<!-- Update bagian JavaScript untuk redirect berdasarkan role -->
 <!-- Add this script at the bottom of your topbar.blade.php file -->
<script>
// Function to erase cookie
function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999; path=/';
}

// Function to clear all kegiatan filter cookies
function clearKegiatanFilterCookies() {
    // List of all filter cookies used in kegiatans/index.blade.php
    const filterCookies = [
        'selected_renstra',
        'selected_pilar', 
        'selected_isu',
        'selected_program_pengembangan',
        'selected_program_rektor',
        'selected_units',
        'selected_kegiatan_ids',
        'selected_status'
    ];
    
    // Clear each cookie
    filterCookies.forEach(function(cookieName) {
        eraseCookie(cookieName);
    });
    
    console.log('All kegiatan filter cookies cleared');
}


</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check user level for notification access
    @if(auth()->user()->isAdmin() || auth()->user()->level == 3 || auth()->user()->level == 2)
    
    let notificationUpdateInterval;
    
    // Load notifications on page load
    loadNotifications();
    loadUnreadCount();
    
    // Refresh notifications every 30 seconds (optimized)
    notificationUpdateInterval = setInterval(function() {
        loadUnreadCount();
    }, 30000);
    
    // Load notifications when dropdown is opened
    $('#alertsDropdown').on('click', function() {
        loadNotifications();
    });
    
    // Mark all as read
    $('#mark-all-read').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        markAllAsRead();
    });
    
    function loadNotifications() {
        $('#notification-list').html('<div class="loading-notifications"><i class="fas fa-spinner fa-spin"></i> Memuat notifikasi...</div>');
        
        $.ajax({
            url: '{{ route("notifications.index") }}',
            type: 'GET',
            timeout: 10000,
            success: function(notifications) {
                displayNotifications(notifications);
            },
            error: function(xhr, status, error) {
                console.error('Error loading notifications:', {status, error});
                $('#notification-list').html('<div class="text-center p-3 text-danger"><i class="fas fa-exclamation-triangle"></i><br>Gagal memuat notifikasi<br><small>Silakan refresh halaman</small></div>');
            }
        });
    }
    
    function loadUnreadCount() {
        $.ajax({
            url: '{{ route("notifications.unreadCount") }}',
            type: 'GET',
            timeout: 5000,
            success: function(response) {
                updateNotificationCount(response.count);
            },
            error: function(xhr) {
                console.error('Error loading unread count:', xhr);
            }
        });
    }
    
    function displayNotifications(notifications) {
        let html = '';
        
        if (notifications.length === 0) {
            html = `
                <div class="empty-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <div>Tidak ada notifikasi</div>
                    <small>Notifikasi akan muncul di sini</small>
                </div>
            `;
        } else {
            notifications.forEach(function(notification) {
                const isRead = notification.read_at !== null;
                const readClass = isRead ? 'read' : 'unread';
                const date = notification.formatted_date || new Date(notification.DCreated).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                               const iconClass = notification.Title.includes('TOR') ? 'fa-file-contract' : 
                                notification.Title.includes('Status') ? 'fa-sync-alt' : 'fa-file-alt';
                const iconColor = notification.Title.includes('TOR') ? 'bg-warning' : 
                                notification.Title.includes('Status') ? 'bg-success' : 'bg-primary';
                
                html += `
                    <div class="notification-item ${readClass}" 
                         data-notification-id="${notification.NotificationID}" 
                         data-kegiatan-id="${notification.KegiatanID}">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon ${iconColor}">
                                <i class="fas ${iconClass} text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notification-date">${formatNotificationTime(notification.DCreated)}</div>
                                <div class="notification-title">${notification.Title}</div>
                                <div class="notification-description">${notification.Description}</div>
                                <div class="notification-sender">Dari: ${notification.sender.name}</div>
                            </div>
                            ${!isRead ? '<div class="ml-2"><i class="fas fa-circle text-primary" style="font-size: 8px;"></i></div>' : ''}
                        </div>
                    </div>
                `;
            });
        }
        
        $('#notification-list').html(html);
        
        // Add click handlers for notification items
        $('.notification-item').on('click', function(e) {
            e.preventDefault();
            const notificationId = $(this).data('notification-id');
            const kegiatanId = $(this).data('kegiatan-id');
            
            // Mark as read
            markAsRead(notificationId);
            
            // Close dropdown
            $('#alertsDropdown').dropdown('hide');
            
            // Redirect based on user role
            const userLevel = {{ auth()->user()->level }};
            let redirectUrl = '';
            
            if (userLevel === 1 || userLevel === 3) {
                // Admin or Super User - redirect to kegiatans page
                redirectUrl = `/kegiatans?kegiatanID=${kegiatanId}`;
                  clearKegiatanFilterCookies();
            } else if (userLevel === 2) {
                // Regular User - redirect to pilars page
                redirectUrl = `/pilars?kegiatanID=${kegiatanId}&treeLevel=kegiatan`;
            }
            
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });
    }
    
    function updateNotificationCount(count) {
        const badge = $('#notification-count');
        if (count > 0) {
            badge.text(count > 99 ? '99+' : count).show();
        } else {
            badge.hide();
        }
    }
    
    function markAsRead(notificationId) {
        $.ajax({
            url: '{{ url("/notifications") }}/' + notificationId + '/read',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Notification marked as read');
                loadUnreadCount();
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
            }
        });
    }
    
    function markAllAsRead() {
        $.ajax({
            url: '{{ route("notifications.readAll") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                loadNotifications();
                loadUnreadCount();
                showToast('Semua notifikasi telah ditandai sebagai dibaca', 'success');
            },
            error: function(xhr) {
                console.error('Error marking all notifications as read:', xhr);
                showToast('Gagal menandai semua notifikasi', 'error');
            }
        });
    }
    
    function showToast(message, type = 'info') {
        const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const toast = $(`
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="toast-header ${bgClass} text-white">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong class="mr-auto">Notifikasi</strong>
                    <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);
        
        $('body').append(toast);
        toast.toast({ delay: 3000 }).toast('show');
        
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    // Cleanup interval on page unload
    window.addEventListener('beforeunload', function() {
        if (notificationUpdateInterval) {
            clearInterval(notificationUpdateInterval);
        }
    });
    
    @endif
});
</script>

<!-- Update bagian Pusher Scripts untuk semua user -->
@if(auth()->user()->isAdmin() || auth()->user()->level == 3 || auth()->user()->level == 2)
<!-- Pusher Scripts -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Initialize Pusher
    const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}'
            }
        }
    });

    // Subscribe to user's private notification channel
    const channel = pusher.subscribe('private-notifications.{{ auth()->id() }}');
    
    console.log('Subscribed to channel: private-notifications.{{ auth()->id() }}');
    
    // Listen for kegiatan status updates
    channel.bind('kegiatan.status.updated', function(data) {
        console.log('Received notification:', data);
        
        // Show toast notification
        showToastNotification(data.notification);
        
        // Update notification count
        setTimeout(function() {
             function updateNotificationCount(count) {
        const badge = $('#notification-count');
        if (count > 0) {
            badge.text(count > 99 ? '99+' : count).show();
        } else {
            badge.hide();
        }
    }
             function loadUnreadCount() {
        $.ajax({
            url: '{{ route("notifications.unreadCount") }}',
            type: 'GET',
            timeout: 5000,
            success: function(response) {
                updateNotificationCount(response.count);
            },
            error: function(xhr) {
                console.error('Error loading unread count:', xhr);
            }
        });
    }
            loadUnreadCount();
        }, 1000);
        
        // If notification dropdown is open, refresh the list
        if ($('#alertsDropdown').attr('aria-expanded') === 'true') {
            setTimeout(function() {
                 function updateNotificationCount(count) {
        const badge = $('#notification-count');
        if (count > 0) {
            badge.text(count > 99 ? '99+' : count).show();
        } else {
            badge.hide();
        }
    }
                 function loadUnreadCount() {
        $.ajax({
            url: '{{ route("notifications.unreadCount") }}',
            type: 'GET',
            timeout: 5000,
            success: function(response) {
                updateNotificationCount(response.count);
            },
            error: function(xhr) {
                console.error('Error loading unread count:', xhr);
            }
        });
    }
                loadNotifications();
            }, 1500);
        }
    });
    
    // Handle connection state
    pusher.connection.bind('connected', function() {
        console.log('Pusher connected successfully');
    });
    
    pusher.connection.bind('disconnected', function() {
        console.log('Pusher disconnected');
    });
    
    pusher.connection.bind('error', function(err) {
        console.error('Pusher connection error:', err);
    });
    
    function showToastNotification(notification) {
        // Create toast element with better styling
        const toast = $(`
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 400px;">
                <div class="toast-header bg-primary text-white">
                    <i class="fas fa-bell mr-2"></i>
                    <strong class="mr-auto">Notifikasi Baru</strong>
                    <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    <div class="mb-2 mt-3">
                        <strong>${notification.title}</strong>
                    </div>
                    <div class="mb-2 text-muted" style="font-size: 0.9em;">
                        ${notification.description}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>Dari: ${notification.sender_name}
                        </small>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewKegiatan(${notification.kegiatan_id})">
                            <i class="fas fa-eye mr-1"></i>Lihat Detail
                        </button>
                    </div>
                </div>
            </div>
        `);
        
        // Add to body and show
        $('body').append(toast);
        toast.toast({ delay: 10000 }).toast('show');
        
        // Remove from DOM after hiding
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
        
        // Play notification sound (optional)
        playNotificationSound();
    }
    
    // Format notification time similar to Carbon format
    function formatNotificationTime(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            
            // Convert to Jakarta timezone (UTC+7)
            const jakartaOffset = 7 * 60; // 7 hours in minutes
            const utc = date.getTime() + (date.getTimezoneOffset() * 60000);
            const jakartaTime = new Date(utc + (jakartaOffset * 60000));
            
            // Format as d-m-Y H:i:s
            const day = String(jakartaTime.getDate()).padStart(2, '0');
            const month = String(jakartaTime.getMonth() + 1).padStart(2, '0');
            const year = jakartaTime.getFullYear();
            const hours = String(jakartaTime.getHours()).padStart(2, '0');
            const minutes = String(jakartaTime.getMinutes()).padStart(2, '0');
            const seconds = String(jakartaTime.getSeconds()).padStart(2, '0');
            
            return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
        } catch (error) {
            console.error('Error formatting date:', error);
            return 'N/A';
        }
    }
    
        function viewKegiatan(kegiatanId) {
        // Close any open toasts
        $('.toast').toast('hide');
        
        // Redirect based on user role
        const userLevel = {{ auth()->user()->level }};
        let redirectUrl = '';
        
        if (userLevel === 1 || userLevel === 3) {
            // Admin or Super User - redirect to kegiatans page
            redirectUrl = `/kegiatans?kegiatanID=${kegiatanId}`;
        } else if (userLevel === 2) {
            // Regular User - redirect to pilars page
            redirectUrl = `/pilars?kegiatanID=${kegiatanId}`;
        }
        
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    
    function playNotificationSound() {
        try {
            // Simple notification beep
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            // Audio not supported or blocked
            console.log('Audio notification not available');
        }
    }
</script>
@endif





