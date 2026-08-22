<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Ami\AmiPeriodController;
use App\Http\Controllers\Ami\AdminPlaceholderController;
use App\Http\Controllers\Ami\AppSettingController;
use App\Http\Controllers\Ami\AmiSchoolAssignmentController;
use App\Http\Controllers\Ami\AmiIndicatorController;
use App\Http\Controllers\Ami\AmiStandardController;
use App\Http\Controllers\Ami\InstrumentController;
use App\Http\Controllers\Ami\SchoolController;
use App\Http\Controllers\Auditor\AmiController as AuditorAmiController;
use App\Http\Controllers\Auditor\FollowUpController as AuditorFollowUpController;
use App\Http\Controllers\Pengurus\AmiController as PengurusAmiController;
use App\Http\Controllers\Pengurus\FindingsController as PengurusFindingsController;
use App\Http\Controllers\Pengurus\ReportController as PengurusReportController;
use App\Http\Controllers\School\FollowUpController as SchoolFollowUpController;
use App\Http\Controllers\School\AmiController as SchoolAmiController;
use App\Models\AmiPeriod;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $activePeriod = AmiPeriod::query()->where('is_active', true)->with(['standards.items.indicators', 'assignments.school', 'assignments.responses', 'assignments.assessments', 'assignments.findings', 'assignments.followUps'])->first();

    $dashboard = [
        'role' => $user?->role,
        'name' => $user?->name,
        'activePeriod' => $activePeriod,
        'quickStats' => [],
        'highlights' => [],
    ];

    if ($user?->role === 'super_admin') {
        $dashboard['quickStats'] = [
            ['label' => 'Periode aktif', 'value' => $activePeriod?->name ?? 'Belum ada'],
            ['label' => 'Standar', 'value' => $activePeriod?->standards->count() ?? 0],
            ['label' => 'Butir', 'value' => $activePeriod?->standards->flatMap->items->count() ?? 0],
            ['label' => 'Indikator', 'value' => $activePeriod?->standards->flatMap->items->flatMap->indicators->count() ?? 0],
        ];
        $dashboard['highlights'] = [
            'Kelola master instrumen, sekolah, dan periode aktif dari menu admin.',
            'Pastikan penugasan sekolah dan auditor sudah sesuai sebelum audit berjalan.',
            'Gunakan laporan untuk memantau status pengisian, penilaian, dan tindak lanjut.',
        ];
    } elseif ($user?->role === 'pengurus') {
        $assignments = $activePeriod?->assignments ?? collect();
        $dashboard['quickStats'] = [
            ['label' => 'Total sekolah', 'value' => $assignments->count()],
            ['label' => 'Belum mulai', 'value' => $assignments->where('status', 'not_started')->count()],
            ['label' => 'Sedang isi', 'value' => $assignments->where('status', 'in_progress')->count()],
            ['label' => 'Sudah submit', 'value' => $assignments->where('status', 'submitted')->count()],
        ];
        $dashboard['highlights'] = [
            'Pantau status sekolah dan hasil audit melalui monitoring dan laporan.',
            'Periksa temuan prioritas serta tindak lanjut yang belum selesai.',
            'Gunakan dashboard ini sebagai pintu masuk ke rekap dan eksport laporan.',
        ];
    } elseif ($user?->role === 'auditor') {
        $assignments = $user?->auditorAssignments()->with(['school', 'assessments', 'findings', 'followUps'])->latest('id')->get() ?? collect();
        $dashboard['quickStats'] = [
            ['label' => 'Penugasan', 'value' => $assignments->count()],
            ['label' => 'Dikerjakan', 'value' => $assignments->where('audit_status', 'in_progress')->count()],
            ['label' => 'Selesai', 'value' => $assignments->where('audit_status', 'completed')->count()],
            ['label' => 'Temuan', 'value' => $assignments->flatMap->findings->count()],
        ];
        $dashboard['highlights'] = [
            'Buka penugasan terbaru untuk memulai penilaian berbasis rubrik IA2024.',
            'Pastikan rating, triangulasi, dan finding terdokumentasi konsisten.',
            'Selesaikan audit agar tindak lanjut bisa diteruskan ke sekolah.',
        ];
    } elseif ($user?->role === 'sekolah') {
        $assignment = $user?->school?->assignments()->with(['period.standards.items.indicators', 'responses.evidences'])->latest('id')->first();
        $requiredIndicators = $assignment?->period?->standards?->flatMap->items?->flatMap->indicators?->where('is_required', true) ?? collect();
        $responses = $assignment?->responses?->keyBy('ami_indicator_id') ?? collect();
        $completed = $requiredIndicators->filter(fn ($indicator) => isset($responses[$indicator->id]) && filled($responses[$indicator->id]->answer) && $responses[$indicator->id]->self_score !== null)->count();
        $total = $requiredIndicators->count();
        $dashboard['quickStats'] = [
            ['label' => 'Sekolah', 'value' => $user?->school?->name ?? '-'],
            ['label' => 'SCOD', 'value' => $user?->school?->scod ?? '-'],
            ['label' => 'Progress', 'value' => $total > 0 ? round(($completed / $total) * 100).'%' : '0%'],
            ['label' => 'Status', 'value' => $assignment?->status ?? 'Belum ada'],
        ];
        $dashboard['highlights'] = [
            'Lanjutkan pengisian indikator yang masih kosong agar submission tidak tertahan.',
            'Pastikan bukti Google Drive dapat dibuka dan relevan dengan DKA yang diisi.',
            'Gunakan halaman ringkasan sebelum submit untuk mengecek kelengkapan wajib.',
        ];
    }

    return view('dashboard', $dashboard);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->middleware('role:super_admin')->group(function () {
        Route::get('instruments', [InstrumentController::class, 'index'])->name('instruments.index');
        Route::get('settings', [AppSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AppSettingController::class, 'update'])->name('settings.update');
        Route::get('auditors', [AdminPlaceholderController::class, 'index'])->name('auditors.index')->defaults('title', 'Auditor');
        Route::get('auditor-assignments', [AdminPlaceholderController::class, 'index'])->name('auditor-assignments.index')->defaults('title', 'Penugasan Auditor');
        Route::get('assessments', [AdminPlaceholderController::class, 'index'])->name('assessments.index')->defaults('title', 'Penilaian');
        Route::get('findings', [AdminPlaceholderController::class, 'index'])->name('findings.index')->defaults('title', 'Temuan');
        Route::get('follow-ups', [AdminPlaceholderController::class, 'index'])->name('follow-ups.index')->defaults('title', 'Tindak Lanjut');
        Route::get('reports', [AdminPlaceholderController::class, 'index'])->name('reports.index')->defaults('title', 'Laporan');
        Route::get('users', [AdminPlaceholderController::class, 'index'])->name('users.index')->defaults('title', 'Pengguna');
        Route::get('assignments', [AmiSchoolAssignmentController::class, 'index'])->name('assignments.index');
        Route::post('assignments', [AmiSchoolAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('standards/create', [AmiStandardController::class, 'create'])->name('standards.create');
        Route::post('standards', [AmiStandardController::class, 'store'])->name('standards.store');
        Route::get('standards/{standard}/edit', [AmiStandardController::class, 'edit'])->name('standards.edit');
        Route::put('standards/{standard}', [AmiStandardController::class, 'update'])->name('standards.update');
        Route::get('indicators/create', [AmiIndicatorController::class, 'create'])->name('indicators.create');
        Route::post('indicators', [AmiIndicatorController::class, 'store'])->name('indicators.store');
        Route::get('indicators/{indicator}/edit', [AmiIndicatorController::class, 'edit'])->name('indicators.edit');
        Route::put('indicators/{indicator}', [AmiIndicatorController::class, 'update'])->name('indicators.update');
        Route::resource('schools', SchoolController::class)->except(['show']);
        Route::resource('ami-periods', AmiPeriodController::class)->except(['show', 'destroy']);
        Route::post('ami-periods/{amiPeriod}/activate', [AmiPeriodController::class, 'activate'])->name('ami-periods.activate');
    });

    Route::prefix('school')->name('school.')->middleware('role:sekolah')->group(function () {
        Route::get('ami', [SchoolAmiController::class, 'index'])->name('ami.index');
        Route::get('ami/standards/{standard}', [SchoolAmiController::class, 'standard'])->name('ami.standard');
        Route::get('ami/indicators/{indicator}/edit', [SchoolAmiController::class, 'edit'])->name('ami.edit');
        Route::put('ami/indicators/{indicator}', [SchoolAmiController::class, 'update'])->name('ami.update');
        Route::post('ami/responses/{response}/evidences', [SchoolAmiController::class, 'evidenceStore'])->name('ami.evidences.store');
        Route::put('ami/evidences/{evidence}', [SchoolAmiController::class, 'evidenceUpdate'])->name('ami.evidences.update');
        Route::delete('ami/evidences/{evidence}', [SchoolAmiController::class, 'evidenceDestroy'])->name('ami.evidences.destroy');
        Route::get('ami/review', [SchoolAmiController::class, 'review'])->name('ami.review');
        Route::post('ami/submit', [SchoolAmiController::class, 'submit'])->name('ami.submit');
        Route::get('ami/follow-ups', [SchoolFollowUpController::class, 'index'])->name('ami.follow-ups.index');
        Route::get('ami/follow-ups/{followUp}', [SchoolFollowUpController::class, 'show'])->name('ami.follow-ups.show');
        Route::post('ami/findings/{finding}/follow-up', [SchoolFollowUpController::class, 'store'])->name('ami.follow-ups.store');
        Route::post('ami/follow-ups/{followUp}/submit', [SchoolFollowUpController::class, 'submit'])->name('ami.follow-ups.submit');
        Route::post('ami/follow-ups/{followUp}/evidences', [SchoolFollowUpController::class, 'evidenceStore'])->name('ami.follow-ups.evidences.store');
        Route::delete('ami/follow-up-evidences/{evidence}', [SchoolFollowUpController::class, 'evidenceDestroy'])->name('ami.follow-ups.evidences.destroy');
    });

    Route::prefix('auditor')->name('auditor.')->middleware('role:auditor')->group(function () {
        Route::get('ami', [AuditorAmiController::class, 'index'])->name('ami.index');
        Route::get('ami/{assignment}', [AuditorAmiController::class, 'show'])->name('ami.show');
        Route::get('ami/{assignment}/standards/{indicator}', [AuditorAmiController::class, 'standard'])->name('ami.standard');
        Route::post('ami/{assignment}/standards/{indicator}/assessments', [AuditorAmiController::class, 'storeAssessment'])->name('ami.assessments.store');
        Route::put('assessments/{assessment}', [AuditorAmiController::class, 'updateAssessment'])->name('ami.assessments.update');
        Route::post('ami/{assignment}/findings/{indicator?}', [AuditorAmiController::class, 'storeFinding'])->name('ami.findings.store');
        Route::put('findings/{finding}', [AuditorAmiController::class, 'updateFinding'])->name('ami.findings.update');
        Route::delete('findings/{finding}', [AuditorAmiController::class, 'destroyFinding'])->name('ami.findings.destroy');
        Route::get('ami/{assignment}/review', [AuditorAmiController::class, 'review'])->name('ami.review');
        Route::post('ami/{assignment}/complete', [AuditorAmiController::class, 'complete'])->name('ami.complete');
        Route::get('follow-ups', [AuditorFollowUpController::class, 'index'])->name('follow-ups.index');
        Route::get('follow-ups/{followUp}', [AuditorFollowUpController::class, 'show'])->name('follow-ups.show');
        Route::post('follow-ups/{followUp}/accept', [AuditorFollowUpController::class, 'accept'])->name('follow-ups.accept');
        Route::post('follow-ups/{followUp}/revision', [AuditorFollowUpController::class, 'revision'])->name('follow-ups.revision');
    });

    Route::prefix('pengurus')->name('pengurus.')->middleware('role:pengurus')->group(function () {
        Route::get('ami/reports', [PengurusReportController::class, 'index'])->name('ami.reports.index');
        Route::get('ami/reports/{assignment}', [PengurusReportController::class, 'school'])->name('ami.reports.school');
        Route::get('ami/reports-export', [PengurusReportController::class, 'export'])->name('ami.reports.export');
        Route::get('ami', [PengurusAmiController::class, 'index'])->name('ami.index');
        Route::get('ami/{assignment}', [PengurusAmiController::class, 'show'])->name('ami.show');
        Route::get('findings', [PengurusFindingsController::class, 'index'])->name('findings.index');
    });
});

require __DIR__.'/auth.php';
