@extends('layouts.dashboard')

@section('title', 'إضافة صلاحيات متعددة')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">الرئيسية</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.permissions.index') }}" class="hover:text-blue-600">الصلاحيات</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 dark:text-white">إضافة متعددة</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة صلاحيات متعددة</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">أضف عدة صلاحيات دفعة واحدة</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.permissions.bulk-store') }}" method="POST">
                @csrf

                <!-- Permissions Textarea -->
                <div class="mb-6">
                    <label for="permissions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الصلاحيات <span class="text-red-500">*</span>
                    </label>
                    <textarea name="permissions"
                              id="permissions"
                              rows="10"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-sm"
                              placeholder="view-users
create-users
edit-users
delete-users
manage-roles
manage-permissions"
                              required>{{ old('permissions') }}</textarea>
                    @error('permissions')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        أدخل كل صلاحية في سطر منفصل. سيتم تجاهل الصلاحيات الموجودة مسبقاً.
                    </p>
                </div>

                <!-- Quick Add Templates -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        قوالب سريعة
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="addTemplate('users')"
                                class="px-4 py-2 text-sm bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg transition">
                            إدارة المستخدمين
                        </button>
                        <button type="button" onclick="addTemplate('courses')"
                                class="px-4 py-2 text-sm bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg transition">
                            إدارة الدورات
                        </button>
                        <button type="button" onclick="addTemplate('sessions')"
                                class="px-4 py-2 text-sm bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/20 dark:hover:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-lg transition">
                            إدارة الجلسات
                        </button>
                        <button type="button" onclick="addTemplate('reports')"
                                class="px-4 py-2 text-sm bg-orange-50 hover:bg-orange-100 dark:bg-orange-900/20 dark:hover:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-lg transition">
                            التقارير
                        </button>
                        <button type="button" onclick="addTemplate('settings')"
                                class="px-4 py-2 text-sm bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                            الإعدادات
                        </button>
                        <button type="button" onclick="addTemplate('all')"
                                class="px-4 py-2 text-sm bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg transition">
                            الكل
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        إنشاء الصلاحيات
                    </button>
                    <a href="{{ route('admin.permissions.index') }}"
                       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const templates = {
    users: `view-users
create-users
edit-users
delete-users`,
    courses: `view-courses
create-courses
edit-courses
delete-courses
manage-enrollments`,
    sessions: `view-sessions
create-sessions
edit-sessions
delete-sessions
manage-attendance`,
    reports: `view-reports
export-reports
view-analytics`,
    settings: `view-settings
edit-settings
manage-system`,
    all: `view-users
create-users
edit-users
delete-users
view-courses
create-courses
edit-courses
delete-courses
manage-enrollments
view-sessions
create-sessions
edit-sessions
delete-sessions
manage-attendance
view-reports
export-reports
view-analytics
view-settings
edit-settings
manage-system
manage-roles
manage-permissions`
};

function addTemplate(type) {
    const textarea = document.getElementById('permissions');
    const currentValue = textarea.value.trim();
    const newValue = currentValue ? currentValue + '\n' + templates[type] : templates[type];
    textarea.value = newValue;
}
</script>
@endsection
