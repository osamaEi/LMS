<style>
.notif-item-unread { background: linear-gradient(90deg, rgba(0,113,170,.06) 0%, transparent 100%); }
.notif-bell-ring   { animation: bellRing .5s ease; transform-origin: top center; }
@keyframes bellRing {
    0%,100%{transform:rotate(0)} 20%{transform:rotate(12deg)} 40%{transform:rotate(-10deg)}
    60%{transform:rotate(8deg)} 80%{transform:rotate(-6deg)}
}
</style>

<div class="relative" x-data="notificationDropdown()" x-init="init()" @click.away="open = false">

    {{-- Bell Button --}}
    <button @click="toggle()"
            class="relative flex h-10 w-10 items-center justify-center rounded-xl transition-all duration-200"
            :class="open ? 'bg-blue-50 dark:bg-blue-900/30' : 'hover:bg-gray-100 dark:hover:bg-gray-800'"
            :title="'الإشعارات' + (unreadCount > 0 ? ' (' + unreadCount + ')' : '')">

        {{-- Unread badge --}}
        <span x-show="unreadCount > 0" x-cloak
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -left-1 flex h-5 min-w-[20px] items-center justify-center rounded-full px-1 text-[10px] font-black text-white shadow-md"
              style="background:linear-gradient(135deg,#ef4444,#dc2626)"></span>

        {{-- Animated bell --}}
        <svg :class="{'notif-bell-ring': unreadCount > 0}"
             width="20" height="20" viewBox="0 0 24 24" fill="currentColor"
             :style="open ? 'color:#0071AA' : 'color:#6b7280'">
            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute left-0 mt-3 w-[560px] max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-boxdark z-50"
         style="border:1px solid rgba(0,0,0,.08)">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4"
             style="background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 100%)">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(255,255,255,.2)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">الإشعارات</h3>
                    <p class="text-[11px]" style="color:rgba(255,255,255,.7)"
                       x-text="unreadCount > 0 ? unreadCount + ' إشعار غير مقروء' : 'جميعها مقروءة'"></p>
                </div>
            </div>
            <button @click="markAllAsRead()" x-show="unreadCount > 0" x-cloak
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition hover:opacity-80"
                    style="background:rgba(255,255,255,.18);color:white">
                قراءة الكل
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="flex items-center justify-center py-10">
            <div class="h-8 w-8 animate-spin rounded-full border-3 border-solid"
                 style="border-color:#0071AA;border-top-color:transparent"></div>
        </div>

        {{-- List --}}
        <div x-show="!loading" class="max-h-[420px] overflow-y-auto divide-y divide-gray-100 dark:divide-strokedark">

            {{-- Empty state --}}
            <template x-if="notifications.length === 0">
                <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full"
                         style="background:linear-gradient(135deg,#eff6ff,#dbeafe)">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="#93c5fd">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300 text-sm">لا توجد إشعارات</p>
                    <p class="mt-1 text-xs text-gray-400">ستظهر هنا جميع التنبيهات والتحديثات</p>
                </div>
            </template>

            {{-- Notification items --}}
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="handleNotificationClick(notification)"
                     class="group flex items-start gap-3 px-4 py-3.5 cursor-pointer transition-all duration-150 hover:bg-gray-50 dark:hover:bg-meta-4"
                     :class="!notification.read_at ? 'notif-item-unread' : ''">

                    {{-- Icon --}}
                    <div class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl shadow-sm transition"
                         :style="!notification.read_at
                             ? 'background:linear-gradient(135deg,#0071AA,#005a88)'
                             : 'background:#f3f4f6'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"
                             :style="!notification.read_at ? 'color:white' : 'color:#9ca3af'">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug"
                           x-text="notification.data.session_title || notification.data.title || 'إشعار'"></p>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate"
                           x-text="notification.data.subject_name || notification.data.body || ''"></p>
                        <div class="mt-1.5 flex items-center gap-2">
                            <span class="text-[11px] text-gray-400" x-text="notification.created_at_human"></span>
                            <template x-if="notification.data.scheduled_at_formatted">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium"
                                      style="background:#eff6ff;color:#0071AA"
                                      x-text="notification.data.scheduled_at_formatted"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Unread dot --}}
                    <div x-show="!notification.read_at" x-cloak class="mt-1.5 flex-shrink-0">
                        <span class="inline-block h-2.5 w-2.5 rounded-full shadow-sm"
                              style="background:linear-gradient(135deg,#0071AA,#0ea5e9)"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 dark:border-strokedark">
            <a href="{{ route('notifications.page') }}"
               class="flex items-center justify-center gap-2 px-4 py-3 text-xs font-semibold transition hover:opacity-80"
               style="color:#0071AA">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
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
            if (this.open) this.fetchNotifications();
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const res = await fetch('/notifications', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.notifications = data.notifications;
                    this.unreadCount   = data.unread_count;
                }
            } catch(e) { console.error(e); }
            finally    { this.loading = false; }
        },

        async fetchUnreadCount() {
            try {
                const res = await fetch('/notifications/unread-count', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) { const d = await res.json(); this.unreadCount = d.count; }
            } catch(e) {}
        },

        async handleNotificationClick(notification) {
            if (!notification.read_at) await this.markAsRead(notification.id);
            if (notification.data.action_url) window.location.href = notification.data.action_url;
        },

        async markAsRead(id) {
            try {
                const res = await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) {
                    const d = await res.json();
                    this.unreadCount = d.unread_count;
                    const n = this.notifications.find(n => n.id === id);
                    if (n) n.read_at = new Date().toISOString();
                }
            } catch(e) {}
        },

        async markAllAsRead() {
            try {
                const res = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) {
                    this.unreadCount = 0;
                    this.notifications.forEach(n => { n.read_at = new Date().toISOString(); });
                }
            } catch(e) {}
        },

        startPolling() {
            this.pollingInterval = setInterval(() => this.fetchUnreadCount(), 30000);
        },

        destroy() {
            if (this.pollingInterval) clearInterval(this.pollingInterval);
        }
    }
}
</script>
