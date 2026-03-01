<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    protected string $logPath;
    protected int $maxLines = 2000;

    public function __construct()
    {
        $this->logPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request)
    {
        $level   = $request->get('level', 'all');
        $search  = $request->get('search', '');
        $lines   = $request->get('lines', 500);
        $lines   = min((int) $lines, $this->maxLines);

        $entries    = [];
        $totalLines = 0;
        $fileSize   = 0;
        $lastModified = null;

        if (File::exists($this->logPath)) {
            $fileSize     = File::size($this->logPath);
            $lastModified = \Carbon\Carbon::createFromTimestamp(File::lastModified($this->logPath));
            $rawLines     = $this->readLastLines($this->logPath, $lines * 5); // read extra to account for multi-line entries
            $totalLines   = substr_count(File::get($this->logPath), "\n");
            $entries      = $this->parseEntries($rawLines);

            if ($level !== 'all') {
                $entries = array_filter($entries, fn($e) => strtolower($e['level']) === strtolower($level));
            }

            if ($search) {
                $entries = array_filter($entries, fn($e) =>
                    stripos($e['message'], $search) !== false ||
                    stripos($e['context'] ?? '', $search) !== false
                );
            }

            // Keep last N entries after filtering
            $entries = array_slice(array_values($entries), -$lines);
            $entries = array_reverse($entries); // newest first
        }

        $levelCounts = $this->countByLevel($entries);

        return view('admin.logs.index', compact(
            'entries',
            'level',
            'search',
            'lines',
            'fileSize',
            'lastModified',
            'totalLines',
            'levelCounts'
        ));
    }

    public function clear(Request $request)
    {
        if (File::exists($this->logPath)) {
            File::put($this->logPath, '');
        }
        return redirect()->route('admin.logs.index')->with('success', 'تم مسح ملف السجل بنجاح.');
    }

    public function download()
    {
        if (!File::exists($this->logPath)) {
            return redirect()->route('admin.logs.index')->with('error', 'ملف السجل غير موجود.');
        }
        return response()->download($this->logPath, 'laravel-' . now()->format('Y-m-d') . '.log');
    }

    // ─── Private Helpers ───────────────────────────────────────────────────────

    private function readLastLines(string $path, int $n): array
    {
        $content = File::get($path);
        $lines   = explode("\n", $content);
        return array_slice($lines, -$n);
    }

    private function parseEntries(array $rawLines): array
    {
        $entries = [];
        $current = null;

        // Pattern: [2024-01-15 12:34:56] local.ERROR: message {"context":"..."} []
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)/';

        foreach ($rawLines as $line) {
            if (preg_match($pattern, $line, $m)) {
                if ($current !== null) {
                    $entries[] = $current;
                }
                // Split message from JSON context at end
                $message = $m[4];
                $context = '';
                if (preg_match('/^(.+?)(\s+\{.+\})\s*\[\]?$/', $message, $msgMatch)) {
                    $message = trim($msgMatch[1]);
                    $context = trim($msgMatch[2]);
                } elseif (preg_match('/^(.+?)(\s+\[.+\])\s*$/', $message, $msgMatch)) {
                    $message = trim($msgMatch[1]);
                    $context = trim($msgMatch[2]);
                }

                $current = [
                    'datetime'    => $m[1],
                    'environment' => $m[2],
                    'level'       => strtoupper($m[3]),
                    'message'     => $message,
                    'context'     => $context,
                    'extra'       => '',
                ];
            } elseif ($current !== null && trim($line) !== '') {
                $current['extra'] .= "\n" . $line;
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        return $entries;
    }

    private function countByLevel(array $entries): array
    {
        $counts = [
            'ERROR'     => 0,
            'WARNING'   => 0,
            'INFO'      => 0,
            'DEBUG'     => 0,
            'CRITICAL'  => 0,
            'ALERT'     => 0,
            'EMERGENCY' => 0,
            'NOTICE'    => 0,
        ];

        foreach ($entries as $e) {
            $lvl = strtoupper($e['level']);
            if (isset($counts[$lvl])) {
                $counts[$lvl]++;
            }
        }

        return $counts;
    }
}
