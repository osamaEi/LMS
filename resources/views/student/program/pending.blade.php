@extends('layouts.dashboard')

@section('title', 'طلب التسجيل - قيد المراجعة')

@push('styles')
<style>
    .dash-page { max-width: 1240px; margin: 0 auto; }

    .dash-header {
        background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .dash-header::before {
        content: '';
        position: absolute;
        top: -60%; left: -8%;
        width: 350px; height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        border-radius: 50%;
    }
    .dash-header::after {
        content: '';
        position: absolute;
        bottom: -70%; right: -3%;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
        border-radius: 50%;
    }

    .d-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .d-card { background: #1f2937; }

    .d-card-head {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .d-card-head { border-color: #374151; }

    .icon-wrap {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    @media (max-width: 640px) { .stats-row { grid-template-columns: 1fr; } }

    .stat-box {
        background: #fff;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .dark .stat-box { background: #1f2937; }
    .stat-box .s-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-box .s-val { font-size: 1.1rem; font-weight: 800; line-height: 1.2; color: #111827; }
    .dark .stat-box .s-val { color: #f9fafb; }
    .stat-box .s-lbl { font-size: 0.78rem; color: #6b7280; margin-top: 0.15rem; font-weight: 500; }
    .dark .stat-box .s-lbl { color: #9ca3af; }

    /* Progress stepper */
    .stepper-wrap { display: flex; align-items: flex-start; padding: 1.5rem; gap: 0; }
    .step-col { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 1; }
    .step-node {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .step-node.done   { background: linear-gradient(135deg,#10b981,#059669); box-shadow: 0 6px 16px rgba(16,185,129,.3); }
    .step-node.active { background: linear-gradient(135deg,#0071AA,#004d77); box-shadow: 0 6px 16px rgba(0,113,170,.35); }
    .step-node.next   { background: #f3f4f6; }
    .dark .step-node.next { background: #374151; }

    .step-connector { flex: 1; height: 3px; margin-top: 25px; border-radius: 2px; position: relative; overflow: hidden; }
    .step-connector.filled { background: linear-gradient(90deg,#10b981,#0071AA); }
    .step-connector.empty  { background: #f3f4f6; }
    .dark .step-connector.empty { background: #374151; }
    .step-connector .anim-fill {
        position: absolute; inset: 0;
        background: linear-gradient(90deg,#10b981,#0071AA);
        transform-origin: right;
        animation: growLine 1s ease .4s both;
    }
    @keyframes growLine { from{transform:scaleX(0)} to{transform:scaleX(1)} }

    .step-label { font-size: .78rem; font-weight: 700; color: #374151; margin-top: .6rem; text-align: center; }
    .dark .step-label { color: #d1d5db; }
    .step-sub { font-size: .68rem; color: #9ca3af; margin-top: .25rem; text-align: center; }

    /* Progress bar */
    .prog-track { height: 6px; background: #f3f4f6; border-radius: 3px; overflow: hidden; }
    .dark .prog-track { background: #374151; }
    .prog-fill { height: 100%; border-radius: 3px; }

    /* Info row item */
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: .85rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        font-size: .88rem;
    }
    .dark .info-row { border-color: #1f2937; }
    .info-row:last-child { border-bottom: none; }

    /* Next step item */
    .next-item {
        display: flex; align-items: flex-start; gap: .85rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
    }
    .dark .next-item { border-color: #1f2937; }
    .next-item:last-child { border-bottom: none; }
    .next-num {
        width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; color: #fff;
        background: linear-gradient(135deg,#0071AA,#004d77);
    }

    /* Flash alert */
    .flash-warn {
        background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px;
        padding: .9rem 1.25rem; display: flex; align-items: center; gap: .7rem;
        font-size: .88rem; font-weight: 600; color: #92400e;
    }

    /* Pulse on active step */
    @keyframes stepPulse { 0%,100%{box-shadow:0 6px 16px rgba(0,113,170,.35)} 50%{box-shadow:0 6px 28px rgba(0,113,170,.6)} }
    .step-node.active { animation: stepPulse 2.5s ease-in-out infinite; }

    .link-all {
        font-size: .78rem; font-weight: 700;
        padding: .35rem .85rem; border-radius: 8px;
        transition: background .15s; text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="dash-page space-y-6" dir="rtl">

    {{-- ══ HEADER ══ --}}
    <div class="dash-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=0071AA&size=96&bold=true"
                     alt="{{ auth()->user()->name }}"
                     class="w-14 h-14 rounded-2xl border-2 shadow-lg" style="border-color: rgba(255,255,255,.2);" />
                <div>
                    <p class="text-sm" style="opacity:.65;">حالة الطلب</p>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ auth()->user()->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-bold px-3 py-1 rounded-full" style="background:rgba(245,158,11,.25);color:#fcd34d;">
                            ⏳ طلب التسجيل قيد المراجعة
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold" style="background:rgba(255,255,255,.12);">الرئيسية</a>
            </div>
        </div>
    </div>

    {{-- ══ FLASH INFO ══ --}}
    @if(session('info'))
    <div class="flash-warn">
        <svg class="w-5 h-5 flex-shrink-0" style="color:#d97706;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- ══ STAT BOXES ══ --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="s-val">تم الإرسال</div>
                <div class="s-lbl">طلبك وصل بنجاح</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#0071AA,#004d77);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="s-val">قيد المراجعة</div>
                <div class="s-lbl">الإدارة تراجع طلبك الآن</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <div class="s-val">إشعار تلقائي</div>
                <div class="s-lbl">نُبلغك فور الموافقة</div>
            </div>
        </div>
    </div>

    {{-- ══ ROW 1: STEPPER + STUDENT INFO ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Progress Stepper --}}
        <div class="lg:col-span-2 d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background:#eff6ff;">
                    <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">مراحل الطلب</span>
                <span class="px-2.5 py-1 text-xs font-bold rounded-lg" style="background:#fef9c3;color:#92400e;">قيد المراجعة</span>
            </div>

            {{-- Stepper --}}
            <div class="stepper-wrap">
                {{-- Step 1 --}}
                <div class="step-col">
                    <div class="step-node done">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="step-label">اختيار البرنامج</div>
                    <div class="step-sub">مكتمل ✓</div>
                </div>

                {{-- Connector 1 --}}
                <div class="step-connector" style="margin-top:26px;">
                    <div class="anim-fill"></div>
                </div>

                {{-- Step 2 --}}
                <div class="step-col">
                    <div class="step-node active">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="step-label" style="color:#0071AA;">قيد المراجعة</div>
                    <div class="step-sub">جارٍ العمل...</div>
                </div>

                {{-- Connector 2 --}}
                <div class="step-connector empty" style="margin-top:26px;"></div>

                {{-- Step 3 --}}
                <div class="step-col">
                    <div class="step-node next">
                        <svg class="w-6 h-6" style="color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="step-label" style="color:#9ca3af;">الموافقة</div>
                    <div class="step-sub">قريباً</div>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="px-6 pb-6">
                <div class="flex justify-between text-xs font-semibold mb-1.5" style="color:#6b7280;">
                    <span>التقدم</span>
                    <span style="color:#0071AA;">66%</span>
                </div>
                <div class="prog-track">
                    <div class="prog-fill" style="width:66%;background:linear-gradient(90deg,#10b981,#0071AA);"></div>
                </div>
                <p class="text-xs mt-2" style="color:#9ca3af;">خطوتان من ثلاث مكتملتان · في انتظار موافقة الإدارة</p>
            </div>
        </div>

        {{-- Student Info --}}
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background:#f0fdf4;">
                    <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">بيانات الطلب</span>
            </div>

            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">الاسم</span>
                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">البريد</span>
                <span class="font-bold text-gray-900 dark:text-white text-sm truncate max-w-[160px]" dir="ltr">{{ auth()->user()->email }}</span>
            </div>
            @if(auth()->user()->phone)
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">الجوال</span>
                <span class="font-bold text-gray-900 dark:text-white text-sm" dir="ltr">{{ auth()->user()->phone }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">تاريخ الطلب</span>
                <span class="font-bold text-sm" style="color:#0071AA;">{{ auth()->user()->updated_at->format('Y/m/d') }}</span>
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">الحالة</span>
                <span class="px-2.5 py-1 text-xs font-bold rounded-lg" style="background:#fef9c3;color:#92400e;">قيد المراجعة</span>
            </div>
        </div>

    </div>

    {{-- ══ ROW 2: NEXT STEPS + CONTACT ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Next Steps --}}
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background:#eff6ff;">
                    <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">الخطوات التالية</span>
            </div>

            <div class="next-item">
                <div class="next-num">١</div>
                <div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white mb-0.5">انتظار مراجعة الإدارة</div>
                    <div class="text-xs" style="color:#6b7280;">سيقوم فريق الإدارة بمراجعة طلبك في أقرب وقت ممكن.</div>
                </div>
            </div>
            <div class="next-item">
                <div class="next-num">٢</div>
                <div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white mb-0.5">إشعار بالقبول</div>
                    <div class="text-xs" style="color:#6b7280;">ستتلقى إشعاراً فور الموافقة على طلب التسجيل في البرنامج.</div>
                </div>
            </div>
            <div class="next-item">
                <div class="next-num">٣</div>
                <div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white mb-0.5">بدء الدراسة</div>
                    <div class="text-xs" style="color:#6b7280;">بعد الموافقة ستتمكن من الوصول لجميع المقررات والجلسات فوراً.</div>
                </div>
            </div>
        </div>

        {{-- Contact Card --}}
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background:#ecfdf5;">
                    <svg class="w-4 h-4" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">تواصل مع الإدارة</span>
            </div>

            <div class="p-5 space-y-4">
                <p class="text-sm" style="color:#6b7280;line-height:1.8;">
                    للاستفسار عن حالة طلبك أو ترتيب الدفع، يمكنك التواصل مع فريق الإدارة مباشرةً في أي وقت.
                </p>

                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f8fafc;">
                    <div class="icon-wrap" style="background:#ecfdf5;width:40px;height:40px;">
                        <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs" style="color:#9ca3af;">البريد الإلكتروني</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">info@institute.sa</div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f8fafc;">
                    <div class="icon-wrap" style="background:#ecfdf5;width:40px;height:40px;">
                        <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs" style="color:#9ca3af;">الهاتف</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white" dir="ltr">+966 XX XXX XXXX</div>
                    </div>
                </div>

                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold text-white"
                   style="background:linear-gradient(135deg,#0071AA,#004d77);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    العودة إلى الرئيسية
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
