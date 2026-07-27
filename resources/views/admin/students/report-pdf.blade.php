<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { direction: rtl; color: #1e293b; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        h2 { font-size: 14px; margin: 18px 0 6px; color: #0071AA; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; }
        .muted { color: #64748b; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: right; }
        th { background: #f1f5f9; font-size: 11px; }
        .tiles { width: 100%; margin: 10px 0; }
        .tiles td { border: none; padding: 4px; }
        .tile { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; text-align: center; }
        .tile .v { font-size: 16px; font-weight: bold; color: #0071AA; }
        .tile .l { font-size: 10px; color: #64748b; }
        .note { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    @php $s = $report['summary']; @endphp
    <h1>التقرير الأكاديمي</h1>
    <div class="muted">{{ $student->name }} — {{ $student->email }} · {{ now()->format('Y/m/d') }}</div>

    <table class="tiles">
        <tr>
            <td><div class="tile"><div class="v">{{ $s['attendance_rate'] }}%</div><div class="l">نسبة الحضور</div></div></td>
            <td><div class="tile"><div class="v">{{ $s['attended_sessions'] }}/{{ $s['total_sessions'] }}</div><div class="l">الجلسات</div></div></td>
            <td><div class="tile"><div class="v">{{ $s['quizzes_count'] }}</div><div class="l">الاختبارات</div></div></td>
            <td><div class="tile"><div class="v">{{ $s['avg_quiz'] !== null ? $s['avg_quiz'].'%' : '—' }}</div><div class="l">متوسط الاختبارات</div></div></td>
            <td><div class="tile"><div class="v">{{ $s['homework_count'] }}</div><div class="l">الواجبات</div></div></td>
        </tr>
    </table>

    <h2>المواد والدرجة النهائية</h2>
    @if($report['subjects']->isEmpty())
        <div class="muted">لا توجد مواد مسجلة.</div>
    @else
        <table>
            <tr><th>المادة</th><th>الكود</th><th>الحالة</th><th>الدرجة النهائية</th><th>التقدير</th></tr>
            @foreach($report['subjects'] as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['code'] ?? '—' }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['final_grade'] ?? '—' }}</td>
                    <td>{{ $row['grade_letter'] ?? '—' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <h2>الاختبارات</h2>
    @if($report['quizzes']->isEmpty())
        <div class="muted">لا توجد محاولات اختبار.</div>
    @else
        <table>
            <tr><th>الاختبار</th><th>التاريخ</th><th>الدرجة</th><th>النسبة</th><th>ناجح</th></tr>
            @foreach($report['quizzes'] as $row)
                <tr>
                    <td>{{ $row['title'] }}</td>
                    <td>{{ $row['submitted_at'] ?? '—' }}</td>
                    <td>{{ $row['score'] ?? '—' }}</td>
                    <td>{{ $row['percentage'] !== null ? $row['percentage'].'%' : '—' }}</td>
                    <td>{{ $row['passed'] ? 'نعم' : 'لا' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <h2>الواجبات</h2>
    @if($report['homework']->isEmpty())
        <div class="muted">لا توجد واجبات مُسلَّمة.</div>
    @else
        <table>
            <tr><th>الواجب</th><th>التاريخ</th><th>الدرجة</th><th>من</th><th>ملاحظة</th></tr>
            @foreach($report['homework'] as $row)
                <tr>
                    <td>{{ $row['title'] }}</td>
                    <td>{{ $row['submitted_at'] ?? '—' }}</td>
                    <td>{{ $row['grade'] ?? '—' }}</td>
                    <td>{{ $row['max_grade'] ?? '—' }}</td>
                    <td>{{ $row['feedback'] ?? '—' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if($note)
        <div class="note"><strong>ملاحظة الإدارة:</strong> {{ $note }}</div>
    @endif
</body>
</html>
