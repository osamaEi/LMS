@extends('layouts.dashboard')

@section('title', 'الحضور والغياب')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6">

    {{-- Header --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #0071AA 55%, #0ea5e9 100%);">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.05)"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 h-48 w-48 rounded-full" style="background:rgba(255,255,255,.05)"></div>
        <div class="relative">
            <h1 class="text-2xl font-bold tracking-tight">الحضور والغياب</h1>
            <p class="mt-1 text-sm" style="color:rgba(255,255,255,.7)">سجل حضور الطلاب لجميع جلساتك</p>
        </div>
    </div>

    @if($subjects->isEmpty())
    <div class="flex flex-col items-center justify-center rounded-2xl border border-stroke bg-white py-20 text-center shadow-sm dark:border-strokedark dark:bg-boxdark">
        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-meta-4">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor" class="text-gray-300">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
        </div>
        <p class="text-lg font-semibold text-gray-500">لا توجد مواد مسندة إليك</p>
    </div>
    @else

    <div class="space-y-6">
        @foreach($subjects as $subject)
        @php
            $pastSessions = $subject->sessions;
            $totalEnrolled = $subject->enrollments_count;
        @endphp
        <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
            {{-- Subject Header --}}
            <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark"
                 style="background: linear-gradient(135deg, rgba(0,113,170,0.06), rgba(0,113,170,0.02));">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl text-white font-bold text-sm"
                         style="background:linear-gradient(135deg,#0071AA,#005a88)">
                        {{ mb_substr($subject->name_ar ?? $subject->name ?? 'م', 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-black dark:text-white">{{ $subject->name_ar ?? $subject->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $totalEnrolled }} طالب مسجل · {{ $pastSessions->count() }} جلسة منتهية</p>
                    </div>
                </div>
                <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
                   class="text-xs font-medium text-primary hover:underline">عرض المادة</a>
            </div>

            @if($pastSessions->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400">لا توجد جلسات منتهية بعد</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background: linear-gradient(180deg,#f8fafc,#f1f5f9);">
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase">#</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase">الجلسة</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase">التاريخ</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase">الحضور</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase">النسبة</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @foreach($pastSessions as $session)
                        @php
                            $attended = $session->attended_count;
                            $rate = $totalEnrolled > 0 ? round(($attended / $totalEnrolled) * 100) : 0;
                            $rateColor = $rate >= 75 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
                            $rateBg    = $rate >= 75 ? 'rgba(16,185,129,.1)' : ($rate >= 50 ? 'rgba(245,158,11,.1)' : 'rgba(239,68,68,.1)');
                        @endphp
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-5 py-3.5 text-sm text-gray-400 font-medium">{{ $session->session_number }}</td>
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-semibold text-black dark:text-white">{{ $session->title }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $session->type === 'live_zoom' ? 'Zoom مباشر' : 'مسجّل' }}
                                </p>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-500">
                                {{ $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') : '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full" style="width:{{ $rate }}%; background:{{ $rateColor }}"></div>
                                    </div>
                                    <span class="text-sm font-bold" style="color:{{ $rateColor }}">{{ $attended }}/{{ $totalEnrolled }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold"
                                      style="background:{{ $rateBg }}; color:{{ $rateColor }}">
                                    {{ $rate }}%
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('teacher.my-subjects.sessions.attendance', [$subject->id, $session->id]) }}"
                                       class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition hover:shadow"
                                       style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                        عرض
                                    </a>
                                    <a href="{{ route('teacher.my-subjects.sessions.attendance', [$subject->id, $session->id]) }}#add-attendance"
                                       class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold transition hover:shadow"
                                       style="background:rgba(16,185,129,.12); color:#059669;">
                                        + إضافة
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
