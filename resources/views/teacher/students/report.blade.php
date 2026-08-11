@extends('layouts.dashboard')

@section('title', 'تقرير المتدرب')

@section('content')
@php
    $m = $report['marks'];
    $w = $m['weights'];

    // Single aggregate row across all the teacher's subjects for this student.
    $rows  = $m['rows'];
    $agg   = fn($k) => $rows->count() > 0 ? round($rows->avg($k), 1) : 0;
    $cells = [
        ['الحضور',        $agg('attendance'),                      $w['attendance']],
        ['المشاركة',      $agg('participation'),                   $w['quizzes'] + $w['homework']],
        ['اختبار نصفي',   $agg('midterm'),                         $w['midterm']],
        ['اختبار نهائي',  $agg('final'),                           $w['final']],
    ];
    $total = $rows->count() > 0 ? round($rows->avg('total'), 1) : 0;
@endphp

<div style="direction:rtl;font-family:'Segoe UI',sans-serif;" class="mx-auto max-w-screen-xl p-4 md:p-6">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px;">
        <div>
            <p style="color:#64748b;font-size:12px;margin:0 0 4px;">تقرير المتدرب — موادك فقط</p>
            <h1 style="color:#0f172a;font-size:22px;font-weight:800;margin:0;">{{ $student->name }}</h1>
        </div>
        <a href="{{ route('teacher.students.index') }}" style="background:#f1f5f9;color:#334155;padding:9px 16px;border-radius:11px;text-decoration:none;font-size:13px;font-weight:700;">← قائمة المتدربين</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ session('success') }}</div>
    @endif

    @if($rows->isEmpty())
        <div style="border:2px solid #0f172a;border-radius:6px;padding:28px;text-align:center;color:#64748b;font-weight:600;">
            لا توجد مواد لحساب توزيع الدرجات.
        </div>
    @else
        <table class="marks">
            <thead>
                <tr>
                    <th class="mk-total-h">المجموع<span>100</span></th>
                    @foreach(array_reverse($cells) as [$label, $val, $max])
                        <th>{{ $label }}<span>{{ $max }}</span></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="mk-total-c">{{ $total }}</td>
                    @foreach(array_reverse($cells) as [$label, $val, $max])
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endif
</div>

<style>
    .marks { width:100%; border-collapse:collapse; table-layout:fixed; direction:rtl; }
    .marks th, .marks td { border:2px solid #0f172a; text-align:center; }
    .marks th { padding:14px 8px; font-size:15px; font-weight:800; color:#0f172a;
                line-height:1.5; }
    .marks th span { display:block; margin-top:3px; font-size:11px; font-weight:600; color:#64748b; }
    .marks td { padding:34px 8px; font-size:26px; font-weight:800; color:#0f172a; }
    .marks .mk-total-h, .marks .mk-total-c { background:#f1f5f9; }
    .marks .mk-total-c { color:#0071AA; }
    @media (max-width:640px) {
        .marks th { font-size:12px; padding:10px 4px; }
        .marks td { font-size:19px; padding:24px 4px; }
    }
</style>
@endsection
