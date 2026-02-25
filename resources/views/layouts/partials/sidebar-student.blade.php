{{-- ═══ الرئيسية ═══ --}}
<li style="padding:16px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">الرئيسية</span>
</li>

<!-- لوحة التحكم -->
<li>
    <a href="{{ route('student.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.33333 3.33334H8.33333V10H3.33333V3.33334ZM11.6667 3.33334H16.6667V6.66667H11.6667V3.33334ZM11.6667 10H16.6667V16.6667H11.6667V10ZM3.33333 13.3333H8.33333V16.6667H3.33333V13.3333Z" fill=""/>
        </svg>
        <span>لوحة التحكم</span>
    </a>
</li>

<!-- برنامجي الدراسي -->
<li>
    <a href="{{ route('student.my-program') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.my-program') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66667L2.5 5V9.16667C2.5 13.7917 5.7 18.1167 10 19.1667C14.3 18.1167 17.5 13.7917 17.5 9.16667V5L10 1.66667ZM15.8333 9.16667C15.8333 12.9 13.3333 16.3333 10 17.4167C6.66667 16.3333 4.16667 12.9 4.16667 9.16667V6.75L10 3.58333L15.8333 6.75V9.16667Z" fill=""/>
        </svg>
        <span>برنامجي الدراسي</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ الدراسة ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">الدراسة</span>
</li>

<!-- جلساتي - Dropdown -->
<li x-data="{ open: {{ request()->routeIs('student.my-sessions') || request()->routeIs('student.upcoming-sessions') || request()->routeIs('student.schedule') ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="menu-item group relative flex w-full items-center justify-between gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.my-sessions') || request()->routeIs('student.upcoming-sessions') || request()->routeIs('student.schedule') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <div class="flex items-center gap-3">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 10L19.553 7.724C19.7054 7.64784 19.8748 7.61188 20.0451 7.61955C20.2154 7.62723 20.3812 7.67828 20.5267 7.76816C20.6723 7.85804 20.7928 7.98377 20.8769 8.13286C20.9609 8.28195 21.0059 8.44961 21.0078 8.62L21 11.38C21.0059 11.5504 20.9609 11.7181 20.8769 11.8671C20.7928 12.0162 20.6723 12.142 20.5267 12.2318C20.3812 12.3217 20.2154 12.3728 20.0451 12.3805C19.8748 12.3881 19.7054 12.3522 19.553 12.276L15 10ZM5 18H13C13.5304 18 14.0391 17.7893 14.4142 17.4142C14.7893 17.0391 15 16.5304 15 16V4C15 3.46957 14.7893 2.96086 14.4142 2.58579C14.0391 2.21071 13.5304 2 13 2H5C4.46957 2 3.96086 2.21071 3.58579 2.58579C3.21071 2.96086 3 3.46957 3 4V16C3 16.5304 3.21071 17.0391 3.58579 17.4142C3.96086 17.7893 4.46957 18 5 18Z" fill="" transform="scale(0.9)"/>
            </svg>
            <span>الجلسات</span>
        </div>
        <svg class="fill-current transition-transform duration-200" :class="{ 'rotate-180': open }" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <ul x-show="open" x-collapse class="mt-1 space-y-1 ps-8">
        <li>
            <a href="{{ route('student.my-sessions') }}"
               class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('student.my-sessions') ? 'menu-item-active' : 'menu-item-inactive' }}">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2.66667V13.3333H14V2.66667H2ZM4 4H12V6H4V4ZM4 7.33333H12V12H4V7.33333Z" fill=""/>
                </svg>
                <span>جميع الجلسات</span>
            </a>
        </li>
    </ul>
</li>

<!-- الاختبارات - Dropdown -->
@php
    $studentSubjects = collect();
    if (auth()->check() && auth()->user()->role === 'student') {
        $student = auth()->user();
        if ($student->program_id) {
            $studentSubjects = \App\Models\Subject::whereHas('term', function($q) use ($student) {
                $q->where('program_id', $student->program_id);
            })->get();
        }
    }
