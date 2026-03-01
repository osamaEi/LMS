@extends('layouts.dashboard')

@section('title', 'ملفي الشخصي')

@push('styles')
<style>
    .prof-page { max-width: 1100px; margin: 0 auto; }

    /* Hero Banner */
    .prof-hero {
        background: linear-gradient(135deg, #0071AA 0%, #004d77 60%, #003356 100%);
        border-radius: 28px;
        padding: 2.5rem 2.5rem 4.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .prof-hero::before {
        content: '';
        position: absolute;
        top: -80px; left: -60px;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
        border-radius: 50%;
    }
    .prof-hero::after {
        content: '';
        position: absolute;
        bottom: -100px; right: -40px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Avatar wrapper - sits half-inside hero, half outside */
    .avatar-wrap {
        position: absolute;
        bottom: -52px;
        right: 2.5rem;
        z-index: 20;
    }
    .avatar-img {
        width: 104px; height: 104px;
        border-radius: 28px;
        border: 4px solid #fff;
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        object-fit: cover;
        background: #e0f2fe;
        display: block;
    }
    .avatar-edit-btn {
        position: absolute;
        bottom: -8px; left: -8px;
        width: 32px; height: 32px;
        background: #0071AA;
        border-radius: 10px;
        border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background .2s;
    }
    .avatar-edit-btn:hover { background: #005f8e; }

    /* Offset card (compensates for avatar overflow) */
    .prof-card-offset { margin-top: 64px; }

    /* Section card */
    .p-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .p-card { background: #1f2937; }
    .p-card-head {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: .75rem;
    }
    .dark .p-card-head { border-color: #374151; }
    .p-card-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* Info row */
    .info-row {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
    }
    .dark .info-row { border-color: #111827; }
    .info-row:last-child { border-bottom: none; }
    .info-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 2px;
    }
    .info-lbl { font-size: .7rem; color: #9ca3af; font-weight: 500; }
    .info-val { font-size: .9rem; font-weight: 700; color: #111827; margin-top: 2px; }
    .dark .info-val { color: #f9fafb; }

    /* Status badge */
    .status-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .3rem .85rem;
        border-radius: 8px;
        font-size: .75rem;
        font-weight: 700;
    }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; }

    /* Document card */
    .doc-card {
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        overflow: hidden;
        transition: border-color .2s, box-shadow .2s;
    }
    .dark .doc-card { border-color: #374151; }
    .doc-card:hover { border-color: #0071AA; box-shadow: 0 4px 12px rgba(0,113,170,.1); }
    .doc-preview {
        height: 130px;
        background: #f8fafc;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .dark .doc-preview { background: #111827; }
    .doc-preview img { width: 100%; height: 100%; object-fit: cover; }
    .doc-footer {
        padding: .65rem .85rem;
        border-top: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .dark .doc-footer { border-color: #374151; }

    /* Edit field */
    .edit-field {
        width: 100%;
        padding: .625rem .875rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: .875rem;
        font-family: 'Cairo', sans-serif;
        background: #f9fafb;
        color: #111827;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .dark .edit-field { background: #111827; border-color: #374151; color: #f9fafb; }
    .edit-field:focus { border-color: #0071AA; box-shadow: 0 0 0 3px rgba(0,113,170,.1); background: #fff; }
    .edit-field.error { border-color: #ef4444; }
    .edit-lbl { display: block; font-size: .72rem; font-weight: 600; color: #374151; margin-bottom: .3rem; }
    .dark .edit-lbl { color: #d1d5db; }

    /* Toast */
    #toast {
        position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(80px);
        padding: .75rem 1.5rem;
        border-radius: 14px;
        font-size: .85rem; font-weight: 700;
        color: #fff; z-index: 9999;
        transition: transform .3s ease, opacity .3s ease;
        opacity: 0;
    }
    #toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

    /* Progress ring */
    .ring-wrap { position: relative; width: 64px; height: 64px; }
    .ring-val { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 800; }
</style>
@endpush

@section('content')
<div class="prof-page space-y-6">

    {{-- ========== HERO ========== --}}
    <div class="prof-hero relative">
        <div class="relative z-10">
            {{-- Top row: name + status --}}
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-sm opacity-60 mb-1">ملفي الشخصي</p>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ $user->name }}</h1>
                    <p class="text-sm opacity-55 mt-0.5">
                        @if($user->program)
                            {{ $user->program->name }}
                        @else
                            لم يتم تعيين برنامج بعد
                        @endif
                    </p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    @php
                        $statusColors = [
                            'active'    => ['bg' => 'rgba(16,185,129,.15)', 'dot' => '#10b981', 'text' => '#a7f3d0'],
                            'pending'   => ['bg' => 'rgba(245,158,11,.15)', 'dot' => '#f59e0b', 'text' => '#fde68a'],
                            'inactive'  => ['bg' => 'rgba(156,163,175,.15)', 'dot' => '#9ca3af', 'text' => '#d1d5db'],
                            'suspended' => ['bg' => 'rgba(239,68,68,.15)',   'dot' => '#ef4444', 'text' => '#fca5a5'],
                        ];
                        $sc = $statusColors[$user->status] ?? $statusColors['inactive'];
                    @endphp
                    <span class="status-badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }};">
                        <span class="status-dot" style="background: {{ $sc['dot'] }};"></span>
                        {{ $user->getStatusDisplayName() }}
                    </span>
                    <span class="text-xs opacity-50">عضو منذ {{ $user->date_of_register?->format('d/m/Y') ?? $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Quick stats row --}}
            <div class="flex gap-5 mt-6 flex-wrap">
                <div class="text-center">
                    <div class="text-xl font-extrabold">{{ $user->national_id ?? '—' }}</div>
                    <div class="text-xs opacity-55 mt-0.5">رقم الهوية</div>
                </div>
                <div class="w-px bg-white/15 self-stretch"></div>
                <div class="text-center">
                    <div class="text-xl font-extrabold">{{ $user->gender === 'male' ? 'ذكر' : ($user->gender === 'female' ? 'أنثى' : '—') }}</div>
                    <div class="text-xs opacity-55 mt-0.5">الجنس</div>
                </div>
                <div class="w-px bg-white/15 self-stretch"></div>
                <div class="text-center">
                    <div class="text-xl font-extrabold">{{ $user->date_of_birth?->format('Y') ?? '—' }}</div>
                    <div class="text-xs opacity-55 mt-0.5">سنة الميلاد</div>
                </div>
                <div class="w-px bg-white/15 self-stretch"></div>
                <div class="text-center">
                    <div class="text-xl font-extrabold">{{ $user->specialization_type ?? '—' }}</div>
                    <div class="text-xs opacity-55 mt-0.5">المؤهل</div>
                </div>
            </div>
        </div>

        {{-- Floating Avatar --}}
        <div class="avatar-wrap">
            <img id="avatar-img"
                 src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0071AA&color=fff&size=208&bold=true&font-size=0.4' }}"
                 alt="{{ $user->name }}"
                 class="avatar-img">
            <label class="avatar-edit-btn" title="تغيير الصورة" onclick="document.getElementById('photo-input').click()">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </label>
            <input type="file" id="photo-input" accept="image/jpg,image/jpeg,image/png" class="hidden" onchange="uploadPhoto(this)">
        </div>
    </div>

    {{-- ========== MAIN GRID ========== --}}
    <div class="prof-card-offset grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COL: Personal + Academic --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Personal Info --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon" style="background: #eff6ff;">
                        <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">البيانات الشخصية</span>
                    <button onclick="openEditModal()" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition" style="background:#eff6ff; color:#2563eb;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </button>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800">
                    @php
                        $infoItems = [
                            ['icon_bg'=>'#eff6ff','icon_color'=>'#2563eb','icon'=>'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0', 'label'=>'رقم الهوية الوطنية', 'value'=> $user->national_id ?? '—', 'dir'=>'ltr'],
                            ['icon_bg'=>'#fef3c7','icon_color'=>'#d97706','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label'=>'تاريخ الميلاد', 'value'=> $user->date_of_birth?->format('d / m / Y') ?? '—'],
                            ['icon_bg'=>'#fdf2f8','icon_color'=>'#be185d','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label'=>'الجنس', 'value'=> $user->gender === 'male' ? 'ذكر' : ($user->gender === 'female' ? 'أنثى' : '—')],
                            ['icon_bg'=>'#ecfdf5','icon_color'=>'#059669','icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label'=>'رقم الجوال', 'value'=> $user->phone ?? '—', 'dir'=>'ltr'],
                            ['icon_bg'=>'#f3e8ff','icon_color'=>'#7c3aed','icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label'=>'البريد الإلكتروني', 'value'=> $user->email ?? '—', 'dir'=>'ltr'],
                            ['icon_bg'=>'#fff7ed','icon_color'=>'#ea580c','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label'=>'تاريخ التسجيل', 'value'=> $user->date_of_register?->format('d / m / Y') ?? $user->created_at->format('d / m / Y')],
                        ];
                    @endphp
                    @foreach($infoItems as $item)
                    <div class="info-row">
                        <div class="info-icon" style="background: {{ $item['icon_bg'] }};">
                            <svg class="w-4 h-4" style="color:{{ $item['icon_color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="info-lbl">{{ $item['label'] }}</div>
                            <div class="info-val" {{ isset($item['dir']) ? 'dir="'.$item['dir'].'"' : '' }}>{{ $item['value'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Academic Info --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon" style="background: #ecfdf5;">
                        <svg class="w-4 h-4" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">البيانات الأكاديمية</span>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800">
                    <div class="info-row">
                        <div class="info-icon" style="background:#eff6ff;">
                            <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div class="flex-1">
                            <div class="info-lbl">التخصص الدراسي</div>
                            <div class="info-val">{{ $user->specialization ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon" style="background:#fef9c3;">
                            <svg class="w-4 h-4" style="color:#a16207;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <div class="info-lbl">نوع التخصص / المؤهل</div>
                            <div class="info-val">{{ $user->specialization_type ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon" style="background:#fef2f2;">
                            <svg class="w-4 h-4" style="color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <div class="info-lbl">تاريخ التخرج</div>
                            <div class="info-val">{{ $user->date_of_graduation?->format('d / m / Y') ?? '—' }}</div>
                        </div>
                    </div>
                    @if($user->program)
                    <div class="info-row">
                        <div class="info-icon" style="background:#f0fdf4;">
                            <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div class="flex-1">
                            <div class="info-lbl">البرنامج الدراسي الحالي</div>
                            <div class="info-val">{{ $user->program->name }}</div>
                        </div>
                        @php
                            $ps = $user->program_status;
                            $psLabel = match($ps) { 'active'=>'نشط','pending'=>'قيد المراجعة','completed'=>'مكتمل', default=>'—' };
                            $psBg = match($ps) { 'active'=>'#dcfce7','pending'=>'#fef9c3', default=>'#f3f4f6' };
                            $psColor = match($ps) { 'active'=>'#16a34a','pending'=>'#92400e', default=>'#6b7280' };
                        @endphp
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg flex-shrink-0" style="background:{{ $psBg }};color:{{ $psColor }};">{{ $psLabel }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- National ID Documents --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon" style="background:#fdf4ff;">
                        <svg class="w-4 h-4" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">صور الهوية الوطنية</span>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['national_id_front' => 'الوجه الأمامي', 'national_id_back' => 'الوجه الخلفي'] as $type => $label)
                        @php $doc = $documents->get($type); @endphp
                        <div class="doc-card">
                            <div class="doc-preview">
                                @if($doc && $doc->file_path)
                                    @if(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['jpg','jpeg','png']))
                                        <img src="{{ asset('storage/'.$doc->file_path) }}" alt="{{ $label }}" class="w-full h-full object-cover cursor-pointer"
                                             onclick="openLightbox('{{ asset('storage/'.$doc->file_path) }}')">
                                    @else
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span class="text-xs font-semibold" style="color:#7c3aed;">PDF</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex flex-col items-center gap-2 text-gray-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-xs">لم يُرفع بعد</span>
                                    </div>
                                @endif
                            </div>
                            <div class="doc-footer">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $label }}</div>
                                    @if($doc)
                                        <div class="text-[0.65rem] text-gray-400 mt-0.5">{{ $doc->original_name ?? '' }}</div>
                                    @endif
                                </div>
                                @if($doc)
                                    @php
                                        $dsBg = match($doc->status) { 'approved'=>'#dcfce7','rejected'=>'#fee2e2', default=>'#fef9c3' };
                                        $dsColor = match($doc->status) { 'approved'=>'#16a34a','rejected'=>'#dc2626', default=>'#92400e' };
                                        $dsLabel = match($doc->status) { 'approved'=>'مقبول','rejected'=>'مرفوض', default=>'قيد المراجعة' };
                                    @endphp
                                    <span class="px-2 py-0.5 text-[0.65rem] font-bold rounded-lg" style="background:{{ $dsBg }};color:{{ $dsColor }};">{{ $dsLabel }}</span>
                                @else
                                    <span class="px-2 py-0.5 text-[0.65rem] font-bold rounded-lg" style="background:#f3f4f6;color:#6b7280;">غير موجود</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- RIGHT COL: Bio + Change Password + Quick Links --}}
        <div class="space-y-6">

            {{-- Bio --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon" style="background:#fff7ed;">
                        <svg class="w-4 h-4" style="color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">نبذة شخصية</span>
                </div>
                <div class="p-4">
                    @if($user->bio)
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $user->bio }}</p>
                    @else
                        <p class="text-sm text-gray-400 text-center py-3">لم تتم إضافة نبذة شخصية</p>
                    @endif
                    <button onclick="openEditModal()" class="mt-3 w-full py-2 text-xs font-bold rounded-xl transition"
                            style="background:#fff7ed; color:#ea580c;">
                        {{ $user->bio ? 'تعديل النبذة' : 'أضف نبذة شخصية' }}
                    </button>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon" style="background:#fef2f2;">
                        <svg class="w-4 h-4" style="color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">تغيير كلمة المرور</span>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="edit-lbl">كلمة المرور الحالية</label>
                        <input type="password" id="cur-pw" class="edit-field" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="edit-lbl">كلمة المرور الجديدة</label>
                        <input type="password" id="new-pw" class="edit-field" placeholder="8 أحرف على الأقل">
                    </div>
                    <div>
                        <label class="edit-lbl">تأكيد كلمة المرور</label>
                        <input type="password" id="new-pw-confirm" class="edit-field" placeholder="أعد كتابة كلمة المرور">
                    </div>
                    <p id="pw-error" class="text-xs hidden" style="color:#ef4444;"></p>
                    <button onclick="changePassword()" id="pw-btn"
                            class="w-full py-2.5 text-sm font-bold text-white rounded-xl transition"
                            style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        تغيير كلمة المرور
                    </button>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon" style="background:#f0fdf4;">
                        <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">روابط سريعة</span>
                </div>
                <div class="p-3 space-y-1.5">
                    @php
                        $links = [
                            ['href'=>route('student.dashboard'),   'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label'=>'لوحة التحكم', 'color'=>'#2563eb', 'bg'=>'#eff6ff'],
                            ['href'=>route('student.my-program'),  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label'=>'برنامجي الدراسي', 'color'=>'#059669', 'bg'=>'#ecfdf5'],
                            ['href'=>route('student.attendance'),  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label'=>'سجل الحضور', 'color'=>'#7c3aed', 'bg'=>'#f3e8ff'],
                            ['href'=>route('student.payments.index'), 'icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label'=>'المدفوعات', 'color'=>'#d97706', 'bg'=>'#fef3c7'],
                            ['href'=>route('student.tickets.index'),'icon'=>'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'label'=>'الدعم والتذاكر', 'color'=>'#dc2626', 'bg'=>'#fef2f2'],
                        ];
                    @endphp
                    @foreach($links as $lnk)
                    <a href="{{ $lnk['href'] }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:opacity-80 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $lnk['bg'] }};">
                            <svg class="w-4 h-4" style="color:{{ $lnk['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $lnk['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $lnk['label'] }}</span>
                        <svg class="w-4 h-4 text-gray-300 mr-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ========== EDIT MODAL ========== --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden" style="background: rgba(0,0,0,0.5);">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg" style="max-height:90vh; overflow-y:auto;">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#eff6ff;">
                    <svg class="w-5 h-5" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex-1">تعديل البيانات</h3>
                <button onclick="closeEditModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="edit-lbl">الاسم الكامل <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="edit-name" class="edit-field" value="{{ $user->name }}">
                </div>
                <div>
                    <label class="edit-lbl">البريد الإلكتروني <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="edit-email" class="edit-field" value="{{ $user->email }}" dir="ltr">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="edit-lbl">التخصص</label>
                        <input type="text" id="edit-spec" class="edit-field" value="{{ $user->specialization }}">
                    </div>
                    <div>
                        <label class="edit-lbl">نوع التخصص</label>
                        <select id="edit-spec-type" class="edit-field">
                            <option value="">اختر</option>
                            @foreach(['diploma'=>'دبلوم','bachelor'=>'بكالوريوس','master'=>'ماجستير','phd'=>'دكتوراه','training'=>'تدريب مهني'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $user->specialization_type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                            {{-- allow custom value if not in list --}}
                            @if($user->specialization_type && !in_array($user->specialization_type, ['diploma','bachelor','master','phd','training']))
                                <option value="{{ $user->specialization_type }}" selected>{{ $user->specialization_type }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div>
                    <label class="edit-lbl">تاريخ التخرج</label>
                    <input type="date" id="edit-grad" class="edit-field" value="{{ $user->date_of_graduation?->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="edit-lbl">نبذة شخصية</label>
                    <textarea id="edit-bio" class="edit-field" rows="3" placeholder="اكتب نبذة مختصرة عنك...">{{ $user->bio }}</textarea>
                </div>
                <p id="edit-error" class="text-xs hidden" style="color:#ef4444;"></p>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button onclick="closeEditModal()" class="flex-1 py-2.5 text-sm font-bold rounded-xl" style="background:#f1f5f9; color:#374151;">إلغاء</button>
                <button onclick="saveProfile()" id="save-btn" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background: linear-gradient(135deg, #0071AA, #005588);">
                    <span id="save-btn-text">حفظ التغييرات</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========== LIGHTBOX ========== --}}
<div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center" style="background:rgba(0,0,0,.85);" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="preview" class="max-w-[90vw] max-h-[90vh] rounded-2xl object-contain shadow-2xl">
</div>

{{-- Toast --}}
<div id="toast"></div>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ===== Photo Upload =====
    async function uploadPhoto(input) {
        if (!input.files[0]) return;
        const fd = new FormData();
        fd.append('photo', input.files[0]);
        fd.append('_method', 'POST');
        try {
            const res = await fetch('{{ route("student.profile.update-photo") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd,
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('avatar-img').src = data.photo_url;
                showToast('تم تحديث الصورة الشخصية بنجاح', 'success');
            } else {
                showToast(data.message || 'حدث خطأ', 'error');
            }
        } catch { showToast('حدث خطأ في الاتصال', 'error'); }
    }

    // ===== Edit Modal =====
    function openEditModal() {
        document.getElementById('edit-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function saveProfile() {
        const errEl = document.getElementById('edit-error');
        errEl.classList.add('hidden');

        const name  = document.getElementById('edit-name').value.trim();
        const email = document.getElementById('edit-email').value.trim();
        if (!name)  { showFieldErr(errEl, 'الاسم مطلوب'); return; }
        if (!email) { showFieldErr(errEl, 'البريد الإلكتروني مطلوب'); return; }

        const btn = document.getElementById('save-btn');
        btn.disabled = true;
        document.getElementById('save-btn-text').textContent = 'جاري الحفظ...';

        try {
            const res = await fetch('{{ route("student.profile.update") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    _method: 'PUT',
                    name,
                    email,
                    specialization:      document.getElementById('edit-spec').value.trim(),
                    specialization_type: document.getElementById('edit-spec-type').value,
                    date_of_graduation:  document.getElementById('edit-grad').value,
                    bio:                 document.getElementById('edit-bio').value.trim(),
                }),
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message, 'success');
                closeEditModal();
                setTimeout(() => location.reload(), 900);
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'حدث خطأ');
                showFieldErr(errEl, msg);
            }
        } catch { showFieldErr(errEl, 'حدث خطأ في الاتصال'); }
        finally { btn.disabled = false; document.getElementById('save-btn-text').textContent = 'حفظ التغييرات'; }
    }

    // ===== Change Password =====
    async function changePassword() {
        const errEl = document.getElementById('pw-error');
        errEl.classList.add('hidden');

        const cur  = document.getElementById('cur-pw').value;
        const np   = document.getElementById('new-pw').value;
        const npc  = document.getElementById('new-pw-confirm').value;

        if (!cur) { showFieldErr(errEl, 'أدخل كلمة المرور الحالية'); return; }
        if (np.length < 8) { showFieldErr(errEl, 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل'); return; }
        if (np !== npc)    { showFieldErr(errEl, 'تأكيد كلمة المرور غير متطابق'); return; }

        const btn = document.getElementById('pw-btn');
        btn.disabled = true; btn.textContent = 'جاري التغيير...';

        try {
            const res = await fetch('{{ route("student.profile.update-password") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ current_password: cur, password: np, password_confirmation: npc }),
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById('cur-pw').value = '';
                document.getElementById('new-pw').value = '';
                document.getElementById('new-pw-confirm').value = '';
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'حدث خطأ');
                showFieldErr(errEl, msg);
            }
        } catch { showFieldErr(errEl, 'حدث خطأ في الاتصال'); }
        finally { btn.disabled = false; btn.textContent = 'تغيير كلمة المرور'; }
    }

    // ===== Lightbox =====
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        const lb = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }
    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
    }

    // ===== Helpers =====
    function showFieldErr(el, msg) { el.textContent = msg; el.classList.remove('hidden'); }

    function showToast(msg, type) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.style.background = type === 'success' ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#ef4444,#dc2626)';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }
</script>
@endpush
@endsection
