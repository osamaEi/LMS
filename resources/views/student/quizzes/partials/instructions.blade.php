@if(filled($quiz->description))
<section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 mb-6" aria-label="تعليمات الاختبار">
    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ in_array($quiz->type, ['final', 'exam']) ? 'تعليمات الاختبار النهائي' : 'تعليمات الاختبار' }}</h2>
    <div class="text-gray-600 dark:text-gray-300" style="white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.9;">{{ $quiz->description }}</div>
</section>
@endif
