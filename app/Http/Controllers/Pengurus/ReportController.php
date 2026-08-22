<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Services\AmiXlsxExportService;
use App\Services\AmiReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected AmiReportService $reportService, protected AmiXlsxExportService $xlsxExportService)
    {
    }

    public function index(Request $request): View
    {
        $periods = AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
        $period = $periods->firstWhere('id', $request->integer('period'))
            ?? $periods->firstWhere('is_active', true)
            ?? $periods->first();
        abort_unless($period, 404);

        $filters = [
            'district' => $request->string('district')->toString(),
            'status' => $request->string('status')->toString(),
            'search' => $request->string('search')->toString(),
        ];

        $summary = $this->reportService->summary($period);
        $schools = $this->reportService->schools($period, $filters);
        $findings = $this->reportService->findings($period, $filters);

        return view('pengurus.ami.reports.index', compact('periods', 'period', 'summary', 'schools', 'findings', 'filters'));
    }

    public function school(Request $request, AmiSchoolAssignment $assignment): View
    {
        $assignment->load([
            'period.standards.indicators',
            'school',
            'auditor',
            'responses.evidences',
            'assessments.indicator',
            'findings.indicator.standard',
            'followUps.evidences',
            'followUps.verifier',
        ]);

        $summary = [
            'total_standards' => $assignment->period->standards->count(),
            'total_indicators' => $assignment->period->standards->flatMap->indicators->count(),
            'assessed_indicators' => $assignment->assessments->count(),
            'kurang' => $assignment->assessments->where('rating.value', 'kurang')->count(),
            'cukup_baik' => $assignment->assessments->where('rating.value', 'cukup_baik')->count(),
            'baik' => $assignment->assessments->where('rating.value', 'baik')->count(),
            'sangat_baik' => $assignment->assessments->where('rating.value', 'sangat_baik')->count(),
        ];

        $standardRows = $assignment->period->standards->map(function ($standard) use ($assignment) {
            $assessments = $assignment->assessments->filter(fn ($assessment) => $assessment->indicator->ami_standard_id === $standard->id);
            $findings = $assignment->findings->filter(fn ($finding) => $finding->indicator?->ami_standard_id === $standard->id);

            return [
                'code' => $standard->code,
                'name' => $standard->name,
                'indicators' => $standard->indicators->count(),
                'kurang' => $assessments->where('rating.value', 'kurang')->count(),
                'cukup_baik' => $assessments->where('rating.value', 'cukup_baik')->count(),
                'baik' => $assessments->where('rating.value', 'baik')->count(),
                'sangat_baik' => $assessments->where('rating.value', 'sangat_baik')->count(),
                'findings' => $findings->count(),
            ];
        });

        return view('pengurus.ami.reports.school', compact('assignment', 'summary', 'standardRows'));
    }

    public function export(Request $request)
    {
        $periods = AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
        $period = $periods->firstWhere('id', $request->integer('period'))
            ?? $periods->firstWhere('is_active', true)
            ?? $periods->firstOrFail();
        $summary = $this->reportService->summary($period);
        $filters = $request->only(['district', 'status', 'search']);
        $schools = $this->reportService->schools($period, $filters)->getCollection();
        $findings = $this->reportService->findings($period, $filters)->getCollection();
        $followUps = $schools->flatMap(fn ($assignment) => $assignment->followUps)->values();

        $sheetPaths = $this->xlsxExportService->download([
            ['name' => 'Ringkasan', 'rows' => $this->ringkasanRows($period, $summary)],
            ['name' => 'Rekap Sekolah', 'rows' => $this->rekapSekolahRows($period, $schools)],
            ['name' => 'Temuan', 'rows' => $this->temuanRows($period, $findings)],
            ['name' => 'Tindak Lanjut', 'rows' => $this->tindakLanjutRows($period, $followUps)],
        ], 'laporan-ami-'.$period->id.'.xlsx');

        register_shutdown_function(fn () => @unlink($sheetPaths));

        return response()->streamDownload(function () use ($sheetPaths) {
            readfile($sheetPaths);
        }, 'laporan-ami-'.$period->id.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function ringkasanRows(AmiPeriod $period, array $summary): array
    {
        return $this->xlsxExportService->sanitizeRows([
            ['Periode', $period->name],
            ['Tanggal Generate', now()->format('Y-m-d H:i:s')],
            ['Total Sekolah', $summary['total_schools']],
            ['Belum Submit', $summary['not_submitted']],
            ['Audit Selesai', $summary['audit_completed']],
            ['Observation', $summary['observation']],
            ['Minor', $summary['minor']],
            ['Major', $summary['major']],
            ['Follow-up Pending', $summary['follow_up_pending']],
            ['Follow-up Accepted', $summary['follow_up_accepted']],
        ]);
    }

    protected function rekapSekolahRows(AmiPeriod $period, $schools): array
    {
        $rows = [[
            'No','SCOD','Sekolah','Kabupaten','Periode','Status Pengisian','Progress','Auditor','Status Audit','Kurang','Cukup Baik','Baik','Sangat Baik','Observation','Minor','Major','Follow-up Pending','Follow-up Accepted','Status Akhir',
        ]];

        foreach ($schools as $index => $assignment) {
            $rows[] = [
                $index + 1,
                $assignment->school->scod,
                $assignment->school->name,
                $assignment->school->district,
                $period->name,
                $assignment->status,
                $assignment->responses->count().'/'.$assignment->period->standards->flatMap->indicators->count(),
                $assignment->auditor?->name ?? '-',
                $assignment->audit_status ?? '-',
                $assignment->assessments->where('rating.value', 'kurang')->count(),
                $assignment->assessments->where('rating.value', 'cukup_baik')->count(),
                $assignment->assessments->where('rating.value', 'baik')->count(),
                $assignment->assessments->where('rating.value', 'sangat_baik')->count(),
                $assignment->findings->where('type', 'observation')->count(),
                $assignment->findings->where('type', 'minor')->count(),
                $assignment->findings->where('type', 'major')->count(),
                $assignment->followUps->where('status.value', '!=', 'accepted')->count(),
                $assignment->followUps->where('status.value', 'accepted')->count(),
                $this->reportService->summary($period)['audit_completed'] ? 'OK' : 'Belum',
            ];
        }

        return $this->xlsxExportService->sanitizeRows($rows);
    }

    protected function temuanRows(AmiPeriod $period, $findings): array
    {
        $rows = [[
            'No','SCOD','Sekolah','Kabupaten','Standar','Indikator','Jenis','Judul','Uraian','Rekomendasi','Auditor','Status Follow-up',
        ]];
        foreach ($findings as $index => $finding) {
            $rows[] = [
                $index + 1,
                $finding->assignment->school->scod,
                $finding->assignment->school->name,
                $finding->assignment->school->district,
                $finding->indicator->standard->name,
                $finding->indicator->code,
                $finding->type,
                $finding->title,
                $finding->description,
                $finding->recommendation,
                $finding->auditor?->name ?? '-',
                $finding->followUp?->status->value ?? '-',
            ];
        }

        return $this->xlsxExportService->sanitizeRows($rows);
    }

    protected function tindakLanjutRows(AmiPeriod $period, $followUps): array
    {
        $rows = [[
            'No','SCOD','Sekolah','Finding','Jenis Finding','Action Plan','Status','Tanggal Submit','Catatan Auditor','Tanggal Verifikasi','Verifier','Evidence URL',
        ]];
        foreach ($followUps as $index => $followUp) {
            foreach ($followUp->evidences as $evidence) {
                $rows[] = [
                    $index + 1,
                    $followUp->school->scod,
                    $followUp->school->name,
                    $followUp->finding->title,
                    $followUp->finding->type,
                    $followUp->action_plan,
                    $followUp->status->value,
                    $followUp->submitted_at?->format('Y-m-d H:i:s'),
                    $followUp->verifier_note,
                    $followUp->verified_at?->format('Y-m-d H:i:s'),
                    $followUp->verifier?->name ?? '-',
                    $evidence->url,
                ];
            }
            if ($followUp->evidences->isEmpty()) {
                $rows[] = [
                    $index + 1,
                    $followUp->school->scod,
                    $followUp->school->name,
                    $followUp->finding->title,
                    $followUp->finding->type,
                    $followUp->action_plan,
                    $followUp->status->value,
                    $followUp->submitted_at?->format('Y-m-d H:i:s'),
                    $followUp->verifier_note,
                    $followUp->verified_at?->format('Y-m-d H:i:s'),
                    $followUp->verifier?->name ?? '-',
                    '-',
                ];
            }
        }
        return $this->xlsxExportService->sanitizeRows($rows);
    }
}
