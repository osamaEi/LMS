@extends('layouts.dashboard')

@section('title', 'إدارة الأدوار')

@push('styles')
<style>
.roles-page { max-width: 1200px; margin: 0 auto; }

.roles-header {
    background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
    border-radius: 24px; padding: 2rem 2.5rem; color: #fff;
    position: relative; overflow: hidden; margin-bottom: 1.5rem;
}
.roles-header::before {
    content:''; position:absolute; top:-40%; right:-10%;
    width:280px; height:280px;
    background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 70%);
    border-radius:50%;
}

.roles-stat {
    background: rgba(255,255,255,0.12); border-radius: 14px;
    padding: .75rem 1.25rem; text-align: center;
}
.roles-stat .sv { font-size: 1.4rem; font-weight: 800; }
.roles-stat .sl { font-size: .72rem; opacity: .8; }

.roles-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.06);
    overflow: hidden;
}
.dark .roles-card { background: #1f2937; }

.roles-table { width: 100%; border-collapse: collapse; }
.roles-table thead th {
    padding: .85rem 1.25rem;
    text-align: right; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #6b7280; background: #f9fafb;
    border-bottom: 2px solid #f1f5f9; white-space: nowrap;
}
.dark .roles-table thead th { background: #111827; color: #9ca3af; border-color: #374151; }
.roles-table thead th.center { text-align: center; }
.roles-table tbody tr {
    border-bottom: 1px solid #f8fafc; transition: background .15s;
}
.dark .roles-table tbody tr { border-color: #374151; }
.roles-table tbody tr:hover { background: #f0f9ff; }
.dark .roles-table tbody tr:hover { background: rgba(0,113,170,.06); }
.roles-table tbody tr:last-child { border-bottom: none; }
.roles-table td { padding: 1rem 1.25rem; vertical-align: middle; font-size: .875rem; }
.roles-table td.center { text-align: center; }

.role-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 12px;
    background: linear-gradient(135deg, #0071AA, #004d77);
    color: #fff; font-weight: 800; font-size: 1rem; flex-shrink: 0;
}

.perm-chip {
    display: inline-block; font-size: .68rem; font-weight: 700;
    padding: .18rem .55rem; border-radius: 6px;
    background: #e0f2fe; color: #0369a1; margin: .1rem;
}
.dark .perm-chip { background: rgba(0,113,170,.2); color: #38bdf8; }

.count-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 28px; height: 28px; padding: 0 .5rem;
    border-radius: 8px; font-size: .8rem; font-weight: 800;
    background: #e0f2fe; color: #0071AA;
}
.dark .count-badge { background: rgba(0,113,170,.2); color: #38bdf8; }

.btn-action {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem .9rem; border-radius: 10px; font-size: .8rem;
    font-weight: 700; text-decoration: none; transition: opacity .2s;
    white-space: nowrap;
}
.btn-action:hover { opacity: .85; }
.btn-view { background: #0071AA; color: #fff; }
.btn-edit { background: #10b981; color: #fff; }
.btn-del  { background: #ef4444; color: #fff; border: none; cursor: pointer; }

.alert-box { padding: .85rem 1.25rem; border-radius: 12px; font-size: .9rem; font-weight: 600; margin-bottom: 1rem; }
.alert-success { background: #ecfdf5; color: #065f46; }
.alert-error   { background: #fef2f2; color: #991b1b; }
</style>
@endpush

@section('content')
<div class="roles-page space-y-5">

    {{-- Header --}}
    <div class="roles-header">
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
                <div style="width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:26px;height:26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <h1 style="font-size:1.6rem;font-weight:800;line-height:1.2;">إدارة الأدوار والصلاحيات</h1>
                    <p style="opacity:.75;font-size:.9rem;margin-top:.2rem;">تحكم في أدوار المستخدمين وصلاحياتهم</p>
                </div>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.permissions.index') }}"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.2rem;border-radius:12px;background:rgba(255,255,255,0.15);font-size:.875rem;font-weight:700;text-decoration:none;color:#fff;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        الصلاحيات
                    </a>
                    <a href="{{ route('admin.roles.create') }}"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.2rem;border-radius:12px;background:rgba(255,255,255,0.22);font-size:.875rem;font-weight:700;text-decoration:none;color:#fff;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        دور جديد
                    </a>
                </div>
            </div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                <div class="roles-stat">
                    <div class="sv">{{ $totalRoles }}</div>
                    <div class="sl">إجمالي الأدوار</div>
                </div>
                <div class="roles-stat">
                    <div class="sv">{{ $totalPermissions }}</div>
                    <div class="sl">إجمالي الصلاحيات</div>
                </div>
                <div class="roles-stat">
                    <div class="sv">{{ $rolesWithUsers }}</div>
                    <div class="sl">أدوار مستخدمة</div>
                </div>
                <div class="roles-stat">
                    <div class="sv">{{ number_format($avgPermissionsPerRole ?? 0, 1) }}</div>
                    <div class="sl">متوسط الصلاحيات</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-box alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-box alert-error">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="roles-card">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;">
            <div style="width:32px;height:32px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
                <svg style="width:16px;height:16px;color:#0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span style="font-weight:700;font-size:.9rem;color:#111827;" class="dark:text-white">قائمة الأدوار</span>
            <span style="font-size:.75rem;color:#6b7280;margin-right:auto;">{{ $roles->total() }} دور</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="roles-table">
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>الدور</th>
                        <th class="center">الصلاحيات</th>
                        <th class="center">المستخدمون</th>
                        <th>أبرز الصلاحيات</th>
                        <th class="center">تاريخ الإنشاء</th>
                        <th class="center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                    <tr>
                        <td>
                            <div class="role-badge">{{ mb_substr($role->name, 0, 1) }}</div>
                        </td>
                        <td>
                            <div style="font-weight:700;color:#111827;" class="dark:text-white">{{ $role->name }}</div>
                            <div style="font-size:.75rem;color:#9ca3af;margin-top:.2rem;">{{ $role->guard_name }}</div>
                        </td>
                        <td class="center">
                            <span class="count-badge">{{ $role->permissions->count() }}</span>
                        </td>
                        <td class="center">
                            @php $userCount = $role->users()->count(); @endphp
                            <span class="count-badge" style="background:#ecfdf5;color:#059669;">{{ $userCount }}</span>
                        </td>
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:.2rem;max-width:300px;">
                                @foreach($role->permissions->take(5) as $perm)
                                    <span class="perm-chip">{{ \App\Helpers\PermissionHelper::translatePermission($perm->name) }}</span>
                                @endforeach
                                @if($role->permissions->count() > 5)
                                    <span class="perm-chip" style="background:#f1f5f9;color:#6b7280;">+{{ $role->permissions->count() - 5 }}</span>
                                @endif
                                @if($role->permissions->isEmpty())
                                    <span style="font-size:.78rem;color:#d1d5db;font-style:italic;">لا توجد صلاحيات</span>
                                @endif
                            </div>
                        </td>
                        <td class="center">
                            <div style="font-size:.8rem;font-weight:600;color:#374151;" class="dark:text-gray-300">{{ $role->created_at->format('d/m/Y') }}</div>
                            <div style="font-size:.72rem;color:#9ca3af;">{{ $role->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="center">
                            <div style="display:flex;align-items:center;justify-content:center;gap:.5rem;flex-wrap:wrap;">
                                <a href="{{ route('admin.roles.show', $role) }}" class="btn-action btn-view">
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    عرض
                                </a>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn-action btn-edit">
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    تعديل
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-del">
                                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:4rem 1rem;">
                            <div style="width:64px;height:64px;border-radius:18px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                <svg style="width:32px;height:32px;color:#0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <p style="color:#6b7280;font-weight:600;">لا توجد أدوار حتى الآن</p>
                            <a href="{{ route('admin.roles.create') }}" style="display:inline-flex;align-items:center;gap:.5rem;margin-top:1rem;padding:.65rem 1.4rem;border-radius:12px;background:#0071AA;color:#fff;font-weight:700;font-size:.875rem;text-decoration:none;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                إنشاء دور جديد
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid #f1f5f9;">
            {{ $roles->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
