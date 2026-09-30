{{-- Styles + scripts shared by offer cards. Include once per page. --}}
<style>
    .oc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 380px));
        justify-content: center;
        gap: 1.5rem;
        max-width: 1280px;
        margin: 0 auto;
    }
    @media (max-width: 400px) { .oc-grid { grid-template-columns: 1fr; } }

    /* Colour tokens — any element with an oc--{type} class (cards, the offer page) */
    .oc, .oc--pct { --oc-c1: #0071AA; --oc-c2: #0ea5e9; --oc-soft: #eaf5fb; }
    .oc {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
        display: flex;
        flex-direction: column;
        transition: transform .25s, box-shadow .25s;
    }
    .oc:hover { transform: translateY(-4px); box-shadow: 0 14px 34px rgba(15, 23, 42, .12); }
    .oc--fix { --oc-c1: #059669; --oc-c2: #34d399; --oc-soft: #ecfdf5; }
    .oc--ovr { --oc-c1: #7c3aed; --oc-c2: #a78bfa; --oc-soft: #f5f3ff; }
    .oc.is-expired { opacity: .6; filter: grayscale(.4); }

    /* Media */
    .oc-media {
        position: relative;
        display: block;
        aspect-ratio: 16 / 9;
        background: #0f172a;
        overflow: hidden;
        flex-shrink: 0;
    }
    .oc-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
    .oc:hover .oc-media img { transform: scale(1.04); }
    .oc-media iframe, .oc-media video {
        position: absolute; inset: 0; width: 100%; height: 100%; border: 0; background: #000; object-fit: contain;
    }
    .oc-media--hero {
        background: linear-gradient(135deg, var(--oc-c1), var(--oc-c2));
        color: #fff;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .oc-media--hero::after {
        content: ''; position: absolute; width: 220px; height: 220px; border-radius: 50%;
        background: rgba(255,255,255,.08); top: -70px; left: -60px;
    }
    .oc-play {
        position: absolute; bottom: 12px; left: 12px; z-index: 1;
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255,255,255,.92); color: var(--oc-c1);
        display: flex; align-items: center; justify-content: center; font-size: 1.35rem;
        box-shadow: 0 4px 14px rgba(0,0,0,.2); transition: transform .2s;
    }
    [dir="ltr"] .oc-play { left: auto; right: 12px; }
    .oc:hover .oc-play { transform: scale(1.1); }
    .oc-hero-num { font-size: 4rem; font-weight: 900; line-height: 1; text-shadow: 0 4px 16px rgba(0,0,0,.15); }
    .oc-hero-num small { font-size: 1.4rem; font-weight: 800; margin-inline-start: .25rem; }
    .oc-hero-lbl { font-size: .85rem; font-weight: 700; opacity: .85; margin-top: .35rem; }

    /* Strip */
    .oc-strip {
        display: flex; align-items: center; justify-content: space-between; gap: .5rem;
        padding: .6rem 1.1rem;
        background: var(--oc-soft);
        border-bottom: 1px solid #eef2f7;
    }
    .oc-chip { display: inline-flex; align-items: center; gap: .35rem; font-size: .82rem; font-weight: 700; color: var(--oc-c1); }
    .oc-chip strong { font-weight: 900; font-size: .95rem; }
    .oc-status { display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 800; padding: .2rem .6rem; border-radius: 999px; white-space: nowrap; }
    .st-active   { background: #dcfce7; color: #15803d; }
    .st-expired  { background: #fee2e2; color: #dc2626; }
    .st-upcoming { background: #dbeafe; color: #1d4ed8; }
    .oc-dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 0 0 rgba(34,197,94,.6); animation: ocPulse 1.6s infinite; }
    @keyframes ocPulse { 70% { box-shadow: 0 0 0 7px rgba(34,197,94,0); } 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); } }

    /* Body */
    .oc-body { padding: 1rem 1.1rem .5rem; display: flex; flex-direction: column; gap: .6rem; flex: 1; }
    .oc-title { font-size: 1.12rem; font-weight: 800; line-height: 1.5; margin: 0; }
    .oc-title a { color: #0f172a; text-decoration: none; }
    .oc-title a:hover { color: var(--oc-c1); }
    .oc-programs { display: flex; flex-wrap: wrap; gap: .4rem; }
    .oc-prog {
        display: inline-flex; align-items: center; gap: .35rem; max-width: 100%;
        font-size: .78rem; font-weight: 700; color: #334155; text-decoration: none;
        background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: .28rem .6rem; transition: all .15s;
    }
    .oc-prog i { color: var(--oc-c1); }
    .oc-prog:hover { background: var(--oc-soft); border-color: var(--oc-c1); color: var(--oc-c1); }
    .oc-prog--all { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .oc-prog--all i { color: #047857; }
    .oc-prog--more { color: var(--oc-c1); background: var(--oc-soft); border-style: dashed; }
    .oc-price-from { font-size: .75rem; font-weight: 700; color: #64748b; }
    .oc-desc {
        font-size: .85rem; color: #475569; line-height: 1.8; margin: 0;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }

    .oc-price { display: flex; align-items: baseline; flex-wrap: wrap; gap: .5rem; }
    .oc-price-new { font-size: 1.35rem; font-weight: 900; color: var(--oc-c1); }
    .oc-price-old { font-size: .9rem; color: #94a3b8; text-decoration: line-through; }
    .oc-price-save { font-size: .72rem; font-weight: 800; color: #b45309; background: #fef3c7; padding: .15rem .5rem; border-radius: 6px; }

    .oc-countdown {
        display: flex; align-items: center; gap: .3rem; flex-wrap: wrap;
        font-size: .8rem; font-weight: 700; color: #9a3412;
        background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: .45rem .7rem;
    }
    .oc-countdown b { font-size: .95rem; font-weight: 900; font-variant-numeric: tabular-nums; }
    .oc-countdown small { font-size: .72rem; font-weight: 700; }
    .oc-countdown > span { margin-inline-end: .2rem; }
    .oc-countdown--soon { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
    .oc-countdown--open { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }

    .oc-code {
        display: flex; align-items: center; gap: .5rem;
        border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: .35rem .5rem .35rem .75rem; background: #f8fafc;
    }
    .oc-code-lbl { font-size: .68rem; font-weight: 800; color: #94a3b8; white-space: nowrap; }
    .oc-code code { flex: 1; font-family: 'Courier New', monospace; font-size: .92rem; font-weight: 900; letter-spacing: 2px; color: #0f172a; }
    .oc-copy {
        width: 32px; height: 32px; border-radius: 8px; border: none; background: #fff; color: #64748b;
        box-shadow: 0 1px 3px rgba(0,0,0,.1); cursor: pointer; transition: all .18s;
    }
    .oc-copy:hover  { background: var(--oc-c1); color: #fff; }
    .oc-copy.copied { background: #10b981; color: #fff; }

    .oc-meta { display: flex; flex-wrap: wrap; gap: .4rem 1rem; font-size: .76rem; color: #64748b; font-weight: 600; margin-top: auto; padding-top: .3rem; }
    .oc-meta i { color: #94a3b8; margin-inline-end: .2rem; }

    /* Actions */
    .oc-actions { display: grid; grid-template-columns: 1fr 1.6fr; gap: .6rem; padding: .75rem 1.1rem 1.1rem; }
    .oc-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        padding: .65rem .75rem; border-radius: 11px; font-size: .9rem; font-weight: 800; text-decoration: none; transition: all .18s;
    }
    .oc-btn--ghost { border: 1.5px solid #e2e8f0; color: #334155; background: #fff; }
    .oc-btn--ghost:hover { border-color: var(--oc-c1); color: var(--oc-c1); }
    .oc-btn--primary { background: linear-gradient(135deg, var(--oc-c1), var(--oc-c2)); color: #fff; }
    .oc-btn--primary:hover { color: #fff; box-shadow: 0 8px 20px rgba(0,0,0,.15); transform: translateY(-1px); }
    .oc-btn--disabled { background: #e5e7eb; color: #6b7280; cursor: not-allowed; }
</style>

<script>
function copyOfferCode(btn, code) {
    navigator.clipboard.writeText(code).then(() => {
        const ico = btn.querySelector('i');
        btn.classList.add('copied');
        ico.className = 'bi bi-clipboard-check';
        setTimeout(() => { btn.classList.remove('copied'); ico.className = 'bi bi-clipboard'; }, 2000);
    });
}

function updateOfferCountdowns() {
    document.querySelectorAll('[data-end]').forEach(el => {
        const diff = new Date(el.dataset.end).getTime() - Date.now();
        if (diff <= 0) { el.innerHTML = '<i class="bi bi-clock-history"></i> <span>انتهى العرض</span>'; el.removeAttribute('data-end'); return; }
        const p = n => String(Math.floor(n)).padStart(2, '0');
        const set = (cls, v) => el.querySelectorAll('.' + cls).forEach(x => x.textContent = v);
        set('cd-days',  Math.floor(diff / 86400000));
        set('cd-hours', p((diff % 86400000) / 3600000));
        set('cd-mins',  p((diff % 3600000) / 60000));
        set('cd-secs',  p((diff % 60000) / 1000));
    });
}
document.addEventListener('DOMContentLoaded', () => { updateOfferCountdowns(); setInterval(updateOfferCountdowns, 1000); });
</script>
