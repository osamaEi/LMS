<div class="relative" x-data="notificationDropdown()" x-init="init()" @click.away="open = false">
    <!-- Notification Bell Button -->
    <button @click="toggle()" class="relative flex h-10 w-10 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-0.5 -left-0.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
              x-cloak></span>

        <!-- Bell Icon -->
        <svg class="fill-current text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
        </svg>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute left-0 mt-2 w-96 max-w-[calc(100vw-2rem)] rounded-lg bg-white shadow-xl dark:bg-gray-800 z-50 border border-gray-200 dark:border-gray-700"
         x-cloak>

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">الإشعارات</h3>
            <button @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline focus:outline-none"
                    x-cloak>
                تعليم الكل كمقروء
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Notifications List -->
        <div x-show="!loading" class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="text-center py-12 px-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">لا توجد إشعارات</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div @click="handleNotificationClick(notification)"
                     class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors"
                     :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read_at }">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                 :class="notification.read_at ? 'bg-gray-200 dark:bg-gray-700' : 'bg-blue-500'">
                                <svg class="w-4 h-4" :class="notification.read_at ? 'text-gray-600 dark:text-gray-400' : 'text-white'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.data.session_title"></p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5" x-text="notification.data.subject_name"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                <span x-text="notification.data.scheduled_at_formatted"></span>
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notification.created_at_human"></p>
                        </div>

                        <!-- Unread Indicator -->
                        <div x-show="!notification.read_at" class="flex-shrink-0">
                            <span class="inline-block w-2 h-2 bg-blue-600 rounded-full" x-cloak></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <a href="#" class="block text-center text-xs text-blue-600 dark:text-blue-400 hover:underline py-1">
                عرض جميع الإشعارات
            </a>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        loading: false,
        pollingInterval: null,

        init() {
            this.fetchUnreadCount();
            this.startPolling();
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.fetchNotifications();
            }
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchUnreadCount() {
            try {
                const response = await fetch('/notifications/unread-count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.unreadCount = data.count;
                }
            } catch (error) {
                console.error('Failed to fetch unread count:', error);
            }
        },

        async handleNotificationClick(notification) {
            // Mark as read
            if (!notification.read_at) {
                await this.markAsRead(notification.id);
            }

            // Navigate to action URL
            if (notification.data.action_url) {
                window.location.href = notification.data.action_url;
            }
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.unreadCount = data.unread_count;

                    // Update local state
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.read_at = new Date().toISOString();
                    }
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.unreadCount = 0;

                    // Update local state
                    this.notifications.forEach(notification => {
                        notification.read_at = new Date().toISOString();
                    });
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        startPolling() {
            // Poll for unread count every 30 seconds
            this.pollingInterval = setInterval(() => {
                this.fetchUnreadCount();
            }, 30000); // 30 seconds
        },

        destroy() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        }
    }
}
</script>
