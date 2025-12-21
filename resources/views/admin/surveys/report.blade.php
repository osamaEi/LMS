@extends('layouts.dashboard')

@section('title', 'تقرير الاستبيان')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.surveys.show', $survey) }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تقرير الاستبيان</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $survey->title }}</p>
            </div>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            طباعة التقرير
        </button>
    </div>

    <!-- Questions Analysis -->
    <div class="space-y-4">
        @foreach($questionStats as $questionId => $stat)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-sm text-gray-500">سؤال {{ $loop->iteration }}</span>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $stat['question'] }}</h4>
                    <span class="text-xs text-gray-500">{{ $stat['responses_count'] }} إجابة</span>
                </div>
                @if($stat['type'] === 'rating')
                <div class="text-left">
                    <div class="text-3xl font-bold text-brand-500">{{ $stat['avg_rating'] }}</div>
                    <div class="text-sm text-gray-500">من 5</div>
                </div>
                @endif
            </div>

            @if($stat['type'] === 'rating')
            <!-- Rating visualization -->
            <div class="mt-4">
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-6 h-6 {{ $i <= round($stat['avg_rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>

                @if($stat['low_count'] > 0)
                <div class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <p class="text-sm text-red-700 dark:text-red-400">
                        <svg class="inline w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        {{ $stat['low_count'] }} تقييم منخفض (2 أو أقل) - يتطلب مراجعة
                    </p>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">ملخص التقرير</h3>
        <div class="prose dark:prose-invert max-w-none">
            <p>تم جمع {{ $survey->responses()->distinct('user_id')->count('user_id') }} إجابة على هذا الاستبيان.</p>
            <p>متوسط التقييم العام: <strong>{{ $survey->getAverageRating() }} من 5</strong></p>
            <p>نسبة الرضا: <strong>{{ round(($survey->getAverageRating() / 5) * 100, 1) }}%</strong></p>
        </div>
    </div>
</div>
@endsection
