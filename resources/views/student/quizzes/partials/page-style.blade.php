<style>
    .quiz-page { max-width: 1200px; margin-inline: auto; }
    .quiz-page > div:first-child { background: linear-gradient(120deg, #075985, #0071aa) !important; }
    .quiz-page .shadow-sm { border: 1px solid #e2e8f0; box-shadow: 0 4px 18px rgb(15 23 42 / 3%); }
    .quiz-page h1 { line-height: 1.6; overflow-wrap: anywhere; }
    .quiz-page p { line-height: 1.9; }
    .quiz-page a, .quiz-page button { transition: box-shadow .15s, background-color .15s; }
    .quiz-page a:focus-visible, .quiz-page button:focus-visible { outline: 3px solid #38bdf8; outline-offset: 4px; }
    .quiz-page table { min-width: 580px; }
    .quiz-page th { background: #f8fafc; white-space: nowrap; }
    .quiz-page td { vertical-align: middle; }
    .quiz-page tbody tr:hover { background: #f8fafc; }
    .quiz-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .quiz-metric { padding: 18px; border: 1px solid #e2e8f0; border-radius: 16px; background: white; }
    .quiz-metric dt { color: #64748b; font-size: 13px; margin-bottom: 8px; }
    .quiz-metric dd { color: #0c4a6e; font-size: 20px; font-weight: 700; }
    .quiz-review-note { display: flex; align-items: flex-start; gap: 14px; padding: 20px; border: 1px solid #fde68a; border-radius: 16px; background: #fffbeb; color: #78350f; }
    .quiz-review-note svg { width: 24px; height: 24px; flex-shrink: 0; margin-top: 3px; }
    .quiz-review-note p { margin-top: 5px; font-size: 14px; }
    .dark .quiz-page .shadow-sm, .dark .quiz-metric { border-color: #374151; }
    .dark .quiz-metric { background: #1f2937; }
    .dark .quiz-metric dt { color: #9ca3af; }
    .dark .quiz-metric dd { color: #7dd3fc; }
    .dark .quiz-page th, .dark .quiz-page tbody tr:hover { background: #111827; }
    .dark .quiz-review-note { background: #292318; color: #fde68a; border-color: #786326; }
    @media (max-width: 640px) {
        .quiz-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .quiz-metric { padding: 14px; }
        .quiz-page .p-6 { padding: 18px; }
        .quiz-page h1 { font-size: 22px; }
    }
</style>
