@extends('layouts.dashboard')

@section('title', 'البرامج التدريبي ة')

@push('styles')
<style>
    .programs-page { --primary: #059669; --primary-dark: #047857; --primary-light: #d1fae5; }

    .hero-header {
        background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
        position: relative; overflow: hidden;
    }
    .hero-header::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: slidePattern 25s linear infinite;
    }
    @keyframes slidePattern { 0% { transform: translateX(0); } 100% { transform: translateX(-60px); } }

    .float-shape { position: absolute; border-radius: 50%; background: rgba(255,255,255,.08); animation: floatAnim 8s ease-in-out infinite; }
    .float-shape:nth-child(1) { width:100px;height:100px;top:-20px;right:10%;animation-delay:0s; }
    .float-shape:nth-child(2) { width:60px;height:60px;bottom:-10px;right:25%;animation-delay:2s; }
    .float-shape:nth-child(3) { width:40px;height:40px;top:30%;left:5%;animation-delay:4s; }
    @keyframes floatAnim { 0%,100%{transform:translateY(0) rotate(0);opacity:.6} 50%{transform:translateY(-15px) rotate(5deg);opacity:1} }

    .stat-card { transition: all .4s cubic-bezier(.4,0,.2,1); position: relative; overflow: hidden; }
    .stat-card::before { content:''; position:absolute; top:0;left:0;right:0; height:4px; background:var(--card-accent); transform:scaleX(0); transform-origin:left; transition:transform .4s ease; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 25px 50px -12px rgba(5,150,105,.2); }
    .stat-card:hover::before { transform: scaleX(1); }

    .data-table { border-collapse: separate; border-spacing: 0; }
    .data-table thead th { position: sticky; top: 0; z-index: 10; }
    .table-row { transition: all .2s ease; }
    .table-row:hover { background: linear-gradient(90deg, rgba(5,150,105,.03) 0%, rgba(5,150,105,.08) 50%, rgba(5,150,105,.03) 100%); }
    .table-row:hover td:first-child { border-right-color: #059669; }

    .action-btn { transition: all .2s ease; position: relative; overflow: hidden; }
    .action-btn::before { content:''; position:absolute;inset:0;background:currentColor;opacity:0;transition:opacity .2s; }
    .action-btn:hover::before { opacity:.1; }
    .action-btn:hover { transform: scale(1.1); }

    .status-badge.active::before {
        content:''; position:absolute; top:50%;left:8px; transform:translateY(-50%);
        width:6px;height:6px;background:currentColor;border-radius:50%;animation:pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1;transform:translateY(-50%) scale(1)} 50%{opacity:.5;transform:translateY(-50%) scale(1.2)} }

    .search-input:focus { box-shadow: 0 0 0 3px rgba(5,150,105,.1); }
    .btn-shine { position: relative; overflow: hidden; }
    .btn-shine::after { content:''; position:absolute;top:0;left:-100%;width:100%;height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent); transition:left .5s; }
    .btn-shine:hover::after { left: 100%; }

    .custom-checkbox { appearance:none;width:18px;height:18px;border:2px solid #d1d5db;border-radius:4px;cursor:pointer;transition:all .2s;position:relative; }
    .custom-checkbox:checked { background:linear-gradient(135deg,#059669,#047857);border-color:#059669; }
    .custom-checkbox:checked::after { content:'';position:absolute;left:5px;top:2px;width:5px;height:9px;border:solid white;border-width:0 2px 2px 0;transform:rotate(45deg); }
    .custom-checkbox:hover { border-color: #059669; }

    .empty-icon { animation: bounceLight 3s ease-in-out infinite; }
    @keyframes bounceLight { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
</style>
@endpush

@section('content')
<div class="programs-page space-y-6">

    <!-- Hero Header -->
    <div class="hero-header rounded-3xl p-8 shadow-xl relative">
        <div class="float-shape"></div><div class="float-shape"></div><div class="float-shape"></div>
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-white/10 backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">البرامج التدريبي ة</h1>
                        <p class="mt-1 text-white/70">إدارة وتنظيم جميع البرامج التدريبي ة</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.training-programs.create') }}"
                       class="btn-shine inline-flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-white text-[#059669] font-bold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة برنامج جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--card-accent:#059669;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي البرامج</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">جميع البرامج في النظام</p>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:linear-gradient(135deg,#059669,#047857);">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--card-accent:#10b981;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">برامج نشطة</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['active'] }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 mt-2">
                        {{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:linear-gradient(135deg,#10b981,#047857);">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--card-accent:#6b7280;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">برامج غير نشطة</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['inactive'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">بحاجة للتفعيل</p>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:linear-gradient(135deg,#6b7280,#4b5563);">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:linear-gradient(135deg,#059669,#047857);">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">قائمة البرامج التدريبي ة</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $programs->total() }} برنامج تدريبي</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="بحث في البرامج..."
                               class="search-input w-64 pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:border-[#059669] transition-all"
                               id="searchInput">
                    </div>
                    <select class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:border-[#059669] transition-all" id="statusFilter">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
            </div>
        </div>

        @if($programs->count() > 0)
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-4 text-right"><input type="checkbox" class="custom-checkbox" id="selectAll"></th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">البرنامج</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الرمز</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">المدة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الأرباع</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($programs as $program)
                    <tr class="table-row" data-status="{{ $program->status }}" data-name="{{ $program->name_ar }} {{ $program->name_en }}">
                        <td class="px-6 py-5 border-r-4 border-transparent">
                            <input type="checkbox" class="custom-checkbox row-checkbox" value="{{ $program->id }}">
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                @if($program->image)
                                <img src="{{ asset('storage/'.$program->image) }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0" alt="">
                                @else
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#05996920,#04785720);">
                                    <svg class="w-6 h-6" style="color:#059669;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $program->name_ar }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $program->name_en }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-700">
                                {{ $program->code ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm text-gray-700 font-medium">
                                {{ $program->duration_months ? $program->duration_months . ' شهر' : '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm text-gray-700 font-medium">
                                @if($program->price)
                                    {{ number_format($program->price) }} <span class="text-gray-400 font-normal"><x-riyal /></span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#d1fae5;">
                                    <span class="text-sm font-bold" style="color:#047857;">{{ $program->terms_count }}</span>
                                </div>
                                <span class="text-xs text-gray-500">ربع</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($program->status === 'active')
                                <span class="status-badge active inline-flex items-center ps-5 pe-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">نشط</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.training-programs.show', $program) }}"
                                   class="action-btn w-9 h-9 rounded-lg flex items-center justify-center text-emerald-600 hover:bg-emerald-50" title="عرض">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.training-programs.edit', $program) }}"
                                   class="action-btn w-9 h-9 rounded-lg flex items-center justify-center text-amber-600 hover:bg-amber-50" title="تعديل">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.training-programs.destroy', $program) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا البرنامج؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn w-9 h-9 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50" title="حذف">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $programs->links() }}
        </div>
        @endif

        @else
        <div class="p-16 text-center">
            <div class="empty-icon relative w-32 h-32 mx-auto mb-8">
                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200"></div>
                <div class="absolute inset-4 rounded-full bg-white flex items-center justify-center shadow-inner">
                    <svg class="w-12 h-12 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">لا توجد برامج تدريبية بعد</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">ابدأ بإضافة برنامج تدريبي جديد.</p>
            <a href="{{ route('admin.training-programs.create') }}"
               class="btn-shine inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-bold"
               style="background:linear-gradient(135deg,#059669,#047857);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة برنامج جديد
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const searchInput  = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows    = document.querySelectorAll('.table-row');

    function filterTable() {
        const search = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        tableRows.forEach(row => {
            const name   = row.dataset.name.toLowerCase();
            const rowSt  = row.dataset.status;
            row.style.display = (name.includes(search) && (!status || rowSt === status)) ? '' : 'none';
        });
    }
    searchInput?.addEventListener('input', filterTable);
    statusFilter?.addEventListener('change', filterTable);

    const selectAll = document.getElementById('selectAll');
    selectAll?.addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
@endsection
