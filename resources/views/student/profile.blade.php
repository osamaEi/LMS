@extends('layouts.dashboard')
@section('title', 'ملفي الشخصي')

@push('styles')
<style>
.sp { max-width: 860px; margin: 0 auto; padding-bottom: 2rem; font-family: 'Cairo', sans-serif; }

/* Cover */
.sp-cover {
    background: linear-gradient(135deg, #0a2540, #0071AA);
    border-radius: 20px;
    padding: 2rem 2rem 4.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.sp-cover::after {
    content: '';
    position: absolute; bottom: -60px; left: -40px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,.05);
    pointer-events: none;
}
.sp-avatar-wrap {
    position: absolute;
    bottom: -44px; right: 2rem;
    z-index: 10;
}
.sp-avatar {
    width: 88px; height: 88px;
    border-radius: 20px;
    border: 3px solid #fff;
    box-shadow: 0 6px 20px rgba(0,0,0,.2);
    object-fit: cover;
    display: block;
}
.sp-cam {
    position: absolute; bottom: -7px; left: -7px;
    width: 26px; height: 26px; border-radius: 8px;
    background: #0071AA; border: 2px solid #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
}

/* Body grid */
.sp-grid {
    margin-top: 56px;
    display: grid;
    grid-template-columns: 1fr 268px;
    gap: 1rem;
    align-items: start;
}
@media(max-width:700px) { .sp-grid { grid-template-columns: 1fr; } }

/* Card */
.sp-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1rem;
}
.dark .sp-card { background: #1f2937; border-color: #374151; }
.sp-card:last-child { margin-bottom: 0; }

.sp-ch {
    display: flex; align-items: center; gap: .6rem;
    padding: .8rem 1.1rem;
    border-bottom: 1px solid #f1f5f9;
}
.dark .sp-ch { border-color: #374151; }
.sp-ch-ico {
    width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.sp-ch h4 { font-size: .82rem; font-weight: 700; color: #1a2540; margin: 0; flex: 1; }
.dark .sp-ch h4 { color: #f3f4f6; }

/* Info rows */
.sp-row {
    display: flex; align-items: center; gap: .65rem;
    padding: .7rem 1.1rem;
    border-bottom: 1px solid #f8fafc;
}
.dark .sp-row { border-color: #111827; }
.sp-row:last-child { border-bottom: none; }
.sp-row-ico {
    width: 28px; height: 28px; border-radius: 7px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.sp-lbl { font-size: .63rem; color: #9ca3af; font-weight: 500; }
.sp-val { font-size: .82rem; font-weight: 700; color: #111827; margin-top: 1px; }
.dark .sp-val { color: #f9fafb; }

/* Field */
.sp-f {
    width: 100%; padding: .55rem .8rem;
    border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: .82rem; font-family: 'Cairo', sans-serif;
    background: #f9fafb; color: #111827; outline: none;
    transition: border-color .2s;
}
.sp-f:focus { border-color: #0071AA; background: #fff; }
.dark .sp-f { background: #111827; border-color: #374151; color: #f9fafb; }
.sp-fl { display: block; font-size: .67rem; font-weight: 600; color: #6b7280; margin-bottom: .2rem; }

/* Modal */
.sp-mb {
    position: fixed; inset: 0; z-index: 200;
    background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center; padding: 1rem;
}
.sp-mb.open { display: flex; }
.sp-m {
    background: #fff; border-radius: 18px;
    width: 100%; max-width: 480px; max-height: 92vh; overflow-y: auto;
    box-shadow: 0 20px 50px rgba(0,0,0,.2);
}
.dark .sp-m { background: #1f2937; }

/* Toast */
#spt {
    position: fixed; bottom: 1.2rem; left: 50%;
    transform: translateX(-50%) translateY(60px);
    padding: .6rem 1.25rem; border-radius: 10px;
    font-size: .8rem; font-weight: 700; color: #fff;
    z-index: 9999; opacity: 0;
    transition: transform .28s, opacity .28s;
}
#spt.show { transform: translateX(-50%) translateY(0); opacity: 1; }
</style>
@endpush

@section('content')
@php
$sc = [
    'active'    => ['bg'=>'rgba(16,185,129,.18)','dot'=>'#10b981','text'=>'#6ee7b7','lbl'=>'نشط'],
    'pending'   => ['bg'=>'rgba(245,158,11,.18)', 'dot'=>'#f59e0b','text'=>'#fde68a','lbl'=>'قيد المراجعة'],
    'inactive'  => ['bg'=>'rgba(156,163,175,.18)','dot'=>'#9ca3af','text'=>'#e5e7eb','lbl'=>'غير نشط'],
    'suspended' => ['bg'=>'rgba(239,68,68,.18)',  'dot'=>'#ef4444','text'=>'#fca5a5','lbl'=>'موقوف'],
][$user->status] ?? ['bg'=>'rgba(156,163,175,.18)','dot'=>'#9ca3af','text'=>'#e5e7eb','lbl'=>'—'];
@endphp

<div class="sp">

{{-- Cover --}}
<div class="sp-cover">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;position:relative;z-index:1;">
        <div>
            <p style="font-size:.72rem;opacity:.5;margin:0 0 .25rem;">ملفي الشخصي</p>
            <h1 style="font-size:1.45rem;font-weight:800;margin:0;line-height:1.25;">{{ $user->name }}</h1>
            <p style="font-size:.78rem;opacity:.55;margin:.2rem 0 0;">{{ $user->program?->name_ar ?? 'لا يوجد برنامج' }}</p>
        </div>
        <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.28rem .8rem;border-radius:20px;font-size:.7rem;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
            <span style="width:6px;height:6px;border-radius:50%;background:{{ $sc['dot'] }};"></span>
            {{ $sc['lbl'] }}
        </span>
    </div>

    <div style="display:flex;gap:1.25rem;margin-top:1.5rem;flex-wrap:wrap;position:relative;z-index:1;">
        @foreach([
            [$user->national_id??'—','رقم الهوية'],
            [$user->gender==='male'?'ذكر':($user->gender==='female'?'أنثى':'—'),'الجنس'],
            [$user->date_of_birth?->format('Y')??'—','الميلاد'],
            [$user->nationality??'—','الجنسية'],
        ] as [$v,$l])
        <div style="text-align:center;">
            <div style="font-size:.95rem;font-weight:800;">{{ $v }}</div>
            <div style="font-size:.6rem;opacity:.5;margin-top:.1rem;">{{ $l }}</div>
        </div>
        @endforeach
    </div>

    <div class="sp-avatar-wrap">
        <img id="sp-av" class="sp-avatar"
             src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0071AA&color=fff&size=180&bold=true&font-size=0.4' }}"
             alt="{{ $user->name }}">
        <label class="sp-cam" onclick="document.getElementById('sp-fi').click()">
            <svg width="12" height="12" fill="none" stroke="white" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </label>
        <input type="file" id="sp-fi" accept="image/*" class="hidden" onchange="spPhoto(this)">
    </div>
</div>

{{-- Grid --}}
<div class="sp-grid">

    {{-- Left --}}
    <div>

        {{-- Personal --}}
        <div class="sp-card">
            <div class="sp-ch">
                <div class="sp-ch-ico" style="background:#eff6ff;"><svg width="14" height="14" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                <h4>البيانات الشخصية</h4>
                <button onclick="document.getElementById('sp-em').classList.add('open')"
                        style="padding:.25rem .75rem;border-radius:7px;border:none;background:#eff6ff;color:#2563eb;font-size:.72rem;font-weight:700;cursor:pointer;">تعديل</button>
            </div>
            @foreach([
                ['#eff6ff','#2563eb','M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0','رقم الهوية',$user->national_id??'—',true],
                ['#fef3c7','#d97706','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','تاريخ الميلاد',$user->date_of_birth?->format('d/m/Y')??'—'],
                ['#fdf2f8','#be185d','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','الجنس',$user->gender==='male'?'ذكر':($user->gender==='female'?'أنثى':'—')],
                ['#f0fdf4','#16a34a','M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9','الجنسية',$user->nationality??'—'],
                ['#ecfdf5','#059669','M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z','الجوال',$user->phone??'—',true],
                ['#f3e8ff','#7c3aed','M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','البريد',$user->email??'—',true],
            ] as $row)
            @php [$bg,$clr,$ico,$lbl,$val] = $row; $ltr = $row[5] ?? false; @endphp
            <div class="sp-row">
                <div class="sp-row-ico" style="background:{{ $bg }};"><svg width="13" height="13" fill="none" stroke="{{ $clr }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/></svg></div>
                <div style="min-width:0;">
                    <div class="sp-lbl">{{ $lbl }}</div>
                    <div class="sp-val" {{ $ltr ? 'dir="ltr"' : '' }}>{{ $val }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Academic --}}
        <div class="sp-card">
            <div class="sp-ch">
                <div class="sp-ch-ico" style="background:#ecfdf5;"><svg width="14" height="14" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg></div>
                <h4>البيانات الأكاديمية</h4>
            </div>
            @foreach([
                ['#eff6ff','#2563eb','M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','نوع المؤهل',$user->specialization??'—'],
                ['#fef9c3','#a16207','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','المؤهل التعليمي',$user->specialization_type??'—'],
                ['#fef2f2','#dc2626','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','تاريخ التخرج',$user->date_of_graduation?->format('d/m/Y')??'—'],
                ['#f0fdf4','#16a34a','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','البرنامج الحالي',$user->program?->name_ar??'—'],
            ] as [$bg,$clr,$ico,$lbl,$val])
            <div class="sp-row">
                <div class="sp-row-ico" style="background:{{ $bg }};"><svg width="13" height="13" fill="none" stroke="{{ $clr }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/></svg></div>
                <div>
                    <div class="sp-lbl">{{ $lbl }}</div>
                    <div class="sp-val">{{ $val }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Documents --}}
        <div class="sp-card">
            <div class="sp-ch">
                <div class="sp-ch-ico" style="background:#fdf4ff;"><svg width="14" height="14" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg></div>
                <h4>الوثائق</h4>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;padding:.9rem 1.1rem;">
                @foreach(['national_id_front'=>'هوية (أمامي)','national_id_back'=>'هوية (خلفي)','certificate'=>'الشهادة'] as $dt=>$dl)
                @php
                    $doc=$documents->get($dt);
                    $dBg=$doc?match($doc->status){'approved'=>'#dcfce7','rejected'=>'#fee2e2',default=>'#fef9c3'}:'#f3f4f6';
                    $dC=$doc?match($doc->status){'approved'=>'#16a34a','rejected'=>'#dc2626',default=>'#92400e'}:'#6b7280';
                    $dL=$doc?match($doc->status){'approved'=>'مقبول','rejected'=>'مرفوض',default=>'مراجعة'}:'غير موجود';
                @endphp
                <div style="border:1.5px solid #f1f5f9;border-radius:12px;overflow:hidden;">
                    <div style="height:72px;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        @if($doc&&$doc->file_path&&in_array(pathinfo($doc->file_path,PATHINFO_EXTENSION),['jpg','jpeg','png']))
                            <img src="{{ asset('storage/'.$doc->file_path) }}" style="width:100%;height:100%;object-fit:cover;cursor:pointer;" onclick="document.getElementById('sp-lb-i').src=this.src;document.getElementById('sp-lb').style.display='flex'">
                        @elseif($doc&&$doc->file_path)
                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" style="color:#7c3aed;font-size:.65rem;font-weight:700;text-decoration:none;">PDF</a>
                        @else
                            <svg width="22" height="22" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <div style="padding:.35rem .55rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:.25rem;">
                        <span style="font-size:.6rem;font-weight:700;color:#374151;">{{ $dl }}</span>
                        <span style="font-size:.58rem;font-weight:700;padding:1px 6px;border-radius:5px;background:{{ $dBg }};color:{{ $dC }};">{{ $dL }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Right --}}
    <div>

        {{-- Bio --}}
        <div class="sp-card">
            <div class="sp-ch">
                <div class="sp-ch-ico" style="background:#fff7ed;"><svg width="14" height="14" fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg></div>
                <h4>نبذة شخصية</h4>
            </div>
            <div style="padding:.85rem 1.1rem;">
                <p style="font-size:.8rem;line-height:1.7;color:{{ $user->bio?'#4b5563':'#9ca3af' }};margin:0 0 .65rem;text-align:{{ $user->bio?'right':'center' }};">{{ $user->bio ?? 'لا توجد نبذة' }}</p>
                <button onclick="document.getElementById('sp-em').classList.add('open')"
                        style="width:100%;padding:.5rem;border-radius:8px;border:none;background:#fff7ed;color:#ea580c;font-size:.75rem;font-weight:700;cursor:pointer;">
                    {{ $user->bio?'تعديل':'إضافة نبذة' }}
                </button>
            </div>
        </div>

        {{-- Password --}}
        <div class="sp-card">
            <div class="sp-ch">
                <div class="sp-ch-ico" style="background:#fef2f2;"><svg width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                <h4>تغيير كلمة المرور</h4>
            </div>
            <div style="padding:.85rem 1.1rem;display:flex;flex-direction:column;gap:.6rem;">
                <div><label class="sp-fl">الحالية</label><input type="password" id="sp-cp" class="sp-f" placeholder="••••••••"></div>
                <div><label class="sp-fl">الجديدة</label><input type="password" id="sp-np" class="sp-f" placeholder="8 أحرف+"></div>
                <div><label class="sp-fl">تأكيد</label><input type="password" id="sp-np2" class="sp-f" placeholder="أعد الكتابة"></div>
                <p id="sp-pe" style="font-size:.72rem;color:#ef4444;display:none;margin:0;"></p>
                <button onclick="spPw()" id="sp-pb" style="padding:.55rem;border-radius:8px;border:none;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;font-size:.78rem;font-weight:700;cursor:pointer;">تغيير</button>
            </div>
        </div>

        {{-- Links --}}
        <div class="sp-card">
            <div class="sp-ch">
                <div class="sp-ch-ico" style="background:#f0fdf4;"><svg width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.1-1.1m-.758-4.9a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg></div>
                <h4>روابط سريعة</h4>
            </div>
            <div style="padding:.4rem .6rem;">
                @foreach([
                    [route('student.dashboard'),'#eff6ff','#2563eb','M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','لوحة التحكم'],
                    [route('student.my-program'),'#ecfdf5','#059669','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','برنامجي'],
                    [route('student.attendance'),'#f3e8ff','#7c3aed','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','الحضور'],
                    [route('student.payments.index'),'#fef3c7','#d97706','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','المدفوعات'],
                    [route('student.tickets.index'),'#fef2f2','#dc2626','M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z','الدعم'],
                ] as [$href,$bg,$c,$ico,$lbl])
                <a href="{{ $href }}" style="display:flex;align-items:center;gap:.6rem;padding:.55rem .5rem;border-radius:9px;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <div style="width:28px;height:28px;border-radius:7px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="13" height="13" fill="none" stroke="{{ $c }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/></svg>
                    </div>
                    <span style="font-size:.8rem;font-weight:600;color:#374151;flex:1;">{{ $lbl }}</span>
                    <svg width="12" height="12" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
</div>

{{-- Edit Modal --}}
<div id="sp-em" class="sp-mb" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="sp-m">
        <div style="display:flex;align-items:center;gap:.6rem;padding:.9rem 1.25rem;border-bottom:1px solid #f1f5f9;">
            <span style="font-size:.9rem;font-weight:700;color:#1a2540;flex:1;">تعديل البيانات</span>
            <button onclick="document.getElementById('sp-em').classList.remove('open')" style="width:28px;height:28px;border-radius:7px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg width="13" height="13" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div style="padding:1.1rem 1.25rem;display:flex;flex-direction:column;gap:.8rem;">

            {{-- Photo row --}}
            <div style="display:flex;align-items:center;gap:1rem;padding:.75rem;background:#f8fafc;border-radius:12px;border:1px solid #f1f5f9;">
                <img id="sp-m-av"
                     src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0071AA&color=fff&size=120&bold=true&font-size=0.4' }}"
                     style="width:56px;height:56px;border-radius:14px;object-fit:cover;border:2px solid #e5e7eb;flex-shrink:0;">
                <div style="flex:1;">
                    <p style="font-size:.75rem;font-weight:700;color:#374151;margin:0 0 .3rem;">الصورة الشخصية</p>
                    <p style="font-size:.68rem;color:#9ca3af;margin:0 0 .45rem;">JPG أو PNG — بحد أقصى 2 ميجابايت</p>
                    <label style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .8rem;border-radius:7px;background:#0071AA;color:#fff;font-size:.72rem;font-weight:700;cursor:pointer;">
                        <svg width="11" height="11" fill="none" stroke="white" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        رفع صورة
                        <input type="file" id="sp-mf" accept="image/*" class="hidden" onchange="spMPhoto(this)">
                    </label>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;">
                <div><label class="sp-fl">الاسم <span style="color:#ef4444;">*</span></label><input type="text" id="sp-en" class="sp-f" value="{{ $user->name }}"></div>
                <div><label class="sp-fl">البريد <span style="color:#ef4444;">*</span></label><input type="email" id="sp-ee" class="sp-f" value="{{ $user->email }}" dir="ltr"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;">
                <div><label class="sp-fl">الجنسية</label><input type="text" id="sp-enat" class="sp-f" value="{{ $user->nationality }}" placeholder="مثال: سعودي"></div>
                <div><label class="sp-fl">تاريخ التخرج</label><input type="date" id="sp-eg" class="sp-f" value="{{ $user->date_of_graduation?->format('Y-m-d') }}"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;">
                <div><label class="sp-fl">نوع المؤهل</label><input type="text" id="sp-es" class="sp-f" value="{{ $user->specialization }}"></div>
                <div>
                    <label class="sp-fl">المؤهل التعليمي</label>
                    <select id="sp-est" class="sp-f">
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
            <div><label class="sp-fl">نبذة شخصية</label><textarea id="sp-eb" class="sp-f" rows="3" placeholder="اكتب نبذة...">{{ $user->bio }}</textarea></div>
            <p id="sp-ee2" style="font-size:.72rem;color:#ef4444;display:none;margin:0;"></p>
        </div>
        <div style="display:flex;gap:.6rem;padding:0 1.25rem 1.1rem;">
            <button onclick="document.getElementById('sp-em').classList.remove('open')" style="flex:1;padding:.6rem;border-radius:9px;border:none;background:#f1f5f9;color:#374151;font-size:.8rem;font-weight:700;cursor:pointer;">إلغاء</button>
            <button onclick="spSave()" id="sp-sb" style="flex:1;padding:.6rem;border-radius:9px;border:none;background:linear-gradient(135deg,#0071AA,#005588);color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;">حفظ</button>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="sp-lb" onclick="this.style.display='none'" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.85);align-items:center;justify-content:center;">
    <img id="sp-lb-i" src="" style="max-width:90vw;max-height:90vh;border-radius:14px;object-fit:contain;">
</div>

<div id="spt"></div>

@push('scripts')
<script>
const _c = document.querySelector('meta[name="csrf-token"]').content;

async function spUpload(file) {
    if (!file) return;
    const fd = new FormData(); fd.append('photo', file);
    try {
        const r = await fetch('{{ route("student.profile.update-photo") }}', { method:'POST', headers:{'X-CSRF-TOKEN':_c,'Accept':'application/json'}, body:fd });
        const d = await r.json();
        if (d.success) {
            document.getElementById('sp-av').src = d.photo_url;
            const mav = document.getElementById('sp-m-av');
            if (mav) mav.src = d.photo_url;
            spT(d.message,'s');
        } else spT(d.message||'خطأ','e');
    } catch { spT('خطأ','e'); }
}
function spPhoto(i) { spUpload(i.files[0]); }
function spMPhoto(i) { spUpload(i.files[0]); }

async function spSave() {
    const err = document.getElementById('sp-ee2'); err.style.display='none';
    const n = document.getElementById('sp-en').value.trim();
    const e = document.getElementById('sp-ee').value.trim();
    if (!n) { err.textContent='الاسم مطلوب'; err.style.display='block'; return; }
    if (!e) { err.textContent='البريد مطلوب'; err.style.display='block'; return; }
    const btn = document.getElementById('sp-sb'); btn.disabled=true; btn.textContent='...';
    try {
        const r = await fetch('{{ route("student.profile.update") }}', {
            method:'POST', headers:{'X-CSRF-TOKEN':_c,'Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({ _method:'PUT', name:n, email:e,
                nationality: document.getElementById('sp-enat').value.trim(),
                specialization: document.getElementById('sp-es').value.trim(),
                specialization_type: document.getElementById('sp-est').value,
                date_of_graduation: document.getElementById('sp-eg').value,
                bio: document.getElementById('sp-eb').value.trim() })
        });
        const d = await r.json();
        if (d.success) { spT(d.message,'s'); document.getElementById('sp-em').classList.remove('open'); setTimeout(()=>location.reload(),800); }
        else { err.textContent = d.errors ? Object.values(d.errors).flat().join(' | ') : (d.message||'خطأ'); err.style.display='block'; }
    } catch { err.textContent='خطأ'; err.style.display='block'; }
    finally { btn.disabled=false; btn.textContent='حفظ'; }
}

async function spPw() {
    const err=document.getElementById('sp-pe'); err.style.display='none';
    const c=document.getElementById('sp-cp').value, n=document.getElementById('sp-np').value, n2=document.getElementById('sp-np2').value;
    if (!c) { err.textContent='أدخل كلمة المرور الحالية'; err.style.display='block'; return; }
    if (n.length<8) { err.textContent='8 أحرف على الأقل'; err.style.display='block'; return; }
    if (n!==n2) { err.textContent='كلمة المرور غير متطابقة'; err.style.display='block'; return; }
    const btn=document.getElementById('sp-pb'); btn.disabled=true; btn.textContent='...';
    try {
        const r = await fetch('{{ route("student.profile.update-password") }}', {
            method:'POST', headers:{'X-CSRF-TOKEN':_c,'Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({ current_password:c, password:n, password_confirmation:n2 })
        });
        const d = await r.json();
        if (d.success) { spT(d.message,'s'); ['sp-cp','sp-np','sp-np2'].forEach(id=>document.getElementById(id).value=''); }
        else { err.textContent = d.errors ? Object.values(d.errors).flat().join(' | ') : (d.message||'خطأ'); err.style.display='block'; }
    } catch { err.textContent='خطأ'; err.style.display='block'; }
    finally { btn.disabled=false; btn.textContent='تغيير'; }
}

function spT(m,t) {
    const el=document.getElementById('spt'); el.textContent=m;
    el.style.background = t==='s' ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#ef4444,#dc2626)';
    el.classList.add('show'); setTimeout(()=>el.classList.remove('show'),3000);
}
</script>
@endpush
@endsection
