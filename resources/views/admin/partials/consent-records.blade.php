<section class="bg-white dark:bg-gray-800 rounded-xl p-6 my-6" dir="rtl">
    <h2 class="text-lg font-bold mb-4">سجل موافقات المتدرب</h2>
    <p class="text-sm text-gray-500 mb-4">عدم اختيار التسويق يُسجل كعدم موافقة، ولا يؤثر على التسجيل.</p>
    @forelse($consentUser->consentRecords()->limit(100)->get() as $consent)
        <details class="border rounded-lg p-3 mb-3">
            <summary class="cursor-pointer">
                {{ ['basic' => 'الشروط والخصوصية ومعالجة البيانات', 'marketing' => 'التواصل التسويقي', 'certificate' => 'إصدار الشهادة'][$consent->type] ?? $consent->type }}
                — {{ $consent->accepted ? 'وافق' : 'لم يوافق' }}
                — <bdi>{{ $consent->recorded_at->format('Y-m-d H:i:s') }}</bdi> ({{ config('app.timezone') }})
            </summary>
            <p class="mt-3">{{ $consent->statement }}</p>
            <p class="text-sm mt-2">عنوان IP: <bdi dir="ltr">{{ $consent->ip_address ?? 'غير متاح' }}</bdi> — إصدار النص: {{ $consent->statement_version }}</p>
            @if($consent->type === 'certificate')
                <p class="text-sm mt-2">البرنامج: {{ $consent->context['program_name'] ?? $consent->program_id }}</p>
                <p class="text-sm">الاسم وقت الموافقة: {{ $consent->context['name'] ?? '—' }} — الهوية: <bdi>{{ $consent->context['national_id'] ?? '—' }}</bdi></p>
            @endif
            @foreach($consent->context['policies'] ?? [] as $policy)
                <details class="mt-3">
                    <summary>{{ $policy['title_ar'] }} — إصدار {{ $policy['version'] }}</summary>
                    <div class="mt-2 whitespace-pre-wrap">{{ strip_tags($policy['content_ar'] ?? '') }}</div>
                </details>
            @endforeach
        </details>
    @empty
        <p class="text-gray-500">لا توجد موافقات موثقة بعد. لم تُنشأ موافقات بأثر رجعي.</p>
    @endforelse
    <p class="text-xs text-gray-500">يعرض أحدث 100 سجل.</p>
</section>