@endphp
<li x-data="{ open: {{ request()->routeIs('student.quizzes.*') ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="menu-item group relative flex w-full items-center justify-between gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.quizzes.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <div class="flex items-center gap-3">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.8333 2.5H4.16667C3.24167 2.5 2.5 3.24167 2.5 4.16667V15.8333C2.5 16.7583 3.24167 17.5 4.16667 17.5H15.8333C16.7583 17.5 17.5 16.7583 17.5 15.8333V4.16667C17.5 3.24167 16.7583 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V4.16667H15.8333V15.8333ZM13.3333 6.66667H6.66667V8.33333H13.3333V6.66667ZM10 10H6.66667V11.6667H10V10ZM10 13.3333H6.66667V15H10V13.3333Z" fill=""/>
            </svg>
            <span>الاختبارات</span>
        </div>
        <svg class="fill-current transition-transform duration-200" :class="{ 'rotate-180': open }" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <ul x-show="open" x-collapse class="mt-1 space-y-1 ps-8">
        @forelse($studentSubjects as $subject)
        <li>
            <a href="{{ route('student.quizzes.index', $subject->id) }}"
               class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium {{ request()->is('student/subjects/'.$subject->id.'/quizzes*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2.66667V13.3333H14V2.66667H2ZM12 12H4V4H12V12Z" fill=""/>
                </svg>
                <span class="truncate">{{ $subject->name_ar ?? $subject->name }}</span>
            </a>
        </li>
        @empty
        <li>
            <span class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium text-gray-400">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1.33334C4.32 1.33334 1.33333 4.32001 1.33333 8.00001C1.33333 11.68 4.32 14.6667 8 14.6667C11.68 14.6667 14.6667 11.68 14.6667 8.00001C14.6667 4.32001 11.68 1.33334 8 1.33334ZM8.66667 12H7.33333V10.6667H8.66667V12ZM8.66667 9.33334H7.33333V4.00001H8.66667V9.33334Z" fill=""/>
                </svg>
                <span>لا توجد مواد مسجلة</span>
            </span>
        </li>
        @endforelse
    </ul>
</li>

<!-- سجل الحضور -->
<li>
    <a href="{{ route('student.attendance') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.attendance') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z" fill=""/>
        </svg>
        <span>سجل الحضور</span>
    </a>
</li>

<!-- الملفات والموارد -->
<li>
    <a href="{{ route('student.files.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.files.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l4.5 4.5H13V4zM8 17h8v-1.5H8V17zm0-3h8v-1.5H8V14zm0-3h5v-1.5H8V11z" fill="currentColor"/>
        </svg>
        <span>الملفات والموارد</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ المالية ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">المالية</span>
</li>

<!-- المدفوعات -->
<li>
    <a href="{{ route('student.payments.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.payments.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 4.16667H2.5C1.57953 4.16667 0.833334 4.91286 0.833334 5.83333V14.1667C0.833334 15.0871 1.57953 15.8333 2.5 15.8333H17.5C18.4205 15.8333 19.1667 15.0871 19.1667 14.1667V5.83333C19.1667 4.91286 18.4205 4.16667 17.5 4.16667ZM17.5 14.1667H2.5V10H17.5V14.1667ZM17.5 7.5H2.5V5.83333H17.5V7.5Z" fill=""/>
        </svg>
        <span>المدفوعات</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ التقدم ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">التقدم</span>
</li>

<!-- التقييم والشهادات -->
<li>
    <a href="{{ route('student.grades.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.grades.*') || request()->routeIs('student.certificates.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66667L2.5 5.83333V9.16667C2.5 13.7917 5.7 18.1167 10 19.1667C14.3 18.1167 17.5 13.7917 17.5 9.16667V5.83333L10 1.66667ZM15.8333 9.16667C15.8333 12.9 13.3333 16.3333 10 17.4167C6.66667 16.3333 4.16667 12.9 4.16667 9.16667V6.75L10 3.58333L15.8333 6.75V9.16667ZM6.175 10L5 11.175L8.33333 14.5L15 7.83333L13.825 6.65833L8.33333 12.15L6.175 10Z" fill=""/>
        </svg>
        <span>التقييم والشهادات</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ المساعدة ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">المساعدة</span>
</li>

<!-- الدعم والمساعدة -->
<li>
    <a href="{{ route('student.tickets.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.tickets.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 0.833336C4.94167 0.833336 0.833336 4.94167 0.833336 10C0.833336 15.0583 4.94167 19.1667 10 19.1667C15.0583 19.1667 19.1667 15.0583 19.1667 10C19.1667 4.94167 15.0583 0.833336 10 0.833336ZM10.8333 15.8333H9.16667V14.1667H10.8333V15.8333ZM10.8333 12.5H9.16667C9.16667 9.375 12.5 9.58333 12.5 7.5C12.5 6.11667 11.3833 5 10 5C8.61667 5 7.5 6.11667 7.5 7.5H5.83333C5.83333 5.19167 7.69167 3.33333 10 3.33333C12.3083 3.33333 14.1667 5.19167 14.1667 7.5C14.1667 10.4167 10.8333 10.625 10.8333 12.5Z" fill=""/>
        </svg>
        <span>الدعم والمساعدة</span>
    </a>
</li>

<!-- الروابط المفيدة - Link to hub page -->
<li>
    <a href="{{ route('student.links') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.links') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.9 10c0-1.4 1.1-2.5 2.5-2.5H10V6H6.4C4.1 6 2.2 7.9 2.2 10.1 2.2 12.2 4.1 14 6.4 14H10v-1.5H6.4C5 12.5 3.9 11.4 3.9 10zM7 11h6V9H7v2zm6.6-5H10v1.5h3.6c1.4 0 2.5 1.1 2.5 2.5s-1.1 2.5-2.5 2.5H10V14h3.6c2.3 0 4.2-1.9 4.2-4.2C17.8 7.6 15.9 6 13.6 6z" fill=""/>
        </svg>
        <span>روابط مفيدة</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ الحساب ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">الحساب</span>
</li>

<!-- الإعدادات -->
<li>
    <a href="{{ route('student.profile') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.profile') || request()->routeIs('student.settings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.1417 10.9417C17.175 10.6333 17.2 10.325 17.2 10C17.2 9.675 17.175 9.36667 17.1417 9.05833L19.15 7.48333C19.3333 7.34167 19.3833 7.075 19.2667 6.86667L17.3667 3.63333C17.2417 3.40833 16.9833 3.325 16.7583 3.40833L14.4 4.36667C13.8917 3.98333 13.35 3.65833 12.7583 3.40833L12.4 0.891667C12.3667 0.65 12.1583 0.5 11.9167 0.5H8.08333C7.84167 0.5 7.63333 0.65 7.6 0.891667L7.24167 3.40833C6.65 3.65833 6.10833 3.99167 5.6 4.36667L3.24167 3.40833C3.01667 3.31667 2.75833 3.40833 2.63333 3.63333L0.733333 6.86667C0.608333 7.08333 0.666667 7.34167 0.85 7.48333L2.85833 9.05833C2.825 9.36667 2.8 9.68333 2.8 10C2.8 10.3167 2.825 10.6333 2.85833 10.9417L0.85 12.5167C0.666667 12.6583 0.616667 12.925 0.733333 13.1333L2.63333 16.3667C2.75833 16.5917 3.01667 16.675 3.24167 16.5917L5.6 15.6333C6.10833 16.0167 6.65 16.3417 7.24167 16.5917L7.6 19.1083C7.63333 19.35 7.84167 19.5 8.08333 19.5H11.9167C12.1583 19.5 12.3667 19.35 12.4 19.1083L12.7583 16.5917C13.35 16.3417 13.8917 16.0083 14.4 15.6333L16.7583 16.5917C16.9833 16.6833 17.2417 16.5917 17.3667 16.3667L19.2667 13.1333C19.3917 12.9083 19.3333 12.6583 19.15 12.5167L17.1417 10.9417ZM10 13.5C8.06667 13.5 6.5 11.9333 6.5 10C6.5 8.06667 8.06667 6.5 10 6.5C11.9333 6.5 13.5 8.06667 13.5 10C13.5 11.9333 11.9333 13.5 10 13.5Z" fill=""/>
        </svg>
        <span>الإعدادات</span>
    </a>
</li>
