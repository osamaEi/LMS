@extends('layouts.dashboard')
@section('title', 'الجدول الأكاديمي')

@php
$calSessions = $sessions->map(fn($s) => [
    'id'               => $s->id,
    'title'            => $s->title_ar ?: ($s->subject->name_ar ?? $s->program->name_ar ?? 'جلسة'),
    'subject_name'     => $s->subject->name_ar ?? '',
    'program_name'     => $s->program->name_ar ?? $s->subject?->program?->name_ar ?? '',
    'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
    'duration_minutes' => $s->duration_minutes ?? 60,
    'type'             => $s->type ?? '',
    'status'           => (string)($s->status ?? ''),
    'session_number'   => $s->session_number,
    'class_name'       => $s->programClass->name ?? $s->subject?->programClass?->name ?? $s->subject?->term?->programClass?->name ?? '',
    'zoom_join_url'    => $s->zoom_join_url,
    'zoom_start_url'   => $s->zoom_start_url,
    'subject_id'       => $s->subject_id,
    'program_id'       => $s->program_id,
])->filter(fn($s) => $s['scheduled_at'])->values();
@endphp

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">الجدول الأكاديمي</h1>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([
                [$stats['total'],     'الكل',    'rgba(255,255,255,.8)'],
                [$stats['upcoming'],  'قادمة',   '#fde68a'],
                [$stats['live'],      'مباشرة',  '#86efac'],
                [$stats['completed'], 'مكتملة',  '#a5b4fc'],
            ] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:60px;">
                <div style="font-size:20px;font-weight:700;color:{{ $c }};line-height:1;">{{ $v }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@include('teacher.partials.weekly-calendar')
</div>
@endsection
