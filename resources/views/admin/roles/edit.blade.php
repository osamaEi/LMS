@extends('layouts.dashboard')

@section('title', 'تعديل الدور: ' . $role->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">الرئيسية</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.roles.index') }}" class="hover:text-blue-600">الأدوار</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 dark:text-white">تعديل: {{ $role->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الدور</h1>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Role Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        اسم الدور <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $role->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Permissions -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            الصلاحيات
                        </label>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectAll()" class="text-xs text-blue-600 hover:underline">تحديد الكل</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="deselectAll()" class="text-xs text-red-600 hover:underline">إلغاء التحديد</button>
                        </div>
                    </div>

                    @if($permissions->count() > 0)
                    <div class="space-y-4 max-h-[600px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        @foreach($groupedPermissions as $key => $group)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $group['name'] }}
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3">
                                @foreach($group['permissions'] as $permission)
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                           class="permission-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ \App\Helpers\PermissionHelper::getPermissionColor($permission->name) }}">
                                        {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-700 dark:text-yellow-400">
                            لا توجد صلاحيات متاحة.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
                       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
}
function deselectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
}
</script>
@endsection
