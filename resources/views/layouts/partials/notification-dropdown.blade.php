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
         class="absolute left-0 mt-3 overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-boxdark z-50"
         style="width:520px;max-width:calc(100vw - 1.5rem);border:1px solid rgba(0,0,0,.08)">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 100%)">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="white">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:1rem;font-weight:800;color:#fff;margin:0;line-height:1.2">الإشعارات</h3>
                    <p style="font-size:0.76rem;color:rgba(255,255,255,.75);margin:2px 0 0;line-height:1"
                       x-text="unreadCount > 0 ? unreadCount + ' إشعار غير مقروء' : 'جميعها مقروءة'"></p>
                </div>
            </div>
            <button @click="markAllAsRead()" x-show="unreadCount > 0" x-cloak
                    style="background:rgba(255,255,255,.18);color:white;border:none;border-radius:8px;padding:6px 12px;font-size:0.8rem;font-weight:600;cursor:pointer;"
                    onmouseover="this.style.background='rgba(255,255,255,.28)'"
                    onmouseout="this.style.background='rgba(255,255,255,.18)'">
                قراءة الكل
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" style="display:flex;align-items:center;justify-content:center;padding:3rem 0">
            <div style="width:36px;height:36px;border-radius:50%;border:3px solid #0071AA;border-top-color:transparent;animation:spin 0.8s linear infinite"></div>
        </div>

        {{-- List --}}
        <div x-show="!loading" style="max-height:460px;overflow-y:auto;" class="divide-y divide-gray-100 dark:divide-strokedark">

            {{-- Empty state --}}
            <template x-if="notifications.length === 0">
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3.5rem 2rem;text-align:center">
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="#93c5fd">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                    </div>
                    <p style="font-size:1rem;font-weight:700;color:#374151;margin:0">لا توجد إشعارات</p>
                    <p style="font-size:0.85rem;color:#9ca3af;margin:6px 0 0">ستظهر هنا جميع التنبيهات والتحديثات</p>
                </div>
            </template>

            {{-- Notification items --}}
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="handleNotificationClick(notification)"
                     @mouseover="$el.style.background='#f8fafd'"
                     @mouseout="$el.style.background = notification.read_at ? '' : 'linear-gradient(90deg,rgba(0,113,170,.06) 0%,transparent 100%)'"
                     :style="`display:flex;align-items:flex-start;gap:10px;padding:11px 16px;cursor:pointer;transition:background 0.15s;border-bottom:1px solid #f3f4f6;${!notification.read_at ? 'background:linear-gradient(90deg,rgba(0,113,170,.06) 0%,transparent 100%)' : ''}`">

                    {{-- Icon --}}
                    <div :style="`flex-shrink:0;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-top:1px;${!notification.read_at ? 'background:linear-gradient(135deg,#0071AA,#005a88);box-shadow:0 2px 6px rgba(0,113,170,0.25)' : 'background:#f1f5f9'}`">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"
                             :style="!notification.read_at ? 'color:white' : 'color:#9ca3af'">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div style="flex:1;min-width:0">
                        <p style="font-size:0.875rem;font-weight:700;color:#111827;margin:0;line-height:1.35"
                           x-text="notification.data.session_title || notification.data.title || 'إشعار'"></p>
                        <p style="font-size:0.78rem;color:#6b7280;margin:3px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                           x-text="notification.data.subject_name || notification.data.body || ''"></p>
                        <div style="display:flex;align-items:center;gap:6px;margin-top:5px;flex-wrap:wrap">
                            <span style="font-size:0.72rem;color:#9ca3af" x-text="notification.created_at_human"></span>
                            <template x-if="notification.data.scheduled_at_formatted">
                                <span style="display:inline-flex;align-items:center;border-radius:999px;padding:1px 8px;font-size:0.72rem;font-weight:600;background:#eff6ff;color:#0071AA"
                                      x-text="notification.data.scheduled_at_formatted"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Unread dot --}}
                    <div x-show="!notification.read_at" x-cloak style="flex-shrink:0;margin-top:6px">
                        <span style="display:block;width:8px;height:8px;border-radius:50%;background:#0071AA"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div style="border-top:1px solid #f3f4f6">
            <a href="{{ route('notifications.page') }}"
               style="display:flex;align-items:center;justify-content:center;gap:6px;padding:10px 16px;font-size:0.84rem;font-weight:700;color:#0071AA;text-decoration:none;transition:background 0.15s;"
               onmouseover="this.style.background='#f0f9ff'"
               onmouseout="this.style.background=''">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
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
