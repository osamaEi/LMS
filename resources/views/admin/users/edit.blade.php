@extends('layouts.dashboard')

@section('title', 'تعديل المستخدم: ' . $user->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">الرئيسية</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">المستخدمين</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 dark:text-white">تعديل: {{ $user->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل المستخدم</h1>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الاسم <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               required>
                        @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            رقم الهاتف
                        </label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Type -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            نوع المستخدم <span class="text-red-500">*</span>
                        </label>
                        <select name="role"
                                id="role"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مدير</option>
                            <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>مدير عام</option>
                        </select>
                        @error('role')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                                id="status"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>موقوف</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            كلمة المرور الجديدة
                        </label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="اتركها فارغة للحفاظ على الحالية">
                        @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            تأكيد كلمة المرور
                        </label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Spatie Roles -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        الأدوار والصلاحيات
                    </label>
                    @if($roles->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($roles as $role)
                        <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:shadow-md transition border-2 border-transparent hover:border-indigo-200 dark:hover:border-indigo-700 {{ \App\Helpers\PermissionHelper::getRoleColor($role->name) }}">
                            <input type="checkbox"
                                   name="spatie_roles[]"
                                   value="{{ $role->id }}"
                                   {{ in_array($role->id, old('spatie_roles', $userRoles)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-sm font-semibold">{{ \App\Helpers\PermissionHelper::translateRole($role->name) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        لا توجد أدوار. <a href="{{ route('admin.roles.create') }}" class="text-blue-600 hover:underline">أنشئ دوراً</a> أولاً.
                    </p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-start gap-4 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="px-8 py-3 bg-brand-500 hover:bg-brand-600 text-white font-medium rounded-lg transition shadow-md">
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-8 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
