@extends('layouts.dashboard')

@section('title', $survey->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.surveys.index') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $survey->title }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $survey->description }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.surveys.report', $survey) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-green-600 border border-green-600 rounded-lg hover:bg-green-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                التقرير
            </a>
            <a href="{{ route('admin.surveys.edit', $survey) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600">
                تعديل
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_responses'] }}</div>
            <div class="text-gray-600 dark:text-gray-400">إجمالي الإجابات</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-3xl font-bold text-brand-500">{{ $stats['avg_rating'] }}/5</div>
            <div class="text-gray-600 dark:text-gray-400">متوسط التقييم</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-3xl font-bold text-red-500">{{ $stats['low_ratings'] }}</div>
            <div class="text-gray-600 dark:text-gray-400">تقييمات منخفضة</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $survey->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $survey->getStatusLabel() }}
            </span>
            <div class="text-gray-600 dark:text-gray-400 mt-2">حالة الاستبيان</div>
        </div>
    </div>

    <!-- Questions Overview -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">الأسئلة والنتائج</h3>

        <div class="space-y-4">
            @foreach($survey->questions as $question)
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <span class="text-sm text-gray-500">سؤال {{ $loop->iteration }}</span>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $question->question }}</p>
                    </div>
                    @if($question->isRatingType())
                    <div class="text-left">
                        <div class="text-2xl font-bold text-brand-500">{{ $question->getAverageRating() }}</div>
                        <div class="text-xs text-gray-500">متوسط التقييم</div>
                    </div>
                    @endif
                </div>

                @if($question->isRatingType())
                <div class="mt-3">
                    <div class="flex items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        @php
                            $count = $question->responses()->where('rating', $i)->count();
                            $percentage = $question->responses()->count() > 0 ? ($count / $question->responses()->count()) * 100 : 0;
                        @endphp
                        <div class="flex-1">
                            <div class="text-xs text-center text-gray-500 mb-1">{{ $i }}</div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="text-xs text-center text-gray-500 mt-1">{{ $count }}</div>
                        </div>
                        @endfor
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
