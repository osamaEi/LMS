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
        return view('admin.reports.index');
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
