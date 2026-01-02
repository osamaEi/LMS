@extends('layouts.dashboard')

@section('title', $quiz->title_ar)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.quizzes.index', $subject->id) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
               style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-white/80 text-sm">{{ $subject->name }}</p>
                <h1 class="text-2xl font-bold">{{ $quiz->title_ar }}</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quiz Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            @if($quiz->description_ar)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">وصف الاختبار</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $quiz->description_ar }}</p>
            </div>
            @endif

            <!-- Instructions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">تعليمات الاختبار</h2>
                <ul class="space-y-3 text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>الاختبار يحتوي على <strong>{{ $quiz->questions_count }} سؤال</strong></span>
                    </li>
                    @if($quiz->duration_minutes)
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>مدة الاختبار <strong>{{ $quiz->duration_minutes }} دقيقة</strong> - سيتم التسليم تلقائياً عند انتهاء الوقت</span>
                    </li>
                    @else
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>الاختبار <strong>غير محدد بوقت</strong></span>
                    </li>
                    @endif
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>الدرجة الكلية <strong>{{ $quiz->total_marks }} درجة</strong> ودرجة النجاح <strong>{{ $quiz->pass_marks }} درجة</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>عدد المحاولات المسموحة: <strong>{{ $quiz->max_attempts }} محاولة</strong></span>
                    </li>
                    @if($quiz->shuffle_questions)
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>ترتيب الأسئلة عشوائي</span>
                    </li>
                    @endif
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-red-600 dark:text-red-400">لا يمكن الرجوع للاختبار بعد التسليم</span>
                    </li>
                </ul>
            </div>

            <!-- Previous Attempts -->
            @if($attempts->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">محاولاتك السابقة</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">المحاولة</th>
                                <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">التاريخ</th>
                                <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">الدرجة</th>
                                <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">الحالة</th>
                                <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($attempts as $index => $attempt)
                            <tr>
                                <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $attempt->started_at->format('Y/m/d H:i') }}</td>
                                <td class="py-3 px-4">
                                    @if($attempt->status === 'completed')
                                        <span class="font-bold {{ $attempt->is_passed ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $attempt->score ?? '-' }} / {{ $quiz->total_marks }}
                                        </span>
                                    @else
                                        <span class="text-amber-600">قيد التقييم</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($attempt->status === 'completed' && $attempt->is_passed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #d1fae5; color: #065f46;">
                                            ناجح
                                        </span>
                                    @elseif($attempt->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #fee2e2; color: #991b1b;">
                                            راسب
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #fef3c7; color: #92400e;">
                                            قيد التقييم
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($attempt->status === 'completed' && $quiz->show_results)
                                        <a href="{{ route('student.quizzes.result', [$subject->id, $quiz->id, $attempt->id]) }}"
                                           class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                            عرض النتيجة
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">ملخص الاختبار</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">نوع الاختبار</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            @switch($quiz->type)
                                @case('quiz') اختبار قصير @break
                                @case('exam') امتحان @break
                                @case('homework') واجب @break
                            @endswitch
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">عدد الأسئلة</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quiz->questions_count }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">الدرجة الكلية</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quiz->total_marks }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">المحاولات المتبقية</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quiz->max_attempts - $attempts->count() }}</span>
                    </div>
                    @if($quiz->ends_at)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">ينتهي في</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quiz->ends_at->format('Y/m/d H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Start Button -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                @if($activeAttempt)
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #fef3c7;">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">لديك محاولة جارية</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">يمكنك استكمال الاختبار من حيث توقفت</p>
                        <a href="{{ route('student.quizzes.take', [$subject->id, $quiz->id]) }}"
                           class="block w-full py-3 px-4 rounded-xl font-bold text-white text-center transition-all"
                           style="background-color: #f59e0b;">
                            استكمال الاختبار
                        </a>
                    </div>
                @elseif(!$quiz->isAvailable())
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #fee2e2;">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">الاختبار غير متاح</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            @if($quiz->starts_at && $quiz->starts_at > now())
                                سيبدأ في {{ $quiz->starts_at->format('Y/m/d H:i') }}
                            @elseif($quiz->ends_at && $quiz->ends_at < now())
                                انتهى وقت الاختبار
                            @else
                                الاختبار مغلق حالياً
                            @endif
                        </p>
                    </div>
                @elseif($attempts->count() >= $quiz->max_attempts)
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #fee2e2;">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">استنفدت المحاولات</h3>
                        <p class="text-gray-600 dark:text-gray-400">لقد استخدمت جميع المحاولات المتاحة</p>
                    </div>
                @else
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #d1fae5;">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">جاهز للبدء؟</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">تأكد من قراءة التعليمات قبل البدء</p>
                        <form action="{{ route('student.quizzes.start', [$subject->id, $quiz->id]) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="block w-full py-3 px-4 rounded-xl font-bold text-white text-center transition-all"
                                    style="background-color: #10b981;">
                                ابدأ الاختبار
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
