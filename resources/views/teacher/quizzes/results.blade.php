@extends('layouts.dashboard')

@section('title', 'نتائج الاختبار')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}"
                   class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                   style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-white/80 text-sm">{{ $quiz->title_ar }}</p>
                    <h1 class="text-2xl font-bold">نتائج الاختبار</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المحاولات</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_attempts'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">ناجح</p>
            <p class="text-2xl font-bold mt-1" style="color: #10b981;">{{ $stats['passed'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">راسب</p>
            <p class="text-2xl font-bold mt-1" style="color: #ef4444;">{{ $stats['failed'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">المتوسط</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['average_score'], 1) }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">أعلى درجة</p>
            <p class="text-2xl font-bold mt-1" style="color: #10b981;">{{ number_format($stats['highest_score'], 1) }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">أقل درجة</p>
            <p class="text-2xl font-bold mt-1" style="color: #ef4444;">{{ number_format($stats['lowest_score'], 1) }}%</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">محاولات الطلاب</h2>
        </div>

        @if($attempts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">الطالب</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">الدرجة</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">النسبة</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">الوقت المستغرق</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">تاريخ التسليم</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($attempts as $attempt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($attempt->student->name) }}&background=8b5cf6&color=fff&size=40"
                                             alt="{{ $attempt->student->name }}"
                                             class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $attempt->student->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $attempt->student->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">
                                    {{ $attempt->score }} / {{ $quiz->total_marks }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold" style="color: {{ $attempt->percentage >= 50 ? '#10b981' : '#ef4444' }};">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($attempt->passed)
                                        <span class="px-3 py-1 rounded-full text-xs font-bold" style="background-color: #d1fae5; color: #047857;">ناجح</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-bold" style="background-color: #fee2e2; color: #991b1b;">راسب</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    {{ $attempt->formatted_time_spent }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    {{ $attempt->submitted_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('teacher.quizzes.review', [$subject->id, $quiz->id, $attempt->id]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                       style="background-color: #ede9fe; color: #5b21b6;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        مراجعة
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($attempts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $attempts->links() }}
                </div>
            @endif
        @else
            <div class="p-16 text-center">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6" style="background-color: #f3f4f6;">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد محاولات</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">لم يقم أي طالب بتقديم هذا الاختبار بعد</p>
            </div>
        @endif
    </div>
</div>
@endsection
