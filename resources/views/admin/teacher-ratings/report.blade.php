@extends('layouts.dashboard')

@section('title', 'تقرير تقييمات المعلمين')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.teacher-ratings.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تقرير تقييمات المعلمين</h2>
            <p class="text-gray-600 dark:text-gray-400">إحصائيات شاملة عن تقييمات المعلمين (معيار NELC 2.4.9)</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي التقييمات</div>
            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_ratings'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">المتوسط العام</div>
            <div class="mt-2 flex items-center gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_overall'] }}</span>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">المعرفة والإلمام</div>
            <div class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['avg_knowledge'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">مهارات التواصل</div>
            <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['avg_communication'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">الالتزام بالمواعيد</div>
            <div class="mt-2 text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['avg_punctuality'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">الدعم والمساعدة</div>
            <div class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['avg_support'] }}</div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">توزيع التقييمات</h3>
        <div class="space-y-3">
            @for($i = 5; $i >= 1; $i--)
            @php
                $count = $distribution[$i] ?? 0;
                $total = array_sum($distribution->toArray());
                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
            @endphp
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 w-20">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $i }}</span>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div class="flex-1 h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-400 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                </div>
                <div class="w-20 text-left">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Top Rated Teachers -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">أفضل المعلمين تقييماً</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">المعلمون الذين حصلوا على 3 تقييمات أو أكثر</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الترتيب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">المعلم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">متوسط التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">عدد التقييمات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($topTeachers as $index => $teacher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            @if($index == 0)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-800 font-bold">1</span>
                            @elseif($index == 1)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-800 font-bold">2</span>
                            @elseif($index == 2)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-bold">3</span>
                            @else
                            <span class="text-gray-500">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=6366f1&color=fff"
                                     class="w-10 h-10 rounded-full border-2 border-brand-500" alt="">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($teacher->avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                                <span class="mr-2 text-sm font-medium text-gray-900 dark:text-white">{{ number_format($teacher->avg_rating, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $teacher->ratings_count }} تقييم
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.teacher-ratings.show', $teacher) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <p class="mt-2">لا يوجد معلمون لديهم 3 تقييمات أو أكثر</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
