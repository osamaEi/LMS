@extends('layouts.dashboard')
@section('title', 'ملفي الشخصي')

@push('styles')
<style>
:root { --brand: #0071AA; --brand-dark: #005a88; }

/* ── Page wrapper ── */
.sp-page { max-width: 960px; margin: 0 auto; padding-bottom: 3rem; }

/* ── Cover card ── */
.sp-cover {
    background: linear-gradient(135deg, #0a2540 0%, #0071AA 55%, #0090d0 100%);
    border-radius: 24px;
    padding: 2.5rem 2.5rem 5rem;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.sp-cover::before {
    content:''; position:absolute; top:-80px; right:-80px;
    width:300px; height:300px; border-radius:50%;
    background: radial-gradient(circle, rgba(255,255,255,.07) 0%, transparent 70%);
}
.sp-cover::after {
    content:''; position:absolute; bottom:-60px; left:-40px;
    width:240px; height:240px; border-radius:50%;
    background: radial-gradient(circle, rgba(255,255,255,.04) 0%, transparent 70%);
}
.sp-cover-inner { position:relative; z-index:1; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.sp-cover-title { font-size:1.6rem; font-weight:800; line-height:1.2; }
.sp-cover-sub   { font-size:.85rem; opacity:.65; margin-top:.3rem; }
.sp-status-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.3rem .9rem; border-radius:20px;
    font-size:.72rem; font-weight:700;
}
.sp-cover-stat  { text-align:center; }
.sp-cover-stat-val { font-size:1.05rem; font-weight:800; }
.sp-cover-stat-lbl { font-size:.65rem; opacity:.55; margin-top:.1rem; }
.sp-cover-divider  { width:1px; background:rgba(255,255,255,.15); align-self:stretch; }

/* ── Avatar floated out of cover ── */
.sp-avatar-wrap {
    position:absolute; bottom:-48px; right:2.5rem; z-index:30;
}
.sp-avatar {
    width:96px; height:96px; border-radius:24px;
    border:4px solid #fff;
    box-shadow:0 8px 28px rgba(0,0,0,.2);
    object-fit:cover; display:block;
    background:#dbeafe;
}
.sp-avatar-btn {
    position:absolute; bottom:-8px; left:-8px;
    width:30px; height:30px; border-radius:9px;
    background:var(--brand); border:2.5px solid #fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:background .2s;
}
.sp-avatar-btn:hover { background:var(--brand-dark); }

/* ── Body offset ── */
.sp-body { margin-top:60px; display:grid; grid-template-columns:1fr 300px; gap:1.25rem; align-items:start; }
@media(max-width:768px){ .sp-body { grid-template-columns:1fr; } }

/* ── Generic card ── */
.sp-card {
    background:#fff;
    border-radius:18px;
    border:1px solid #e5e7eb;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    overflow:hidden;
    margin-bottom:1.25rem;
}
.dark .sp-card { background:#1f2937; border-color:#374151; }
.sp-card:last-child { margin-bottom:0; }

.sp-card-hd {
    display:flex; align-items:center; gap:.7rem;
    padding:.9rem 1.25rem;
    border-bottom:1px solid #f1f5f9;
    background:#fafbfc;
}
.dark .sp-card-hd { background:#111827; border-color:#374151; }
.sp-card-hd-icon {
    width:34px; height:34px; border-radius:9px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.sp-card-hd h4 { font-size:.875rem; font-weight:700; color:#1a2540; margin:0; flex:1; }
.dark .sp-card-hd h4 { color:#f9fafb; }

/* ── Info rows ── */
.sp-info-grid {
    display:grid; grid-template-columns:1fr 1fr; gap:0;
}
@media(max-width:480px){ .sp-info-grid { grid-template-columns:1fr; } }
.sp-info-cell {
    display:flex; align-items:center; gap:.65rem;
    padding:.8rem 1.25rem;
    border-bottom:1px solid #f8fafc;
    border-left:1px solid #f8fafc;
}
.dark .sp-info-cell { border-color:#1f2937; }
.sp-info-cell:nth-child(odd) { border-left:none; }
.sp-info-ico {
    width:32px; height:32px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
}
.sp-info-lbl { font-size:.65rem; color:#9ca3af; font-weight:500; }
.sp-info-val { font-size:.855rem; font-weight:700; color:#111827; margin-top:1px; }
.dark .sp-info-val { color:#f9fafb; }

/* ── Edit field ── */
.sp-field {
    width:100%; padding:.6rem .85rem;
    border:1.5px solid #e5e7eb; border-radius:10px;
    font-size:.855rem; font-family:'Cairo',sans-serif;
    background:#f9fafb; color:#111827;
    outline:none; transition:border-color .2s, box-shadow .2s;
}
.sp-field:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(0,113,170,.1); background:#fff; }
.dark .sp-field { background:#111827; border-color:#374151; color:#f9fafb; }
.sp-lbl { display:block; font-size:.7rem; font-weight:600; color:#374151; margin-bottom:.25rem; }
.dark .sp-lbl { color:#d1d5db; }

/* ── Toast ── */
#sp-toast {
    position:fixed; bottom:1.5rem; left:50%;
    transform:translateX(-50%) translateY(80px);
    padding:.7rem 1.4rem; border-radius:12px;
    font-size:.83rem; font-weight:700; color:#fff;
    z-index:9999; opacity:0;
    transition:transform .3s, opacity .3s;
}
#sp-toast.show { transform:translateX(-50%) translateY(0); opacity:1; }

/* ── Modal ── */
.sp-modal-bg {
    position:fixed; inset:0; z-index:200;
    background:rgba(0,0,0,.5);
    display:none; align-items:center; justify-content:center; padding:1rem;
}
.sp-modal-bg.open { display:flex; }
.sp-modal {
    background:#fff; border-radius:20px;
    box-shadow:0 24px 60px rgba(0,0,0,.2);
    width:100%; max-width:520px; max-height:90vh; overflow-y:auto;
}
.dark .sp-modal { background:#1f2937; }

/* ── Quick link ── */
.sp-qlink {
    display:flex; align-items:center; gap:.75rem;
    padding:.7rem 1rem; border-radius:12px;
    text-decoration:none; transition:background .15s;
}
.sp-qlink:hover { background:#f8fafc; }
.dark .sp-qlink:hover { background:#111827; }
.sp-qlink-icon {
    width:34px; height:34px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
}
</style>
@endpush

@section('content')
@php
$statusColors = [
    'active'    => ['bg'=>'rgba(16,185,129,.18)',  'dot'=>'#10b981','text'=>'#a7f3d0','label'=>'نشط'],
    'pending'   => ['bg'=>'rgba(245,158,11,.18)',   'dot'=>'#f59e0b','text'=>'#fde68a','label'=>'قيد المراجعة'],
    'inactive'  => ['bg'=>'rgba(156,163,175,.18)',  'dot'=>'#9ca3af','text'=>'#d1d5db','label'=>'غير نشط'],
    'suspended' => ['bg'=>'rgba(239,68,68,.18)',    'dot'=>'#ef4444','text'=>'#fca5a5','label'=>'موقوف'],
];
$sc = $statusColors[$user->status] ?? $statusColors['inactive'];
@endphp

<div class="sp-page space-y-0">

{{-- ══ COVER ══ --}}
<div class="sp-cover">
    <div class="sp-cover-inner">
        <div>
            <p style="font-size:.75rem;opacity:.55;margin-bottom:.35rem;">ملفي الشخصي</p>
            <div class="sp-cover-title">{{ $user->name }}</div>
            <div class="sp-cover-sub">
                {{ $user->program?->name_ar ?? 'لم يتم تعيين برنامج بعد' }}
            </div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.6rem;">
            <span class="sp-status-pill" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
                <span style="width:6px;height:6px;border-radius:50%;background:{{ $sc['dot'] }};flex-shrink:0;"></span>
                {{ $sc['label'] }}
            </span>
            <span style="font-size:.7rem;opacity:.5;">منذ {{ $user->date_of_register?->format('Y') ?? $user->created_at->format('Y') }}</span>
        </div>
    </div>

    {{-- Stats strip --}}
    <div style="display:flex;gap:1.5rem;margin-top:1.75rem;flex-wrap:wrap;position:relative;z-index:1;">
        <div class="sp-cover-stat">
            <div class="sp-cover-stat-val">{{ $user->national_id ?? '—' }}</div>
            <div class="sp-cover-stat-lbl">رقم الهوية</div>
        </div>
        <div class="sp-cover-divider"></div>
        <div class="sp-cover-stat">
            <div class="sp-cover-stat-val">{{ $user->gender === 'male' ? 'ذكر' : ($user->gender === 'female' ? 'أنثى' : '—') }}</div>
            <div class="sp-cover-stat-lbl">الجنس</div>
        </div>
        <div class="sp-cover-divider"></div>
        <div class="sp-cover-stat">
            <div class="sp-cover-stat-val">{{ $user->date_of_birth?->format('Y') ?? '—' }}</div>
            <div class="sp-cover-stat-lbl">سنة الميلاد</div>
        </div>
        <div class="sp-cover-divider"></div>
        <div class="sp-cover-stat">
            <div class="sp-cover-stat-val">{{ $user->nationality ?? '—' }}</div>
            <div class="sp-cover-stat-lbl">الجنسية</div>
        </div>
    </div>

    {{-- Floating avatar --}}
    <div class="sp-avatar-wrap">
        <img id="sp-avatar" class="sp-avatar"
             src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0071AA&color=fff&size=200&bold=true&font-size=0.4' }}"
             alt="{{ $user->name }}">
        <label class="sp-avatar-btn" title="تغيير الصورة" onclick="document.getElementById('sp-photo-input').click()">
            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </label>
        <input type="file" id="sp-photo-input" accept="image/*" class="hidden" onchange="spUploadPhoto(this)">
    </div>
</div>

{{-- ══ BODY ══ --}}
<div class="sp-body">

    {{-- ── LEFT COLUMN ── --}}
    <div>

        {{-- Personal Info --}}
        <div class="sp-card">
            <div class="sp-card-hd">
                <div class="sp-card-hd-icon" style="background:#eff6ff;">
                    <svg width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h4>البيانات الشخصية</h4>
                <button onclick="document.getElementById('sp-edit-modal').classList.add('open')"
                        style="display:inline-flex;align-items:center;gap:5px;padding:.3rem .85rem;border-radius:8px;border:none;background:#eff6ff;color:#2563eb;font-size:.75rem;font-weight:700;cursor:pointer;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828 9 16l.172-3z"/></svg>
                    تعديل
                </button>
            </div>
            @php
            $cells = [
                ['bg'=>'#eff6ff','color'=>'#2563eb','icon'=>'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0','lbl'=>'رقم الهوية','val'=>$user->national_id??'—','ltr'=>true],
                ['bg'=>'#fef3c7','color'=>'#d97706','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','lbl'=>'تاريخ الميلاد','val'=>$user->date_of_birth?->format('d/m/Y')??'—'],
                ['bg'=>'#fdf2f8','color'=>'#be185d','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','lbl'=>'الجنس','val'=>$user->gender==='male'?'ذكر':($user->gender==='female'?'أنثى':'—')],
                ['bg'=>'#f0fdf4','color'=>'#16a34a','icon'=>'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9','lbl'=>'الجنسية','val'=>$user->nationality??'—'],
                ['bg'=>'#ecfdf5','color'=>'#059669','icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z','lbl'=>'الجوال','val'=>$user->phone??'—','ltr'=>true],
                ['bg'=>'#f3e8ff','color'=>'#7c3aed','icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','lbl'=>'البريد الإلكتروني','val'=>$user->email??'—','ltr'=>true],
                ['bg'=>'#fff7ed','color'=>'#ea580c','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','lbl'=>'تاريخ التسجيل','val'=>($user->date_of_register??$user->created_at)->format('d/m/Y')],
                ['bg'=>'#f0fdf4','color'=>'#16a34a','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','lbl'=>'البرنامج','val'=>$user->program?->name_ar??'—'],
            ];
            @endphp
            <div class="sp-info-grid">
                @foreach($cells as $c)
                <div class="sp-info-cell">
                    <div class="sp-info-ico" style="background:{{ $c['bg'] }};">
                        <svg width="15" height="15" fill="none" stroke="{{ $c['color'] }}" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <div class="sp-info-lbl">{{ $c['lbl'] }}</div>
                        <div class="sp-info-val" {{ isset($c['ltr']) ? 'dir="ltr"' : '' }} style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $c['val'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Academic Info --}}
        <div class="sp-card">
            <div class="sp-card-hd">
                <div class="sp-card-hd-icon" style="background:#ecfdf5;">
                    <svg width="16" height="16" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <h4>البيانات الأكاديمية</h4>
            </div>
            @php
            $aCells = [
                ['bg'=>'#eff6ff','color'=>'#2563eb','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','lbl'=>'نوع المؤهل التدريبي','val'=>$user->specialization??'—'],
                ['bg'=>'#fef9c3','color'=>'#a16207','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','lbl'=>'المؤهل التعليمي','val'=>$user->specialization_type??'—'],
                ['bg'=>'#fef2f2','color'=>'#dc2626','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','lbl'=>'تاريخ التخرج','val'=>$user->date_of_graduation?->format('d/m/Y')??'—'],
                ['bg'=>'#dcfce7','color'=>'#16a34a','icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','lbl'=>'حالة البرنامج','val'=>match($user->program_status??''){
                    'active'=>'نشط','pending'=>'قيد المراجعة','completed'=>'مكتمل',default=>'—'}],
            ];
            @endphp
            <div class="sp-info-grid">
                @foreach($aCells as $c)
                <div class="sp-info-cell">
                    <div class="sp-info-ico" style="background:{{ $c['bg'] }};">
                        <svg width="15" height="15" fill="none" stroke="{{ $c['color'] }}" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <div class="sp-info-lbl">{{ $c['lbl'] }}</div>
                        <div class="sp-info-val">{{ $c['val'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Documents --}}
        <div class="sp-card">
            <div class="sp-card-hd">
                <div class="sp-card-hd-icon" style="background:#fdf4ff;">
                    <svg width="16" height="16" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                </div>
                <h4>الوثائق المرفوعة</h4>
            </div>
            <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;">
                @foreach(['national_id_front'=>'الهوية (أمامي)','national_id_back'=>'الهوية (خلفي)','certificate'=>'الشهادة'] as $dtype => $dlabel)
                @php
                    $doc = $documents->get($dtype);
                    $dsBg    = $doc ? match($doc->status){ 'approved'=>'#dcfce7','rejected'=>'#fee2e2',default=>'#fef9c3' } : '#f3f4f6';
                    $dsColor = $doc ? match($doc->status){ 'approved'=>'#16a34a','rejected'=>'#dc2626',default=>'#92400e' } : '#6b7280';
                    $dsLabel = $doc ? match($doc->status){ 'approved'=>'مقبول','rejected'=>'مرفوض',default=>'مراجعة' } : 'غير موجود';
                @endphp
                <div style="border:1.5px solid #f1f5f9;border-radius:14px;overflow:hidden;">
                    <div style="height:90px;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        @if($doc && $doc->file_path && in_array(pathinfo($doc->file_path,PATHINFO_EXTENSION),['jpg','jpeg','png']))
                            <img src="{{ asset('storage/'.$doc->file_path) }}" style="width:100%;height:100%;object-fit:cover;cursor:pointer;" onclick="spLightbox('{{ asset('storage/'.$doc->file_path) }}')">
                        @elseif($doc && $doc->file_path)
                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" style="display:flex;flex-direction:column;align-items:center;gap:4px;color:#7c3aed;text-decoration:none;">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span style="font-size:.65rem;font-weight:700;">PDF</span>
                            </a>
                        @else
                            <svg width="28" height="28" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <div style="padding:.5rem .65rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:.3rem;">
                        <span style="font-size:.65rem;font-weight:700;color:#374151;">{{ $dlabel }}</span>
                        <span style="font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:6px;background:{{ $dsBg }};color:{{ $dsColor }};">{{ $dsLabel }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div>

        {{-- Bio --}}
        <div class="sp-card">
            <div class="sp-card-hd">
                <div class="sp-card-hd-icon" style="background:#fff7ed;">
                    <svg width="16" height="16" fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </div>
                <h4>نبذة شخصية</h4>
            </div>
            <div style="padding:1rem 1.25rem;">
                @if($user->bio)
                    <p style="font-size:.855rem;line-height:1.75;color:#4b5563;margin:0 0 .75rem;">{{ $user->bio }}</p>
                @else
                    <p style="font-size:.83rem;color:#9ca3af;text-align:center;padding:.5rem 0 .75rem;">لم تتم إضافة نبذة شخصية</p>
                @endif
                <button onclick="document.getElementById('sp-edit-modal').classList.add('open')"
                        style="width:100%;padding:.55rem;border-radius:10px;border:none;background:#fff7ed;color:#ea580c;font-size:.78rem;font-weight:700;cursor:pointer;">
                    {{ $user->bio ? 'تعديل النبذة' : 'أضف نبذة' }}
                </button>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="sp-card">
            <div class="sp-card-hd">
                <div class="sp-card-hd-icon" style="background:#fef2f2;">
                    <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h4>تغيير كلمة المرور</h4>
            </div>
            <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.75rem;">
                <div>
                    <label class="sp-lbl">كلمة المرور الحالية</label>
                    <input type="password" id="sp-cur-pw" class="sp-field" placeholder="••••••••">
                </div>
                <div>
                    <label class="sp-lbl">كلمة المرور الجديدة</label>
                    <input type="password" id="sp-new-pw" class="sp-field" placeholder="8 أحرف على الأقل">
                </div>
                <div>
                    <label class="sp-lbl">تأكيد كلمة المرور</label>
                    <input type="password" id="sp-new-pw2" class="sp-field" placeholder="أعد الكتابة">
                </div>
                <p id="sp-pw-err" style="font-size:.75rem;color:#ef4444;display:none;"></p>
                <button onclick="spChangePw()" id="sp-pw-btn"
                        style="padding:.65rem;border-radius:10px;border:none;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;font-size:.83rem;font-weight:700;cursor:pointer;">
                    تغيير كلمة المرور
                </button>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="sp-card">
            <div class="sp-card-hd">
                <div class="sp-card-hd-icon" style="background:#f0fdf4;">
                    <svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.1-1.1m-.758-4.9a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <h4>روابط سريعة</h4>
            </div>
            <div style="padding:.5rem .75rem;">
                @foreach([
                    ['href'=>route('student.dashboard'),      'bg'=>'#eff6ff','c'=>'#2563eb','icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','lbl'=>'لوحة التحكم'],
                    ['href'=>route('student.my-program'),     'bg'=>'#ecfdf5','c'=>'#059669','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','lbl'=>'برنامجي التدريبي'],
                    ['href'=>route('student.attendance'),     'bg'=>'#f3e8ff','c'=>'#7c3aed','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','lbl'=>'سجل الحضور'],
                    ['href'=>route('student.payments.index'), 'bg'=>'#fef3c7','c'=>'#d97706','icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','lbl'=>'المدفوعات'],
                    ['href'=>route('student.tickets.index'),  'bg'=>'#fef2f2','c'=>'#dc2626','icon'=>'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z','lbl'=>'الدعم والتذاكر'],
                ] as $lnk)
                <a href="{{ $lnk['href'] }}" class="sp-qlink">
                    <div class="sp-qlink-icon" style="background:{{ $lnk['bg'] }};">
                        <svg width="15" height="15" fill="none" stroke="{{ $lnk['c'] }}" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $lnk['icon'] }}"/>
                        </svg>
                    </div>
                    <span style="font-size:.83rem;font-weight:600;color:#374151;flex:1;" class="dark:text-gray-200">{{ $lnk['lbl'] }}</span>
                    <svg width="14" height="14" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
</div>

{{-- ══ EDIT MODAL ══ --}}
<div id="sp-edit-modal" class="sp-modal-bg" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="sp-modal">
        <div style="display:flex;align-items:center;gap:.75rem;padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;">
            <div style="width:34px;height:34px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828 9 16l.172-3z"/></svg>
            </div>
            <span style="font-size:.95rem;font-weight:700;color:#1a2540;flex:1;">تعديل البيانات</span>
            <button onclick="document.getElementById('sp-edit-modal').classList.remove('open')"
                    style="width:30px;height:30px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div style="padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:.9rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div>
                    <label class="sp-lbl">الاسم الكامل <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="sp-e-name" class="sp-field" value="{{ $user->name }}">
                </div>
                <div>
                    <label class="sp-lbl">البريد الإلكتروني <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="sp-e-email" class="sp-field" value="{{ $user->email }}" dir="ltr">
                </div>
            </div>
            <div>
                <label class="sp-lbl">الجنسية</label>
                <input type="text" id="sp-e-nat" class="sp-field" value="{{ $user->nationality }}" placeholder="مثال: سعودي">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div>
                    <label class="sp-lbl">نوع المؤهل التدريبي</label>
                    <input type="text" id="sp-e-spec" class="sp-field" value="{{ $user->specialization }}">
                </div>
                <div>
                    <label class="sp-lbl">المؤهل التعليمي</label>
                    <select id="sp-e-sptype" class="sp-field">
                        <option value="">اختر</option>
                        @foreach(['diploma'=>'دبلوم','bachelor'=>'بكالوريوس','master'=>'ماجستير','phd'=>'دكتوراه','training'=>'تدريب مهني'] as $v=>$l)
                            <option value="{{ $v }}" {{ $user->specialization_type===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                        @if($user->specialization_type && !in_array($user->specialization_type,['diploma','bachelor','master','phd','training']))
                            <option value="{{ $user->specialization_type }}" selected>{{ $user->specialization_type }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div>
                <label class="sp-lbl">تاريخ التخرج</label>
                <input type="date" id="sp-e-grad" class="sp-field" value="{{ $user->date_of_graduation?->format('Y-m-d') }}">
            </div>
            <div>
                <label class="sp-lbl">نبذة شخصية</label>
                <textarea id="sp-e-bio" class="sp-field" rows="3" placeholder="اكتب نبذة مختصرة عنك...">{{ $user->bio }}</textarea>
            </div>
            <p id="sp-e-err" style="font-size:.75rem;color:#ef4444;display:none;"></p>
        </div>
        <div style="display:flex;gap:.75rem;padding:0 1.5rem 1.25rem;">
            <button onclick="document.getElementById('sp-edit-modal').classList.remove('open')"
                    style="flex:1;padding:.65rem;border-radius:10px;border:none;background:#f1f5f9;color:#374151;font-size:.83rem;font-weight:700;cursor:pointer;">
                إلغاء
            </button>
            <button onclick="spSave()" id="sp-save-btn"
                    style="flex:1;padding:.65rem;border-radius:10px;border:none;background:linear-gradient(135deg,#0071AA,#005588);color:#fff;font-size:.83rem;font-weight:700;cursor:pointer;">
                حفظ التغييرات
            </button>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="sp-lightbox" onclick="this.style.display='none'" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.88);align-items:center;justify-content:center;">
    <img id="sp-lb-img" src="" style="max-width:90vw;max-height:90vh;border-radius:16px;object-fit:contain;">
</div>

<div id="sp-toast"></div>

@push('scripts')
<script>
const _csrf = document.querySelector('meta[name="csrf-token"]').content;

async function spUploadPhoto(input) {
    if (!input.files[0]) return;
    const fd = new FormData(); fd.append('photo', input.files[0]);
    try {
        const r = await fetch('{{ route("student.profile.update-photo") }}',
            { method:'POST', headers:{'X-CSRF-TOKEN':_csrf,'Accept':'application/json'}, body:fd });
        const d = await r.json();
        if (d.success) { document.getElementById('sp-avatar').src = d.photo_url; spToast(d.message,'s'); }
        else spToast(d.message||'حدث خطأ','e');
    } catch { spToast('خطأ في الاتصال','e'); }
}

async function spSave() {
    const err = document.getElementById('sp-e-err');
    err.style.display = 'none';
    const name  = document.getElementById('sp-e-name').value.trim();
    const email = document.getElementById('sp-e-email').value.trim();
    if (!name)  { err.textContent='الاسم مطلوب'; err.style.display='block'; return; }
    if (!email) { err.textContent='البريد مطلوب'; err.style.display='block'; return; }
    const btn = document.getElementById('sp-save-btn');
    btn.disabled = true; btn.textContent = 'جاري الحفظ...';
    try {
        const r = await fetch('{{ route("student.profile.update") }}', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':_csrf,'Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({ _method:'PUT', name, email,
                nationality:         document.getElementById('sp-e-nat').value.trim(),
                specialization:      document.getElementById('sp-e-spec').value.trim(),
                specialization_type: document.getElementById('sp-e-sptype').value,
                date_of_graduation:  document.getElementById('sp-e-grad').value,
                bio:                 document.getElementById('sp-e-bio').value.trim() }),
        });
        const d = await r.json();
        if (d.success) { spToast(d.message,'s'); document.getElementById('sp-edit-modal').classList.remove('open'); setTimeout(()=>location.reload(),900); }
        else { const m = d.errors ? Object.values(d.errors).flat().join(' | ') : (d.message||'حدث خطأ'); err.textContent=m; err.style.display='block'; }
    } catch { err.textContent='خطأ في الاتصال'; err.style.display='block'; }
    finally { btn.disabled=false; btn.textContent='حفظ التغييرات'; }
}

async function spChangePw() {
    const err = document.getElementById('sp-pw-err');
    err.style.display = 'none';
    const cur = document.getElementById('sp-cur-pw').value;
    const np  = document.getElementById('sp-new-pw').value;
    const np2 = document.getElementById('sp-new-pw2').value;
    if (!cur)         { err.textContent='أدخل كلمة المرور الحالية'; err.style.display='block'; return; }
    if (np.length<8)  { err.textContent='يجب أن تكون 8 أحرف على الأقل'; err.style.display='block'; return; }
    if (np !== np2)   { err.textContent='كلمة المرور غير متطابقة'; err.style.display='block'; return; }
    const btn = document.getElementById('sp-pw-btn');
    btn.disabled=true; btn.textContent='جاري التغيير...';
    try {
        const r = await fetch('{{ route("student.profile.update-password") }}', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':_csrf,'Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({ current_password:cur, password:np, password_confirmation:np2 }),
        });
        const d = await r.json();
        if (d.success) { spToast(d.message,'s'); ['sp-cur-pw','sp-new-pw','sp-new-pw2'].forEach(id=>document.getElementById(id).value=''); }
        else { const m = d.errors ? Object.values(d.errors).flat().join(' | ') : (d.message||'حدث خطأ'); err.textContent=m; err.style.display='block'; }
    } catch { err.textContent='خطأ في الاتصال'; err.style.display='block'; }
    finally { btn.disabled=false; btn.textContent='تغيير كلمة المرور'; }
}

function spLightbox(src) {
    document.getElementById('sp-lb-img').src = src;
    const lb = document.getElementById('sp-lightbox');
    lb.style.display = 'flex';
}

function spToast(msg, type) {
    const t = document.getElementById('sp-toast');
    t.textContent = msg;
    t.style.background = type==='s' ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#ef4444,#dc2626)';
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 3000);
}
</script>
@endpush
@endsection
