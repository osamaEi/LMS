<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        // Get quick stats for the dashboard
        $stats = [
            'total_students' => \App\Models\User::where('role', 'student')->count(),
            'total_programs' => \App\Models\Program::count(),
            'total_sessions' => \App\Models\Session::count(),
            'total_activities' => \App\Models\ActivityLog::count(),
            'pending_xapi' => \App\Models\ActivityLog::where('xapi_sent', false)->count(),
            'active_enrollments' => \App\Models\Enrollment::count(),
        ];

        // Get recent activity
        $recentReports = \App\Models\ActivityLog::where('action_category', 'admin')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact('stats', 'recentReports'));
    }

    public function nelcCompliance()
    {
        $report = $this->reportService->nelcComplianceReport();
        return view('admin.reports.nelc-compliance', compact('report'));
    }

    public function export(Request $request)
    {
        $type = $request->get('report_type', 'nelc');
        $format = $request->get('format', 'pdf'); // pdf, csv, json

        $data = match ($type) {
            'nelc' => $this->reportService->nelcComplianceReport(),
            default => [],
        };

        $filename = "report-{$type}-" . now()->format('Y-m-d');

        return match ($format) {
            'json' => $this->reportService->exportJson($data, $filename),
            'pdf' => $this->reportService->exportPdf('admin.reports.pdf.nelc', ['report' => $data], $filename),
            default => redirect()->back()->with('error', 'Invalid format'),
        };
    }
}
