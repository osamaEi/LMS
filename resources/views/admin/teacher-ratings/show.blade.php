@extends('layouts.dashboard')

@section('title', 'تقييمات المعلم - ' . $teacher->name)

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
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تقييمات المعلم</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ $teacher->name }}</p>
        </div>
    </div>

    <!-- Teacher Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-wrap items-center gap-6">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=6366f1&color=fff&size=96"
                 class="w-24 h-24 rounded-full border-4 border-brand-500" alt="{{ $teacher->name }}">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $teacher->name }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $teacher->email }}</p>
                <div class="mt-3 flex items-center gap-4">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= round($teacher->getAverageRating()) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                        <span class="mr-2 text-lg font-bold text-gray-900 dark:text-white">{{ number_format($teacher->getAverageRating(), 2) }}</span>
                    </div>
                    <span class="text-gray-500 dark:text-gray-400">({{ $ratings->total() }} تقييم)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">المعرفة والإلمام</div>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($breakdown['knowledge'] ?? 0) * 20 }}%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($breakdown['knowledge'] ?? 0, 1) }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">مهارات التواصل</div>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: {{ ($breakdown['communication'] ?? 0) * 20 }}%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($breakdown['communication'] ?? 0, 1) }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">الالتزام بالمواعيد</div>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-500 rounded-full" style="width: {{ ($breakdown['punctuality'] ?? 0) * 20 }}%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($breakdown['punctuality'] ?? 0, 1) }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">الدعم والمساعدة</div>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 rounded-full" style="width: {{ ($breakdown['support'] ?? 0) * 20 }}%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($breakdown['support'] ?? 0, 1) }}</span>
            </div>
        </div>
    </div>

    <!-- Ratings by Subject -->
    @if($subjectRatings->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">التقييمات حسب المادة</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subjectRatings as $subjectRating)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="font-medium text-gray-900 dark:text-white">{{ $subjectRating->subject->name ?? 'غير معروف' }}</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($subjectRating->avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                            <span class="mr-1 text-sm font-medium">{{ number_format($subjectRating->avg_rating, 1) }}</span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $subjectRating->count }} تقييم</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Ratings List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">جميع التقييمات</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($ratings as $rating)
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($rating->student->name ?? 'Unknown') }}&background=6366f1&color=fff"
                             class="w-10 h-10 rounded-full" alt="">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $rating->student->name ?? 'طالب محذوف' }}</div>
                            <div class="text-sm text-gray-500">{{ $rating->subject->name ?? '-' }} - {{ $rating->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $rating->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                </div>
                @if($rating->comment)
                <div class="mt-3 text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    {{ $rating->comment }}
                </div>
                @endif
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded px-2 py-1">
                        <span class="text-gray-500">المعرفة:</span>
                        <span class="font-medium">{{ $rating->knowledge_rating }}/5</span>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded px-2 py-1">
                        <span class="text-gray-500">التواصل:</span>
                        <span class="font-medium">{{ $rating->communication_rating }}/5</span>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded px-2 py-1">
                        <span class="text-gray-500">الالتزام:</span>
                        <span class="font-medium">{{ $rating->punctuality_rating }}/5</span>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded px-2 py-1">
                        <span class="text-gray-500">الدعم:</span>
                        <span class="font-medium">{{ $rating->support_rating }}/5</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <p class="mt-2">لا توجد تقييمات لهذا المعلم حتى الآن</p>
            </div>
            @endforelse
        </div>
        @if($ratings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $ratings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
