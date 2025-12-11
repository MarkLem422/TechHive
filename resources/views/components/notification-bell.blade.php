@auth
    <div class="relative z-50" id="notification-container">
        <button onclick="toggleNotifications()" class="relative px-3 py-2 text-sm border border-white/20 bg-white/10 text-white rounded-md hover:border-white/40 hover:bg-white/20 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span id="notification-badge" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full hidden">0</span>
        </button>

        <!-- Notification Dropdown -->
        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-[#e3e3e0] rounded-lg shadow-xl z-[60] max-h-96 overflow-y-auto">
            <div class="p-4 border-b border-[#e3e3e0] flex items-center justify-between">
                <h3 class="font-semibold">Notifications</h3>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:underline">View All</a>
            </div>
            <div id="notification-list" class="divide-y divide-[#e3e3e0]">
                <div class="p-4 text-center text-sm text-[#706f6c]">Loading...</div>
            </div>
            <div class="p-2 border-t border-[#e3e3e0]">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="text-center">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:underline">Mark all as read</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="notification-toast" class="hidden fixed top-4 right-4 bg-white border border-[#e3e3e0] rounded-lg shadow-xl p-4 z-[70] max-w-sm">
        <div class="flex items-start">
            <div class="flex-1">
                <h4 id="toast-title" class="font-semibold text-[#1b1b18] mb-1"></h4>
                <p id="toast-message" class="text-sm text-[#706f6c]"></p>
            </div>
            <button onclick="closeToast()" class="ml-4 text-[#706f6c] hover:text-[#1b1b18]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        let lastNotificationId = null;
        let shownNotificationIds = new Set(); // Track all shown notifications
        let notificationCheckInterval = null;
        let toastTimeout = null;

        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.toggle('hidden');
            
            if (!dropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        }

        function closeToast() {
            const toast = document.getElementById('notification-toast');
            toast.classList.add('hidden');
            if (toastTimeout) {
                clearTimeout(toastTimeout);
                toastTimeout = null;
            }
        }

        function showToast(title, message, notificationId, orderId = null) {
            // Don't show if we've already shown this notification
            if (shownNotificationIds.has(notificationId)) {
                return;
            }

            // Mark as shown
            shownNotificationIds.add(notificationId);

            const toast = document.getElementById('notification-toast');
            document.getElementById('toast-title').textContent = title;
            document.getElementById('toast-message').textContent = message;
            toast.classList.remove('hidden');

            // Auto-hide after 5 seconds
            toastTimeout = setTimeout(() => {
                closeToast();
            }, 5000);

            // Play notification sound (optional)
            // const audio = new Audio('/notification.mp3');
            // audio.play();
        }

        function loadNotifications() {
            fetch('{{ route("notifications.latest") }}')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('notification-list');
                    
                    if (data.notifications.length === 0) {
                        list.innerHTML = '<div class="p-4 text-center text-sm text-[#706f6c]">No notifications</div>';
                        return;
                    }

                    list.innerHTML = data.notifications.map(notif => `
                        <div class="p-3 hover:bg-gray-50 ${!notif.is_read ? 'bg-blue-50' : ''}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-sm text-[#1b1b18]">${notif.title}</h4>
                                    <p class="text-xs text-[#706f6c] mt-1">${notif.message}</p>
                                    <p class="text-xs text-[#706f6c] mt-1">${notif.created_at}</p>
                                    ${notif.order_id ? `<a href="/notifications/${notif.id}/open" class="text-xs text-blue-600 hover:underline mt-1 inline-block font-semibold">Manage Order →</a>` : ''}
                                </div>
                                ${!notif.is_read ? '<span class="ml-2 w-2 h-2 bg-blue-600 rounded-full"></span>' : ''}
                            </div>
                        </div>
                    `).join('');

                    // Track the latest notification ID and mark all as seen
                    if (data.notifications.length > 0) {
                        if (!lastNotificationId) {
                            lastNotificationId = data.notifications[0].id;
                        }
                        // Mark all current notifications as shown
                        data.notifications.forEach(notif => {
                            shownNotificationIds.add(notif.id);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        function checkForNewNotifications() {
            fetch('{{ route("notifications.latest") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications.length > 0) {
                        const latest = data.notifications[0];
                        
                        // Check if this is a new notification we haven't shown yet
                        if (!shownNotificationIds.has(latest.id)) {
                            // Only show if it's actually newer than what we've seen
                            if (!lastNotificationId || latest.id > lastNotificationId) {
                                showToast(latest.title, latest.message, latest.id, latest.order_id);
                                lastNotificationId = latest.id;
                            }
                        }
                    }
                    
                    updateBadge();
                })
                .catch(error => {
                    console.error('Error checking notifications:', error);
                });
        }

        function updateBadge() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error updating badge:', error);
                });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateBadge();
            loadNotifications();
            
            // Check for new notifications every 5 seconds
            notificationCheckInterval = setInterval(checkForNewNotifications, 5000);
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (notificationCheckInterval) {
                clearInterval(notificationCheckInterval);
            }
            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('notification-container');
            if (container && !container.contains(event.target)) {
                document.getElementById('notification-dropdown').classList.add('hidden');
            }
        });
    </script>
@endauth

