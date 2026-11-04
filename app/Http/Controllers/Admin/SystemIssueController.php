<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Session;
use App\Models\Subject;
use App\Models\SystemIssue;
use App\Models\SystemIssueAttachment;
use App\Models\SystemIssueComment;
use App\Models\SystemIssueTag;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SystemIssueController extends Controller
{
    /** Browsers record webm/ogg (Chrome, Firefox) or mp4 (Safari); uploads may be mp3/wav/m4a. */
    private const VOICE_RULE = 'nullable|file|max:20480|mimetypes:audio/webm,video/webm,audio/ogg,video/ogg,audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/wave,audio/mp4,video/mp4,audio/x-m4a,audio/m4a,audio/aac,audio/3gpp';

    /** Allowed attachment types (by extension). Anything executable or scriptable (php, exe, html, svg, js...) is refused. */
    private const ATTACHMENT_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic', 'heif',
        'mp4', 'webm', 'mov', 'm4v', 'ogv', 'mkv', 'avi', '3gp',
        'mp3', 'wav', 'm4a', 'ogg', 'aac',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp',
        'zip', 'rar', '7z',
    ];
    private const MAX_ATTACHMENTS = 10;
    private const MAX_ATTACHMENT_KB = 102400; // 100 MB per file

    private function attachmentRules(): array
    {
        return [
            'attachments'   => 'nullable|array|max:' . self::MAX_ATTACHMENTS,
            'attachments.*' => 'file|max:' . self::MAX_ATTACHMENT_KB . '|extensions:' . implode(',', self::ATTACHMENT_EXTENSIONS),
        ];
    }

    private function attachmentMessages(): array
    {
        return [
            'attachments.max'        => 'الحد الأقصى ' . self::MAX_ATTACHMENTS . ' ملفات في المرة الواحدة.',
            'attachments.*.max'      => 'حجم الملف :attribute أكبر من 100 ميجابايت.',
            'attachments.*.extensions' => 'نوع الملف غير مسموح. المسموح: صور، فيديو، صوت، PDF، Word، Excel، PowerPoint، ملفات مضغوطة.',
            'attachments.*.uploaded' => 'فشل رفع أحد الملفات — غالباً حجمه أكبر من المسموح على السيرفر.',
        ];
    }

    public function index(Request $request)
    {
        $search   = trim((string) $request->query('search', ''));
        $status   = $request->query('status');
        $priority = $request->query('priority');
        $tagType  = $request->query('tag_type');
        $mine     = $request->boolean('mine');

        $issues = SystemIssue::with(['creator:id,name', 'assignee:id,name', 'tags'])
            ->withCount(['comments' => fn ($q) => $q->where('type', 'comment'), 'allAttachments'])
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where(function ($w) use ($like, $search) {
                    $w->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like)
                      ->orWhereHas('tags', fn ($t) => $t->where('label', 'like', $like));
                    if (ctype_digit($search)) $w->orWhere('id', (int) $search);
                });
            })
            ->when($status && isset(SystemIssue::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->when($priority && isset(SystemIssue::PRIORITIES[$priority]), fn ($q) => $q->where('priority', $priority))
            ->when($tagType && isset(SystemIssueTag::types()[$tagType]), fn ($q) => $q->whereHas('tags', fn ($t) => $t->where('type', $tagType)))
            ->when($mine, fn ($q) => $q->where('assigned_to', auth()->id()))
            ->orderByRaw("FIELD(status, 'open', 'in_progress', 'waiting', 'resolved', 'closed')")
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = SystemIssue::select('status', DB::raw('count(*) as c'))->groupBy('status')->pluck('c', 'status');

        return view('admin.system-issues.index', compact('issues', 'counts', 'search', 'status', 'priority', 'tagType', 'mine'));
    }

    public function create()
    {
        return view('admin.system-issues.create', ['admins' => $this->admins()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:20000',
            'priority'     => ['required', Rule::in(array_keys(SystemIssue::PRIORITIES))],
            'assigned_to'  => 'nullable|exists:users,id',
            'voice'        => self::VOICE_RULE,
            'tags'         => 'nullable|array|max:30',
            'tags.*.type'  => ['required', Rule::in(array_keys(SystemIssueTag::types()))],
            'tags.*.id'    => 'nullable|integer',
            'tags.*.label' => 'nullable|string|max:255',
            'tags.*.url'   => 'nullable|string|max:2048',
        ] + $this->attachmentRules(), $this->attachmentMessages());

        $issue = DB::transaction(function () use ($request, $data) {
            $issue = SystemIssue::create([
                'title'       => $data['title'],
                'description' => SystemIssue::sanitizeDescription($data['description'] ?? null),
                'priority'    => $data['priority'],
                'assigned_to' => $data['assigned_to'] ?? null,
                'status'      => 'open',
                'created_by'  => auth()->id(),
                'voice_path'  => $this->storeVoice($request->file('voice')),
            ]);

            foreach ($data['tags'] ?? [] as $tag) {
                $this->attachTag($issue, $tag);
            }

            $this->storeAttachmentFiles($issue, $request->file('attachments', []));

            return $issue;
        });

        return redirect()->route('admin.system-issues.show', $issue)->with('success', 'تم تسجيل المشكلة');
    }

    public function show(SystemIssue $systemIssue)
    {
        $systemIssue->load([
            'creator:id,name', 'assignee:id,name', 'tags', 'attachments',
            'comments' => fn ($q) => $q->whereNull('parent_id')->with(['user:id,name', 'attachments', 'replies.user:id,name', 'replies.attachments']),
        ]);

        return view('admin.system-issues.show', [
            'issue'  => $systemIssue,
            'admins' => $this->admins(),
        ]);
    }

    public function update(Request $request, SystemIssue $systemIssue)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:20000',
            'priority'     => ['required', Rule::in(array_keys(SystemIssue::PRIORITIES))],
            'assigned_to'  => 'nullable|exists:users,id',
            'voice'        => self::VOICE_RULE,
            'remove_voice' => 'nullable|boolean',
        ]);

        if ($request->hasFile('voice') || $request->boolean('remove_voice')) {
            $this->deleteVoice($systemIssue->voice_path);
            $data['voice_path'] = $this->storeVoice($request->file('voice'));
        }

        unset($data['voice'], $data['remove_voice']);
        $data['description'] = SystemIssue::sanitizeDescription($data['description'] ?? null);
        $systemIssue->update($data);

        return back()->with('success', 'تم حفظ التعديلات');
    }

    public function updateStatus(Request $request, SystemIssue $systemIssue)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(SystemIssue::STATUSES))],
        ]);

        $old = $systemIssue->status;
        if ($old === $data['status']) {
            return back();
        }

        $systemIssue->update([
            'status'      => $data['status'],
            'resolved_at' => in_array($data['status'], ['resolved', 'closed'], true)
                ? ($systemIssue->resolved_at ?? now())
                : null,
        ]);

        // Keep a visible history of status changes in the discussion.
        $systemIssue->comments()->create([
            'user_id' => auth()->id(),
            'type'    => 'status',
            'body'    => (SystemIssue::STATUSES[$old]['label'] ?? $old) . ' ← ' . SystemIssue::STATUSES[$data['status']]['label'],
        ]);

        return back()->with('success', 'تم تغيير الحالة إلى: ' . SystemIssue::STATUSES[$data['status']]['label']);
    }

    public function destroy(SystemIssue $systemIssue)
    {
        $systemIssue->delete();

        return redirect()->route('admin.system-issues.index')->with('success', 'تم حذف المشكلة');
    }

    // ── Tags ───────────────────────────────────────────────────────────────

    public function searchTags(Request $request)
    {
        $type = (string) $request->query('type');
        $q    = (string) $request->query('q', '');

        if (!isset(SystemIssueTag::types()[$type]) || $type === 'link' || mb_strlen(trim($q)) < 1) {
            return response()->json([]);
        }

        return response()->json(SystemIssueTag::search($type, $q)->values());
    }

    public function storeTag(Request $request, SystemIssue $systemIssue)
    {
        $tag = $request->validate([
            'type'  => ['required', Rule::in(array_keys(SystemIssueTag::types()))],
            'id'    => 'nullable|integer',
            'label' => 'nullable|string|max:255',
            'url'   => 'nullable|string|max:2048',
        ]);

        if (!$this->attachTag($systemIssue, $tag)) {
            return back()->withErrors(['tag' => 'تعذّر إضافة التاج — تأكد من اختيار عنصر صحيح أو رابط صالح.']);
        }

        return back()->with('success', 'تمت إضافة التاج');
    }

    public function destroyTag(SystemIssue $systemIssue, SystemIssueTag $tag)
    {
        abort_unless((int) $tag->system_issue_id === (int) $systemIssue->id, 404);
        $tag->delete();

        return back()->with('success', 'تم حذف التاج');
    }

    // ── Comments ───────────────────────────────────────────────────────────

    public function storeComment(Request $request, SystemIssue $systemIssue)
    {
        $data = $request->validate([
            'body'      => 'nullable|string|max:10000',
            'voice'     => self::VOICE_RULE,
            'parent_id' => 'nullable|integer',
        ] + $this->attachmentRules(), $this->attachmentMessages());

        if (blank($data['body'] ?? null) && !$request->hasFile('voice') && !$request->hasFile('attachments')) {
            return back()->withErrors(['body' => 'اكتب تعليقاً أو سجّل رسالة صوتية أو أرفق ملفاً.']);
        }

        $parentId = null;
        if (!empty($data['parent_id'])) {
            $parent = SystemIssueComment::where('system_issue_id', $systemIssue->id)
                ->where('type', 'comment')
                ->findOrFail($data['parent_id']);
            // Replies stay one level deep: a reply to a reply joins the same thread.
            $parentId = $parent->parent_id ?? $parent->id;
        }

        $comment = $systemIssue->comments()->create([
            'user_id'    => auth()->id(),
            'parent_id'  => $parentId,
            'type'       => 'comment',
            'body'       => $data['body'] ?? null,
            'voice_path' => $this->storeVoice($request->file('voice')),
        ]);

        $this->storeAttachmentFiles($systemIssue, $request->file('attachments', []), $comment);

        $systemIssue->touch();

        return redirect()->to(route('admin.system-issues.show', $systemIssue) . '#comment-' . $comment->id)
            ->with('success', $parentId ? 'تم إضافة الرد' : 'تم إضافة التعليق');
    }

    public function updateComment(Request $request, SystemIssue $systemIssue, SystemIssueComment $comment)
    {
        $this->authorizeComment($systemIssue, $comment);

        $data = $request->validate(['body' => 'nullable|string|max:10000']);

        if (blank($data['body'] ?? null) && !$comment->voice_path && !$comment->attachments()->exists()) {
            return back()->withErrors(['body' => 'لا يمكن ترك التعليق فارغاً.']);
        }

        $comment->update(['body' => $data['body'] ?? null, 'edited_at' => now()]);

        return redirect()->to(route('admin.system-issues.show', $systemIssue) . '#comment-' . $comment->id)
            ->with('success', 'تم تعديل التعليق');
    }

    public function destroyComment(SystemIssue $systemIssue, SystemIssueComment $comment)
    {
        $this->authorizeComment($systemIssue, $comment);

        // Rows cascade in the DB; the files on disk have to be removed here.
        foreach ($comment->replies->push($comment) as $c) {
            $this->deleteVoice($c->voice_path);
            foreach ($c->attachments as $a) {
                Storage::disk(SystemIssueAttachment::DISK)->delete($a->path);
            }
        }
        $comment->delete(); // replies + attachment rows cascade

        return back()->with('success', 'تم حذف التعليق');
    }

    // ── Attachments ────────────────────────────────────────────────────────

    public function storeAttachments(Request $request, SystemIssue $systemIssue)
    {
        $request->validate(['attachments' => 'required|array'] + $this->attachmentRules(), $this->attachmentMessages());

        $n = $this->storeAttachmentFiles($systemIssue, $request->file('attachments', []));
        $systemIssue->touch();

        return back()->with('success', 'تم رفع ' . $n . ' ملف');
    }

    /**
     * Serve an attachment to a logged-in admin. Images/videos open inline
     * (videos support seeking); everything else is downloaded.
     */
    public function showAttachment(Request $request, SystemIssue $systemIssue, SystemIssueAttachment $attachment)
    {
        abort_unless((int) $attachment->system_issue_id === (int) $systemIssue->id, 404);

        $disk = Storage::disk(SystemIssueAttachment::DISK);
        abort_unless($disk->exists($attachment->path), 404);

        if ($attachment->isInline() && !$request->boolean('download')) {
            return response()->file($disk->path($attachment->path), [
                'Content-Type'           => $attachment->mime,
                'Content-Disposition'    => 'inline; filename="' . addcslashes($attachment->original_name, '"\\') . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()->download($disk->path($attachment->path), $attachment->original_name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroyAttachment(SystemIssue $systemIssue, SystemIssueAttachment $attachment)
    {
        abort_unless((int) $attachment->system_issue_id === (int) $systemIssue->id, 404);

        $user = auth()->user();
        abort_unless((int) $attachment->user_id === (int) $user->id || $user->role === 'super_admin', 403);

        Storage::disk(SystemIssueAttachment::DISK)->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'تم حذف الملف');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function admins()
    {
        return User::whereIn('role', ['admin', 'super_admin'])->orderBy('name')->get(['id', 'name']);
    }

    /** Only the author (or a super admin) may edit or delete a comment. */
    private function authorizeComment(SystemIssue $issue, SystemIssueComment $comment): void
    {
        abort_unless((int) $comment->system_issue_id === (int) $issue->id && $comment->type === 'comment', 404);

        $user = auth()->user();
        abort_unless((int) $comment->user_id === (int) $user->id || $user->role === 'super_admin', 403);
    }

    /**
     * Add a tag after checking it points at something real. The label is
     * re-read from the database, not trusted from the form.
     */
    private function attachTag(SystemIssue $issue, array $tag): bool
    {
        $type = $tag['type'];

        if ($type === 'link') {
            $url = trim((string) ($tag['url'] ?? ''));
            // Allow site-relative paths and http(s) URLs only (no javascript: etc.).
            if ($url === '' || !(str_starts_with($url, '/') || preg_match('#^https?://#i', $url))) {
                return false;
            }
            $label = trim((string) ($tag['label'] ?? '')) ?: $url;

            $issue->tags()->firstOrCreate(
                ['type' => 'link', 'url' => $url],
                ['label' => mb_substr($label, 0, 255)]
            );
            return true;
        }

        $id    = (int) ($tag['id'] ?? 0);
        $label = $id ? $this->resolveLabel($type, $id) : null;
        if ($label === null) {
            return false;
        }

        $issue->tags()->firstOrCreate(
            ['type' => $type, 'taggable_id' => $id],
            ['label' => mb_substr($label, 0, 255)]
        );
        return true;
    }

    private function resolveLabel(string $type, int $id): ?string
    {
        return match ($type) {
            'student', 'teacher' => User::where('role', $type)->whereKey($id)->value('name'),
            'program' => Program::whereKey($id)->value('name_ar'),
            'class'   => ProgramClass::whereKey($id)->value('name'),
            'subject' => Subject::whereKey($id)->value('name_ar'),
            'session' => ($s = Session::find($id, ['id', 'title_ar', 'session_number']))
                ? ($s->title_ar ?: 'محاضرة ' . $s->session_number)
                : null,
            'ticket'  => ($t = Ticket::find($id, ['id', 'ticket_number', 'subject']))
                ? ($t->ticket_number . ' — ' . $t->subject)
                : null,
            default   => null,
        };
    }

    /**
     * Save uploaded files on the private disk under a random name. The
     * extension comes from the validated allow-list, the original name is
     * kept only for display/download.
     *
     * @param UploadedFile[] $files
     */
    private function storeAttachmentFiles(SystemIssue $issue, array $files, ?SystemIssueComment $comment = null): int
    {
        $n = 0;
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, self::ATTACHMENT_EXTENSIONS, true)) {
                continue;
            }

            $path = $file->storeAs('system-issues/' . $issue->id, Str::random(40) . '.' . $ext, SystemIssueAttachment::DISK);

            $issue->allAttachments()->create([
                'comment_id'    => $comment?->id,
                'user_id'       => auth()->id(),
                'path'          => $path,
                'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'mime'          => $file->getMimeType(),
                'size'          => $file->getSize() ?: 0,
            ]);
            $n++;
        }

        return $n;
    }

    private function storeVoice(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        // Recordings arrive as blobs with no reliable extension; derive it from the
        // detected MIME type. Never use the client's extension (it is stored publicly).
        $mime = $file->getMimeType() ?? '';
        $ext = match (true) {
            str_contains($mime, 'webm') => 'webm',
            str_contains($mime, 'ogg')  => 'ogg',
            str_contains($mime, 'mp4'), str_contains($mime, 'm4a'), str_contains($mime, 'aac') => 'm4a',
            str_contains($mime, 'wav'), str_contains($mime, 'wave') => 'wav',
            str_contains($mime, 'mpeg'), str_contains($mime, 'mp3') => 'mp3',
            str_contains($mime, '3gpp') => '3gp',
            default => 'webm',
        };

        return $file->storeAs('system-issues/voice', uniqid('v_', true) . '.' . $ext, 'public');
    }

    private function deleteVoice(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
