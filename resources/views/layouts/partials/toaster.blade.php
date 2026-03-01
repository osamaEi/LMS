{{-- ════════════════════════════════════════════════════════════════ --}}
{{--  GLOBAL TOASTER — included in dashboard.blade.php & front.blade.php --}}
{{-- ════════════════════════════════════════════════════════════════ --}}

<style>
/* ── Container ─────────────────────────────────────────────────── */
#toast-container {
    position: fixed;
    top: 1.25rem;
    right: 1.25rem;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: .6rem;
    pointer-events: none;
    max-width: 380px;
    width: calc(100vw - 2.5rem);
}

/* ── Individual Toast ───────────────────────────────────────────── */
.toast-item {
    pointer-events: all;
    display: flex;
    align-items: flex-start;
    gap: .9rem;
    padding: 1rem 1rem 1rem 1.1rem;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.08);
    background: rgba(15,23,42,.92);
    backdrop-filter: blur(20px) saturate(1.4);
    -webkit-backdrop-filter: blur(20px) saturate(1.4);
    box-shadow: 0 8px 32px rgba(0,0,0,.45), 0 2px 8px rgba(0,0,0,.3);
    position: relative;
    overflow: hidden;
    border-right: 4px solid transparent;
    transform: translateX(120%);
    opacity: 0;
    transition: transform .35s cubic-bezier(.34,1.28,.64,1), opacity .3s ease;
    will-change: transform, opacity;
    min-width: 280px;
}

.toast-item.show {
    transform: translateX(0);
    opacity: 1;
}

.toast-item.hide {
    transform: translateX(120%);
    opacity: 0;
    transition: transform .28s ease-in, opacity .25s ease-in;
}

