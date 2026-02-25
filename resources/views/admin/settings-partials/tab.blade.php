{{--
    Generic settings tab card.
    Variables: $tabTitle, $tabDesc, $tabIcon, $group, $settingsGroup (array of settings)
--}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700"
         style="background:linear-gradient(135deg,rgba(0,113,170,.05),rgba(0,90,136,.03))">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            <div class="p-2 rounded-lg" style="background:rgba(0,113,170,.12)">
                <svg class="w-5 h-5" style="color:#0071AA" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tabIcon }}"/>
                </svg>
            </div>
            {{ $tabTitle }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $tabDesc }}</p>
    </div>

    <form action="{{ route('admin.settings.update-group', $group) }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf
        @method('PUT')

        @if(empty($settingsGroup))
        <div class="flex flex-col items-center py-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">لا توجد إعدادات في هذا القسم</p>
        </div>
        @else
        <div class="space-y-6">
            @foreach($settingsGroup as $setting)
                @include('admin.settings-partials.field', ['setting' => $setting])
            @endforeach
        </div>

        <div class="flex items-center justify-end pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="px-8 py-3 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl"
                    style="background:linear-gradient(135deg,#0071AA,#005a88)">
                حفظ التغييرات
            </button>
        </div>
        @endif
    </form>
</div>
