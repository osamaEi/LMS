@extends('layouts.dashboard')

@section('title', __('لوحة توافق NELC'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Hero Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-700 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-72 h-72 bg-white opacity-5 rounded-full -mr-36 -mt-36"></div>
                <div class="absolute bottom-0 left-0 w-56 h-56 bg-white opacity-5 rounded-full -ml-28 -mb-28"></div>
                <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-white opacity-5 rounded-full"></div>

                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-14 h-14 rounded-xl bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold">{{ __('لوحة توافق معايير NELC') }}</h1>
                                    <p class="text-green-100 text-lg">{{ __('المركز الوطني للتعليم الإلكتروني') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Compliance Score -->
                        <div class="mt-4 md:mt-0">
                            <div class="bg-white bg-opacity-20 rounded-2xl p-6 backdrop-blur-sm text-center">
                                <div class="text-5xl font-bold mb-1">{{ $complianceScore }}%</div>
                                <div class="text-green-100 text-sm">{{ __('نسبة التوافق') }}</div>
                                <div class="mt-2 flex items-center justify-center gap-4 text-xs">
                                    <span class="flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-green-300"></span>
                                        {{ $implemented }} {{ __('مكتمل') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-yellow-300"></span>
                                        {{ $partial }} {{ __('جزئي') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-red-300"></span>
                                        {{ $total - $implemented - $partial }} {{ __('غير مكتمل') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-4 text-center hover:shadow-xl transition-shadow">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_students'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('طالب') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4 text-center hover:shadow-xl transition-shadow">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['total_teachers'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('معلم') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4 text-center hover:shadow-xl transition-shadow">
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_programs'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('برنامج') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4 text-center hover:shadow-xl transition-shadow">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['total_sessions'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('جلسة') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4 text-center hover:shadow-xl transition-shadow">
                <div class="text-2xl font-bold text-teal-600">{{ $stats['xapi_sent'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('xAPI مرسل') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4 text-center hover:shadow-xl transition-shadow">
                <div class="text-2xl font-bold text-pink-600">{{ $stats['active_enrollments'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('تسجيل نشط') }}</div>
            </div>
        </div>

        <!-- Standards Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                    {{ __('جدول معايير NELC - جميع المعايير مع الروابط') }}
                </h2>
                <span class="text-white text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">{{ $total }} {{ __('معيار') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 border-gray-200">
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">{{ __('الرمز') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">{{ __('المعيار') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">{{ __('التصنيف') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">{{ __('الصفة') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">{{ __('الحالة') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">{{ __('الرابط') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">{{ __('ملاحظات') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $currentCategory = ''; @endphp
                        @foreach($standards as $standard)
                            @if($standard['category'] !== $currentCategory)
                                @php $currentCategory = $standard['category']; @endphp
                                <tr class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                    <td colspan="7" class="px-4 py-3">
                                        <span class="font-bold text-blue-800 text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1H8a3 3 0 00-3 3v1.5a1.5 1.5 0 01-3 0V6z" clip-rule="evenodd"></path><path d="M6 12a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H2h2a2 2 0 002-2v-2z"></path></svg>
                                            {{ $currentCategory }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-800">
                                        {{ $standard['code'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-medium max-w-xs">
                                    {{ $standard['title_ar'] }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs text-gray-500">{{ $standard['category'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($standard['type'] === 'إلزامي')
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $standard['type'] }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">{{ $standard['type'] }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($standard['status'] === 'implemented')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            {{ __('مكتمل') }}
                                        </span>
                                    @elseif($standard['status'] === 'partial')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            {{ __('جزئي') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                            {{ __('غير مكتمل') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($standard['link'] !== '#')
                                        <a href="{{ $standard['link'] }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-cyan-700 transition-all shadow-sm hover:shadow-md">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path><path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path></svg>
                                            {{ $standard['link_label'] }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">{{ $standard['link_label'] }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs">
                                    {{ $standard['notes'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Public Pages Quick Links -->
        <div class="mt-8 bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path></svg>
                    {{ __('الروابط المباشرة للصفحات العامة (NELC)') }}
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Policies -->
                    <a href="{{ route('nelc.policies.privacy') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all border border-blue-200">
                        <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('سياسة الخصوصية') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.7') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.academic-integrity') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition-all border border-green-200">
                        <div class="w-10 h-10 rounded-lg bg-green-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('النزاهة الأكاديمية') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.4') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.intellectual-property') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all border border-purple-200">
                        <div class="w-10 h-10 rounded-lg bg-purple-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('الملكية الفكرية') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.5') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.communication') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl hover:from-orange-100 hover:to-orange-200 transition-all border border-orange-200">
                        <div class="w-10 h-10 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path><path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('سياسة التواصل') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.10') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.attendance') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-teal-50 to-teal-100 rounded-xl hover:from-teal-100 hover:to-teal-200 transition-all border border-teal-200">
                        <div class="w-10 h-10 rounded-lg bg-teal-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('سياسة الحضور') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.9') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.assessment') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-xl hover:from-red-100 hover:to-red-200 transition-all border border-red-200">
                        <div class="w-10 h-10 rounded-lg bg-red-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('سياسة التقييم') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 2.4.2') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.ai-ethics') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl hover:from-indigo-100 hover:to-indigo-200 transition-all border border-indigo-200">
                        <div class="w-10 h-10 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('أخلاقيات الذكاء الاصطناعي') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.6') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.guides.student') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-cyan-50 to-cyan-100 rounded-xl hover:from-cyan-100 hover:to-cyan-200 transition-all border border-cyan-200">
                        <div class="w-10 h-10 rounded-lg bg-cyan-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('دليل الطالب') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.3.2') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.guides.teacher') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-amber-50 to-amber-100 rounded-xl hover:from-amber-100 hover:to-amber-200 transition-all border border-amber-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('دليل المعلم') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.3.2') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.open-content') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl hover:from-emerald-100 hover:to-emerald-200 transition-all border border-emerald-200">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('المحتوى المفتوح') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 2.1.5') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.technical-support') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-rose-50 to-rose-100 rounded-xl hover:from-rose-100 hover:to-rose-200 transition-all border border-rose-200">
                        <div class="w-10 h-10 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.078-2.183L2.413 7.372A5.98 5.98 0 004 10c0 .9.198 1.751.552 2.516l1.606-1.399z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('الدعم الفني') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.3.3') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('nelc.policies.national-compliance') }}" target="_blank" class="flex items-center gap-3 p-4 bg-gradient-to-r from-lime-50 to-lime-100 rounded-xl hover:from-lime-100 hover:to-lime-200 transition-all border border-lime-200">
                        <div class="w-10 h-10 rounded-lg bg-lime-600 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ __('الالتزام بالأنظمة الوطنية') }}</div>
                            <div class="text-xs text-gray-500">{{ __('معيار 1.1.3') }}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
