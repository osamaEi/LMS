@extends('layouts.teacher')

@section('title', 'استبيانات الرضا')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">استبيانات الرضا</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">شارك في استبيانات قياس الرضا لتحسين جودة الخدمات</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    <!-- Available Surveys -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            استبيانات متاحة
        </h2>

        @if($availableSurveys->count() > 0)
            <div class="space-y-4">
                @foreach($availableSurveys as $survey)
                    <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800 dark:text-white">{{ $survey->title }}</h3>
                                @if($survey->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $survey->description }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $survey->questions->count() }} أسئلة
                                    </span>
                                    @if($survey->ends_at)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            متاح حتى {{ $survey->ends_at->format('Y-m-d') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('teacher.surveys.show', $survey) }}" class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                المشاركة الآن
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">لا توجد استبيانات متاحة حالياً</p>
            </div>
        @endif
    </div>

    <!-- Completed Surveys -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            استبيانات مكتملة
        </h2>

        @if($completedSurveys->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="text-right px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300">الاستبيان</th>
                            <th class="text-right px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300">تاريخ الإكمال</th>
                            <th class="text-center px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($completedSurveys as $survey)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $survey->title }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">
                                    {{ $survey->responses->first()?->created_at?->format('Y-m-d H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        مكتمل
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">لم تكمل أي استبيانات بعد</p>
        @endif
    </div>
</div>
@endsection
