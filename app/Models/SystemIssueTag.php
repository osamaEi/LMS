<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/**
 * A tag points an issue at something in the system. Each type below says how
 * to search for records and which admin page a tag of that type opens.
 * Type 'link' is a free-form URL for anything not covered by a type.
 */
class SystemIssueTag extends Model
{
    protected $fillable = [
        'system_issue_id',
        'type',
        'taggable_id',
        'label',
        'url',
    ];

    public static function types(): array
    {
        return [
            'student' => ['label' => 'طالب',      'color' => '#2563eb', 'route' => 'admin.students.show'],
            'teacher' => ['label' => 'مدرب',      'color' => '#7c3aed', 'route' => 'admin.teachers.show'],
            'program' => ['label' => 'برنامج',    'color' => '#059669', 'route' => 'admin.programs.show'],
            'class'   => ['label' => 'فصل',       'color' => '#0891b2', 'route' => 'admin.classes.show'],
            'subject' => ['label' => 'مقرر',      'color' => '#d97706', 'route' => 'admin.subjects.show'],
            'session' => ['label' => 'محاضرة',    'color' => '#db2777', 'route' => 'admin.sessions.show'],
            'ticket'  => ['label' => 'تذكرة دعم', 'color' => '#dc2626', 'route' => 'admin.tickets.show'],
            'link'    => ['label' => 'رابط',      'color' => '#475569', 'route' => null],
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(SystemIssue::class, 'system_issue_id');
    }

    public function typeLabel(): string
    {
        return self::types()[$this->type]['label'] ?? $this->type;
    }

    public function color(): string
    {
        return self::types()[$this->type]['color'] ?? '#475569';
    }

    public function href(): ?string
    {
        if ($this->type === 'link') {
            return $this->url;
        }

        $route = self::types()[$this->type]['route'] ?? null;

        return $route && $this->taggable_id && Route::has($route)
            ? route($route, $this->taggable_id)
            : null;
    }

    /**
     * Search records of a tag type. Returns [{id, label, sub}].
     */
    public static function search(string $type, string $q, int $limit = 10): Collection
    {
        $q    = trim($q);
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
        $isId = ctype_digit($q);

        switch ($type) {
            case 'student':
            case 'teacher':
                return User::where('role', $type)
                    ->where(function ($w) use ($like, $isId, $q) {
                        $w->where('name', 'like', $like)
                          ->orWhere('email', 'like', $like)
                          ->orWhere('phone', 'like', $like)
                          ->orWhere('national_id', 'like', $like)
                          ->orWhere('student_code', 'like', $like);
                        if ($isId) $w->orWhere('id', (int) $q);
                    })
                    ->orderBy('name')->limit($limit)
                    ->get(['id', 'name', 'email', 'student_code'])
                    ->map(fn ($u) => [
                        'id'    => $u->id,
                        'label' => $u->name,
                        'sub'   => trim(($u->student_code ? $u->student_code . ' · ' : '') . $u->email, ' ·'),
                    ]);

            case 'program':
                return Program::where(function ($w) use ($like, $isId, $q) {
                        $w->where('name_ar', 'like', $like)->orWhere('name_en', 'like', $like)->orWhere('code', 'like', $like);
                        if ($isId) $w->orWhere('id', (int) $q);
                    })
                    ->orderBy('name_ar')->limit($limit)->get(['id', 'name_ar', 'code', 'type'])
                    ->map(fn ($p) => ['id' => $p->id, 'label' => $p->name_ar, 'sub' => trim('#' . $p->id . ' ' . ($p->code ?? ''))]);

            case 'class':
                return ProgramClass::with('program:id,name_ar')
                    ->where(function ($w) use ($like, $isId, $q) {
                        $w->where('name', 'like', $like);
                        if ($isId) $w->orWhere('id', (int) $q);
                    })
                    ->orderBy('name')->limit($limit)->get(['id', 'name', 'program_id'])
                    ->map(fn ($c) => ['id' => $c->id, 'label' => $c->name, 'sub' => '#' . $c->id . ' · ' . ($c->program->name_ar ?? '')]);

            case 'subject':
                return Subject::where(function ($w) use ($like, $isId, $q) {
                        $w->where('name_ar', 'like', $like)->orWhere('name_en', 'like', $like)->orWhere('code', 'like', $like);
                        if ($isId) $w->orWhere('id', (int) $q);
                    })
                    ->orderBy('name_ar')->limit($limit)->get(['id', 'name_ar', 'code'])
                    ->map(fn ($s) => ['id' => $s->id, 'label' => $s->name_ar, 'sub' => trim('#' . $s->id . ' ' . ($s->code ?? ''))]);

            case 'session':
                return Session::where(function ($w) use ($like, $isId, $q) {
                        $w->where('title_ar', 'like', $like)->orWhere('title_en', 'like', $like);
                        if ($isId) $w->orWhere('id', (int) $q);
                    })
                    ->latest('scheduled_at')->limit($limit)->get(['id', 'title_ar', 'session_number', 'scheduled_at'])
                    ->map(fn ($s) => [
                        'id'    => $s->id,
                        'label' => $s->title_ar ?: ('محاضرة ' . $s->session_number),
                        'sub'   => '#' . $s->id . ($s->scheduled_at ? ' · ' . \Carbon\Carbon::parse($s->scheduled_at)->format('Y/m/d H:i') : ''),
                    ]);

            case 'ticket':
                return Ticket::where(function ($w) use ($like, $isId, $q) {
                        $w->where('ticket_number', 'like', $like)->orWhere('subject', 'like', $like);
                        if ($isId) $w->orWhere('id', (int) $q);
                    })
                    ->latest()->limit($limit)->get(['id', 'ticket_number', 'subject'])
                    ->map(fn ($t) => ['id' => $t->id, 'label' => $t->subject, 'sub' => $t->ticket_number]);
        }

        return collect();
    }
}