/* ── Type colours ───────────────────────────────────────────────── */
.toast-success { border-right-color: #10b981; }
.toast-error   { border-right-color: #ef4444; }
.toast-warning { border-right-color: #f59e0b; }
.toast-info    { border-right-color: #3b82f6; }

.toast-success .toast-icon-wrap { background: rgba(16,185,129,.15); color:#10b981; }
.toast-error   .toast-icon-wrap { background: rgba(239,68,68,.15);  color:#ef4444; }
.toast-warning .toast-icon-wrap { background: rgba(245,158,11,.15); color:#f59e0b; }
.toast-info    .toast-icon-wrap { background: rgba(59,130,246,.15); color:#3b82f6; }

.toast-success .toast-progress { background: #10b981; }
.toast-error   .toast-progress { background: #ef4444; }
.toast-warning .toast-progress { background: #f59e0b; }
.toast-info    .toast-progress { background: #3b82f6; }

/* ── Icon ───────────────────────────────────────────────────────── */
.toast-icon-wrap {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-icon-wrap svg {
    width: 20px;
    height: 20px;
}

/* ── Body ───────────────────────────────────────────────────────── */
.toast-body {
    flex: 1;
    min-width: 0;
    padding-top: .05rem;
}

.toast-title {
    font-weight: 700;
    font-size: .875rem;
    color: #f1f5f9;
    margin-bottom: .2rem;
    line-height: 1.3;
    font-family: 'Cairo', sans-serif;
}

.toast-message {
    font-size: .8rem;
    color: #94a3b8;
    line-height: 1.5;
    word-break: break-word;
    font-family: 'Cairo', sans-serif;
}

/* ── Close button ───────────────────────────────────────────────── */
.toast-close {
    flex-shrink: 0;
    width: 26px;
    height: 26px;
    background: rgba(255,255,255,.06);
    border: none;
    border-radius: 7px;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s, color .15s;
    padding: 0;
    margin-top: -.1rem;
}

.toast-close:hover {
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
}

/* ── Progress bar ───────────────────────────────────────────────── */
.toast-progress-track {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: rgba(255,255,255,.06);
}

.toast-progress {
    height: 100%;
    width: 100%;
    transform-origin: left;
    transition: transform linear;
}
</style>

{{-- ── Container ──────────────────────────────────────────────────── --}}
<div id="toast-container" aria-live="polite"></div>

{{-- ── Icons SVG templates (hidden) ──────────────────────────────── --}}
<template id="tpl-icon-success">
    <svg fill="none" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" fill="rgba(16,185,129,.2)"/>
        <path d="M7 13l3 3 7-7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</template>
<template id="tpl-icon-error">
    <svg fill="none" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" fill="rgba(239,68,68,.2)"/>
        <path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
    </svg>
</template>
<template id="tpl-icon-warning">
    <svg fill="none" viewBox="0 0 24 24">
        <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</template>
<template id="tpl-icon-info">
    <svg fill="none" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" fill="rgba(59,130,246,.2)"/>
        <path d="M12 8h.01M12 12v4" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
    </svg>
</template>

<script>
(function () {
    'use strict';

    const DURATION = 5000; // ms before auto-dismiss
    const container = document.getElementById('toast-container');

    const LABELS = {
        success: 'تمّ بنجاح',
        error:   'حدث خطأ',
        warning: 'تحذير',
        info:    'معلومة',
    };

    /* ── Core function ─────────────────────────────────────────────── */
    function showToast(message, type = 'info', title = null, duration = DURATION) {
        type = ['success','error','warning','info'].includes(type) ? type : 'info';

        const iconTpl = document.getElementById(`tpl-icon-${type}`);
        const iconHtml = iconTpl ? iconTpl.innerHTML : '';

        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-icon-wrap">${iconHtml}</div>
            <div class="toast-body">
                <div class="toast-title">${title || LABELS[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="إغلاق">
                <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                    <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            <div class="toast-progress-track">
                <div class="toast-progress"></div>
            </div>
        `;

        container.appendChild(toast);

        // Trigger entrance animation
        requestAnimationFrame(() => {
            requestAnimationFrame(() => { toast.classList.add('show'); });
        });

        // Progress bar
        const bar = toast.querySelector('.toast-progress');
        bar.style.transition = `transform ${duration}ms linear`;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                bar.style.transform = 'scaleX(0)';
            });
        });

        // Auto-dismiss
        let timer = setTimeout(() => dismiss(toast), duration);

        // Pause on hover
        toast.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            bar.style.transition = 'none';
        });
        toast.addEventListener('mouseleave', () => {
            const remaining = parseFloat(getComputedStyle(bar).transform.split(',')[0].replace('matrix(','')) * duration;
            const leftMs = Math.max(remaining, 500);
            bar.style.transition = `transform ${leftMs}ms linear`;
            bar.style.transform = 'scaleX(0)';
            timer = setTimeout(() => dismiss(toast), leftMs);
        });

        // Close button
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timer);
            dismiss(toast);
        });

        return toast;
    }

    function dismiss(toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');
        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
    }

    /* ── Global API ────────────────────────────────────────────────── */
    window.toast = showToast;
    window.toast.success = (msg, title) => showToast(msg, 'success', title);
    window.toast.error   = (msg, title) => showToast(msg, 'error',   title);
    window.toast.warning = (msg, title) => showToast(msg, 'warning', title);
    window.toast.info    = (msg, title) => showToast(msg, 'info',    title);

    /* ── Fire session flash messages ───────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {

        @if(session('success'))
        showToast(@json(session('success')), 'success');
        @endif

        @if(session('error'))
        showToast(@json(session('error')), 'error');
        @endif

        @if(session('warning'))
        showToast(@json(session('warning')), 'warning');
        @endif

        @if(session('info'))
        showToast(@json(session('info')), 'info');
        @endif

        @if(session('message'))
        showToast(@json(session('message')), 'info');
        @endif

        @if($errors->any())
        const firstError = @json($errors->first());
        const moreCount  = {{ $errors->count() - 1 }};
        const msg = moreCount > 0 ? firstError + ' (و ' + moreCount + ' أخطاء أخرى)' : firstError;
        showToast(msg, 'error', 'خطأ في البيانات');
        @endif

    });
}());
</script>
