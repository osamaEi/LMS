@extends('layouts.dashboard')

@section('title', 'طلب تسجيل - ' . $user->name)

@push('styles')
<style>
/* ── Hero ── */
.enroll-hero {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
    border-radius: 24px;
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.enroll-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -15%;
    width: 55%; height: 200%;
    background: radial-gradient(ellipse, rgba(255,255,255,0.12) 0%, transparent 70%);
    pointer-events: none;
}
.enroll-hero::after {
    content: '';
    position: absolute;
    bottom: -30%; left: -10%;
    width: 40%; height: 150%;
    background: radial-gradient(ellipse, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Avatar ── */
.hero-avatar {
    width: 90px; height: 90px;
    border-radius: 20px;
    background: rgba(255,255,255,0.2);
    border: 3px solid rgba(255,255,255,0.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; font-weight: 800; color: white;
    flex-shrink: 0;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    overflow: hidden;
}
.hero-avatar img { width:100%; height:100%; object-fit:cover; }

/* ── Info Cards ── */
.info-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1.5px solid #f1f5f9;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.info-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.1); }

.card-header {
    padding: 1.1rem 1.5rem;
    display: flex; align-items: center; gap: .85rem;
    border-bottom: 1.5px solid #f1f5f9;
}
.card-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.card-header h3 { font-size: .95rem; font-weight: 700; color: #111827; margin: 0; }
.card-body { padding: 1.25rem 1.5rem; }

/* ── Data Row ── */
.data-row {
    display: flex; align-items: center;
    padding: .7rem 0;
    border-bottom: 1px solid #f8fafc;
    gap: 1rem;
}
.data-row:last-child { border-bottom: none; padding-bottom: 0; }
.data-row .dr-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.data-row .dr-label { font-size: .72rem; color: #9ca3af; font-weight: 600; margin-bottom: 2px; }
.data-row .dr-value { font-size: .88rem; font-weight: 700; color: #111827; }

/* ── Program Banner ── */
.program-banner {
    background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
    border-radius: 16px;
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
}
.program-banner::after {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 60%; height: 200%;
    background: radial-gradient(ellipse, rgba(255,255,255,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.program-banner .prog-code {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    border-radius: 8px; padding: .25rem .75rem;
    font-size: .72rem; font-weight: 700; letter-spacing: .5px;
    margin-bottom: .75rem;
}
.program-banner .prog-name { font-size: 1.2rem; font-weight: 800; margin-bottom: .5rem; }
.program-banner .prog-meta { font-size: .8rem; color: rgba(255,255,255,.75); display: flex; gap: 1.5rem; flex-wrap: wrap; }

/* ── Action Buttons ── */
.action-approve {
    padding: .85rem 2rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white; border: none; border-radius: 14px;
    font-size: .95rem; font-weight: 800; cursor: pointer;
    display: flex; align-items: center; gap: .6rem;
    box-shadow: 0 6px 20px rgba(16,185,129,0.35);
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
}
.action-approve:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(16,185,129,0.45); color: white; }

.action-reject {
    padding: .85rem 2rem;
    background: white;
    color: #ef4444; border: 2px solid #fecaca; border-radius: 14px;
    font-size: .95rem; font-weight: 800; cursor: pointer;
    display: flex; align-items: center; gap: .6rem;
    transition: all .2s;
    text-decoration: none;
}
.action-reject:hover { background: #fef2f2; border-color: #ef4444; color: #dc2626; }

/* ── Doc preview ── */
.doc-thumb {
    border-radius: 14px; overflow: hidden;
    border: 2px solid #f1f5f9;
    background: #0f0f1a;
    position: relative;
}
.doc-thumb img {
    width: 100%; height: 160px; object-fit: cover;
    cursor: zoom-in; display: block;
    transition: opacity .2s;
}
.doc-thumb img:hover { opacity: .9; }
.doc-thumb-footer {
    background: white;
    padding: .6rem .85rem;
    display: flex; align-items: center; justify-content: space-between;
    border-top: 1.5px solid #f1f5f9;
}

/* ── Timeline ── */
.timeline { position: relative; padding-right: 1.5rem; }
.timeline::before {
    content: '';
    position: absolute; top: 0; right: 7px; bottom: 0;
    width: 2px; background: #f1f5f9;
}
.tl-item { position: relative; padding-bottom: 1.25rem; }
.tl-item:last-child { padding-bottom: 0; }
.tl-dot {
    position: absolute; right: -1.5rem; top: 2px;
    width: 16px; height: 16px; border-radius: 50%;
    border: 3px solid white; box-shadow: 0 0 0 2px currentColor;
    display: flex; align-items: center; justify-content: center;
}
.tl-dot.green { color: #10b981; background: #10b981; }
.tl-dot.blue  { color: #0071AA; background: #0071AA; }
.tl-dot.amber { color: #f59e0b; background: #f59e0b; }
.tl-dot.gray  { color: #9ca3af; background: #9ca3af; }

/* ── Reject Modal ── */
#reject-confirm-modal {
    display: none; position: fixed; inset: 0; z-index: 9990;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}

/* ── Badge ── */
.badge-pending { background: #fef9c3; color: #92400e; padding: .3rem .9rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
.badge-active  { background: #dcfce7; color: #16a34a; padding: .3rem .9rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }

/* ── Lightbox ── */
#doc-lb {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.92); backdrop-filter: blur(6px);
    align-items: center; justify-content: center;
}
</style>
@endpush

@section('content')
<div style="max-width:1100px; margin:0 auto;">

    {{-- ── Back Link ── --}}
    <a href="{{ route('admin.program-enrollments.index') }}"
       style="display:inline-flex;align-items:center;gap:.4rem;color:#0071AA;font-size:.85rem;font-weight:600;margin-bottom:1.25rem;text-decoration:none;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        العودة للطلبات المعلقة
    </a>

    {{-- ── Hero Header ── --}}
    <div class="enroll-hero">
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                {{-- Avatar --}}
                <div class="hero-avatar">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/'.$user->profile_photo) }}" alt="{{ $user->name }}">
                    @else
                        {{ mb_substr($user->name, 0, 1) }}
                    @endif
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.4rem;flex-wrap:wrap;">
                        <span style="background:rgba(255,255,255,0.2);color:white;padding:.25rem .85rem;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:.4px;">⏳ طلب تسجيل معلق</span>
                        <span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,.85);padding:.25rem .85rem;border-radius:20px;font-size:.72rem;font-weight:600;">
                            تقدّم {{ $user->updated_at->diffForHumans() }}
                        </span>
                    </div>
                    <h1 style="font-size:1.7rem;font-weight:800;color:white;margin:0 0 .3rem;">{{ $user->name }}</h1>
                    <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                        <span style="color:rgba(255,255,255,.8);font-size:.85rem;display:flex;align-items:center;gap:.4rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $user->email }}
                        </span>
                        @if($user->phone)
                        <span style="color:rgba(255,255,255,.8);font-size:.85rem;display:flex;align-items:center;gap:.4rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $user->phone }}
                        </span>
                        @endif
                        @if($user->national_id)
                        <span style="color:rgba(255,255,255,.8);font-size:.85rem;display:flex;align-items:center;gap:.4rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                            {{ $user->national_id }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
                    <form action="{{ route('admin.program-enrollments.approve', $user) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="action-approve">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            قبول الطلب
                        </button>
                    </form>
                    <button type="button" onclick="openRejectModal()" class="action-reject">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        رفض الطلب
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Grid ── --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">

        {{-- ── LEFT COLUMN ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Program Banner --}}
            <div class="program-banner">
                <div style="position:relative;z-index:1;">
                    <div class="prog-code">{{ strtoupper($user->program->code ?? 'N/A') }}</div>
                    <div class="prog-name">{{ $user->program->name_ar ?? 'غير محدد' }}</div>
                    @if($user->program && $user->program->name_en)
                    <div style="font-size:.85rem;color:rgba(255,255,255,.65);margin-bottom:.85rem;">{{ $user->program->name_en }}</div>
                    @endif
                    <div class="prog-meta">
                        @if($user->program && $user->program->duration_months)
                        <span style="display:flex;align-items:center;gap:.4rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $user->program->duration_months }} شهر
                        </span>
                        @endif
                        @if($user->program && $user->program->price)
                        <span style="display:flex;align-items:center;gap:.4rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ number_format($user->program->price) }} ريال
                        </span>
                        @endif
                        <span style="display:flex;align-items:center;gap:.4rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $user->program->status === 'active' ? 'البرنامج نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
                {{-- decorative icon --}}
                <svg style="position:absolute;left:1.5rem;top:50%;transform:translateY(-50%);opacity:.06;width:100px;height:100px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>

            {{-- Personal & Academic Data --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

                {{-- Personal Info --}}
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon" style="background:linear-gradient(135deg,#0071AA,#005a88);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3>البيانات الشخصية</h3>
                    </div>
                    <div class="card-body">
                        <div class="data-row">
                            <div class="dr-icon" style="background:#eff6ff;">
                                <svg class="w-4 h-4" style="color:#0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                            </div>
                            <div><div class="dr-label">رقم الهوية الوطنية</div><div class="dr-value" style="font-family:monospace;">{{ $user->national_id ?? '—' }}</div></div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#f0fdf4;">
                                <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div><div class="dr-label">تاريخ الميلاد</div><div class="dr-value">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y/m/d') : '—' }}</div></div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#fdf4ff;">
                                <svg class="w-4 h-4" style="color:#9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div><div class="dr-label">الجنس</div><div class="dr-value">{{ $user->gender === 'male' ? 'ذكر' : ($user->gender === 'female' ? 'أنثى' : '—') }}</div></div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#fff7ed;">
                                <svg class="w-4 h-4" style="color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div><div class="dr-label">البريد الإلكتروني</div><div class="dr-value" style="font-size:.78rem;">{{ $user->email }}</div></div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#ecfdf5;">
                                <svg class="w-4 h-4" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div><div class="dr-label">رقم الجوال</div><div class="dr-value" style="font-family:monospace;">{{ $user->phone ?? '—' }}</div></div>
                        </div>
                    </div>
                </div>

                {{-- Academic Info --}}
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <h3>المؤهل الأكاديمي</h3>
                    </div>
                    <div class="card-body">
                        <div class="data-row">
                            <div class="dr-icon" style="background:#faf5ff;">
                                <svg class="w-4 h-4" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <div class="dr-label">التخصص</div>
                                <div class="dr-value">
                                    @php
                                        $specMap = ['quran'=>'القرآن الكريم','arabic'=>'اللغة العربية','islamic_studies'=>'الدراسات الإسلامية','education'=>'التربية والتعليم','other'=>'أخرى'];
                                    @endphp
                                    {{ $specMap[$user->specialization] ?? ($user->specialization ?? '—') }}
                                </div>
                            </div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#eff6ff;">
                                <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="dr-label">نوع الشهادة</div>
                                <div class="dr-value">
                                    @php
                                        $typeMap = ['bachelor'=>'بكالوريوس','master'=>'ماجستير','phd'=>'دكتوراه','diploma'=>'دبلوم','high_school'=>'ثانوية عامة'];
                                    @endphp
                                    {{ $typeMap[$user->specialization_type] ?? ($user->specialization_type ?? '—') }}
                                </div>
                            </div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#f0fdf4;">
                                <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div><div class="dr-label">سنة التخرج</div><div class="dr-value">{{ $user->date_of_graduation ? \Carbon\Carbon::parse($user->date_of_graduation)->format('Y') : '—' }}</div></div>
                        </div>
                        <div class="data-row">
                            <div class="dr-icon" style="background:#fff7ed;">
                                <svg class="w-4 h-4" style="color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div><div class="dr-label">تاريخ التسجيل</div><div class="dr-value">{{ $user->date_of_register ? \Carbon\Carbon::parse($user->date_of_register)->format('Y/m/d') : '—' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- National ID Documents --}}
            @if($user->documents && $user->documents->count() > 0)
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                    </div>
                    <h3>صور الهوية الوطنية</h3>
                    <span style="margin-right:auto;font-size:.75rem;font-weight:700;background:#e0f2fe;color:#0369a1;padding:.25rem .8rem;border-radius:20px;">{{ $user->documents->count() }} وثيقة</span>
                </div>
                <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;">
                    @foreach($user->documents as $doc)
                    @php
                        $dlabel = match($doc->document_type) { 'national_id_front'=>'الوجه الأمامي', 'national_id_back'=>'الوجه الخلفي', default=>$doc->document_type };
                        $isImg  = in_array(strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']);
                        $dsBg   = match($doc->status) { 'approved'=>'#dcfce7','rejected'=>'#fee2e2', default=>'#fef9c3' };
                        $dsClr  = match($doc->status) { 'approved'=>'#16a34a','rejected'=>'#dc2626', default=>'#92400e' };
                        $dsTxt  = match($doc->status) { 'approved'=>'مقبول','rejected'=>'مرفوض', default=>'قيد المراجعة' };
                    @endphp
                    <div class="doc-thumb">
                        @if($isImg)
                            <img src="{{ asset('storage/'.$doc->file_path) }}" alt="{{ $dlabel }}"
                                 onclick="openDocLb('{{ asset('storage/'.$doc->file_path) }}','{{ $dlabel }}')">
                        @else
                            <div style="height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:1rem;">
                                <svg class="w-12 h-12" style="color:#a78bfa;margin-bottom:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" style="color:#0891b2;font-weight:700;font-size:.8rem;">فتح PDF</a>
                            </div>
                        @endif
                        <div class="doc-thumb-footer">
                            <span style="font-size:.78rem;font-weight:700;color:#374151;">{{ $dlabel }}</span>
                            <span style="font-size:.65rem;font-weight:700;background:{{ $dsBg }};color:{{ $dsClr }};padding:.2rem .6rem;border-radius:8px;">{{ $dsTxt }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- end left col --}}

        {{-- ── RIGHT COLUMN ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Quick Actions Card --}}
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3>الإجراءات</h3>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:.75rem;">
                    <form action="{{ route('admin.program-enrollments.approve', $user) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:.85rem;background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;border-radius:14px;font-size:.95rem;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.6rem;box-shadow:0 4px 16px rgba(16,185,129,.3);transition:opacity .2s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            قبول الطلب
                        </button>
                    </form>
                    <button type="button" onclick="openRejectModal()" style="width:100%;padding:.85rem;background:white;color:#ef4444;border:2px solid #fecaca;border-radius:14px;font-size:.95rem;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.6rem;transition:all .2s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        رفض الطلب
                    </button>
                    <a href="{{ route('admin.students.show', $user) }}" style="width:100%;padding:.75rem;background:#f8fafc;color:#374151;border:1.5px solid #e5e7eb;border-radius:14px;font-size:.875rem;font-weight:700;display:flex;align-items:center;justify-content:center;gap:.6rem;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        عرض ملف الطالب
                    </a>
                </div>
            </div>

            {{-- Request Status Timeline --}}
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3>دبلومة الطلب</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="tl-item">
                            <div class="tl-dot green"></div>
                            <div style="font-size:.82rem;font-weight:700;color:#111827;">إنشاء الحساب</div>
                            <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;">{{ $user->created_at->format('Y/m/d H:i') }}</div>
                        </div>
                        @if($user->nafath_verified_at)
                        <div class="tl-item">
                            <div class="tl-dot blue"></div>
                            <div style="font-size:.82rem;font-weight:700;color:#111827;">التحقق عبر نفاذ</div>
                            <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;">{{ \Carbon\Carbon::parse($user->nafath_verified_at)->format('Y/m/d H:i') }}</div>
                        </div>
                        @endif
                        @if($user->documents && $user->documents->count() > 0)
                        <div class="tl-item">
                            <div class="tl-dot blue"></div>
                            <div style="font-size:.82rem;font-weight:700;color:#111827;">رفع صور الهوية</div>
                            <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;">{{ $user->documents->sortBy('created_at')->last()->created_at->format('Y/m/d H:i') }}</div>
                        </div>
                        @endif
                        <div class="tl-item">
                            <div class="tl-dot amber"></div>
                            <div style="font-size:.82rem;font-weight:700;color:#111827;">طلب التسجيل في البرنامج</div>
                            <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;">{{ $user->updated_at->format('Y/m/d H:i') }}</div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-dot gray"></div>
                            <div style="font-size:.82rem;font-weight:600;color:#9ca3af;">في انتظار موافقة الإدارة...</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Verification Status --}}
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3>حالة التحقق</h3>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:.6rem;">
                    @php
                        $checks = [
                            ['label'=>'البريد الإلكتروني','ok'=>!!$user->email_verified_at],
                            ['label'=>'رقم الجوال','ok'=>!!$user->phone_verified_at],
                            ['label'=>'نفاذ الوطني','ok'=>!!$user->nafath_verified_at],
                            ['label'=>'صور الهوية','ok'=>$user->documents && $user->documents->count() > 0],
                            ['label'=>'الشروط والأحكام','ok'=>!!$user->is_terms],
                        ];
                    @endphp
                    @foreach($checks as $check)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;border-radius:10px;background:{{ $check['ok'] ? '#f0fdf4' : '#fafafa' }};">
                        <span style="font-size:.82rem;font-weight:600;color:#374151;">{{ $check['label'] }}</span>
                        @if($check['ok'])
                            <span style="display:flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:700;color:#16a34a;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                مكتمل
                            </span>
                        @else
                            <span style="display:flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:700;color:#9ca3af;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                غير مكتمل
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- end right col --}}
    </div>

</div>{{-- end max-width --}}

{{-- ── Reject Confirm Modal ── --}}
<div id="reject-confirm-modal" style="display:none;position:fixed;inset:0;z-index:9990;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div onclick="event.stopPropagation()" style="background:white;border-radius:20px;padding:2rem;width:100%;max-width:460px;margin:1rem;box-shadow:0 30px 80px rgba(0,0,0,0.3);">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#ef4444,#dc2626);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg class="w-6 h-6" style="color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 style="font-size:1.1rem;font-weight:800;color:#111827;margin:0;">تأكيد رفض الطلب</h3>
                <p style="font-size:.82rem;color:#6b7280;margin:.25rem 0 0;">سيتم إزالة البرنامج من حساب الطالب</p>
            </div>
        </div>
        <div style="background:#fef2f2;border-radius:12px;padding:.85rem 1rem;margin-bottom:1.25rem;border:1px solid #fecaca;">
            <p style="font-size:.85rem;color:#b91c1c;margin:0;font-weight:500;">
                هل أنت متأكد من رفض طلب تسجيل الطالب <strong>{{ $user->name }}</strong> في برنامج <strong>{{ $user->program->name_ar ?? '' }}</strong>؟
            </p>
        </div>
        <div style="display:flex;gap:.75rem;justify-content:flex-end;">
            <button onclick="closeRejectModal()" style="padding:.65rem 1.4rem;background:#f1f5f9;color:#6b7280;border:none;border-radius:10px;font-size:.875rem;font-weight:700;cursor:pointer;">إلغاء</button>
            <form action="{{ route('admin.program-enrollments.reject', $user) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding:.65rem 1.4rem;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:10px;font-size:.875rem;font-weight:700;cursor:pointer;">تأكيد الرفض</button>
            </form>
        </div>
    </div>
</div>

{{-- ── Doc Lightbox ── --}}
<div id="doc-lb" onclick="closeDocLb()" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.92);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <img id="doc-lb-img" src="" alt="" style="max-width:90vw;max-height:88vh;object-fit:contain;border-radius:16px;box-shadow:0 30px 80px rgba(0,0,0,0.6);">
    <button onclick="closeDocLb()" style="position:fixed;top:1.5rem;left:1.5rem;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);color:white;border-radius:50%;width:44px;height:44px;font-size:1.3rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
</div>

@push('scripts')
<script>
function openRejectModal() {
    document.getElementById('reject-confirm-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeRejectModal() {
    document.getElementById('reject-confirm-modal').style.display = 'none';
    document.body.style.overflow = '';
}
function openDocLb(src, label) {
    document.getElementById('doc-lb-img').src = src;
    document.getElementById('doc-lb').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeDocLb() {
    document.getElementById('doc-lb').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeRejectModal(); closeDocLb(); }
});
</script>
@endpush
@endsection
