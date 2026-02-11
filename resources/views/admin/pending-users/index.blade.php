@extends('layouts.admin')

@section('title', 'طلبات التسجيل المعلقة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">طلبات التسجيل المعلقة</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">مراجعة وقبول طلبات التسجيل الجديدة</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg font-bold">
                {{ $users->total() }} طلب معلق
            </span>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.pending-users.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="بحث بالاسم، البريد، الجوال، رقم الهوية..."
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                بحث
            </button>
            @if(request('search'))
                <a href="{{ route('admin.pending-users.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-form" method="POST" class="space-y-4">
        @csrf

        <!-- Bulk Actions Bar -->
        <div id="bulk-actions-bar" class="hidden bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-900 dark:text-blue-300">
                    تم تحديد <span id="selected-count">0</span> طالب
                </span>
                <div class="flex gap-2">
                    <button type="button" onclick="bulkApprove()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                        قبول المحددين
                    </button>
                    <button type="button" onclick="bulkReject()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                        رفض المحددين
                    </button>
                </div>
            </div>
        </div>

        <!-- Pending Users List -->
        @if($users->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات معلقة</h3>
                <p class="text-gray-600 dark:text-gray-400">جميع طلبات التسجيل تمت مراجعتها</p>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-right">
                                    <input type="checkbox" id="select-all"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           onchange="toggleSelectAll(this)">
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الاسم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">البريد الإلكتروني</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الجوال</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">رقم الهوية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">تاريخ التسجيل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                               class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                               onchange="updateBulkActions()">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ substr($user->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->role }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $user->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $user->national_id ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.pending-users.show', $user) }}"
                                               class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                                                عرض
                                            </a>
                                            <form action="{{ route('admin.pending-users.approve', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('هل أنت متأكد من قبول هذا المستخدم؟')"
                                                        class="px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium transition-colors">
                                                    قبول
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.pending-users.reject', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('هل أنت متأكد من رفض وحذف هذا المستخدم؟')"
                                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium transition-colors">
                                                    رفض
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </form>
</div>

<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkboxes.length;
        const bulkBar = document.getElementById('bulk-actions-bar');
        const countSpan = document.getElementById('selected-count');

        if (count > 0) {
            bulkBar.classList.remove('hidden');
            countSpan.textContent = count;
        } else {
            bulkBar.classList.add('hidden');
        }
    }

    function bulkApprove() {
        if (!confirm('هل أنت متأكد من قبول المستخدمين المحددين؟')) {
            return;
        }

        const form = document.getElementById('bulk-form');
        form.action = '{{ route("admin.pending-users.bulk-approve") }}';
        form.submit();
    }

    function bulkReject() {
        if (!confirm('هل أنت متأكد من رفض وحذف المستخدمين المحددين؟')) {
            return;
        }

        const form = document.getElementById('bulk-form');
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        form.action = '{{ route("admin.pending-users.bulk-reject") }}';
        form.submit();
    }
</script>
@endsection
