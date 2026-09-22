<section class="bg-white dark:bg-gray-800 rounded-xl p-6 my-6" dir="rtl">
    <h2 class="text-lg font-bold mb-4">{{ __('Trainee Consent Records') }}</h2>
    <p class="text-sm text-gray-500 mb-4">{{ __('Not selecting marketing is recorded as non-consent and does not affect registration.') }}</p>
    @forelse($consentUser->consentRecords()->limit(100)->get() as $consent)
        <details class="border rounded-lg p-3 mb-3">
            <summary class="cursor-pointer">
                {{ ['basic' => __('Terms, Privacy and Data Processing'), 'marketing' => __('Marketing Communications'), 'certificate' => __('Certificate Issuance')][$consent->type] ?? $consent->type }}
                — {{ $consent->accepted ? __('Consented') : __('Did Not Consent') }}
                — <bdi>{{ $consent->recorded_at->format('Y-m-d H:i:s') }}</bdi> ({{ config('app.timezone') }})
            </summary>
            <p class="mt-3">{{ $consent->statement }}</p>
            <p class="text-sm mt-2">{{ __('IP Address') }}: <bdi dir="ltr">{{ $consent->ip_address ?? __('Not available') }}</bdi> — {{ __('Statement version') }}: {{ $consent->statement_version }}</p>
            @if($consent->type === 'certificate')
                <p class="text-sm mt-2">{{ __('Program') }}: {{ $consent->context['program_name'] ?? $consent->program_id }}</p>
                <p class="text-sm">{{ __('Name at time of consent') }}: {{ $consent->context['name'] ?? '—' }} — {{ __('National ID') }}: <bdi>{{ $consent->context['national_id'] ?? '—' }}</bdi></p>
            @endif
            @foreach($consent->context['policies'] ?? [] as $policy)
                <details class="mt-3">
                    <summary>{{ $policy['title_ar'] }} — {{ __('Version') }} {{ $policy['version'] }}</summary>
                    <div class="mt-2 whitespace-pre-wrap">{{ strip_tags($policy['content_ar'] ?? '') }}</div>
                </details>
            @endforeach
        </details>
    @empty
        <p class="text-gray-500">{{ __('No documented consents yet. Consents were not created retroactively.') }}</p>
    @endforelse
    <p class="text-xs text-gray-500">{{ __('Showing the latest 100 records.') }}</p>
</section>
