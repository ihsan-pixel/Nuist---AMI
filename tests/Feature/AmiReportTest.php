<?php

use App\Enums\AmiFollowUpStatus;
use App\Models\AmiAssessment;
use App\Models\AmiFinding;
use App\Models\AmiFollowUp;
use App\Models\AmiFollowUpEvidence;
use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Models\AmiStandard;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
uses(RefreshDatabase::class);

function reportFixture(): array
{
    $pengurus = User::create([
        'name' => 'Pengurus',
        'email' => 'pengurus-report@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'pengurus',
    ]);

    $school = School::create([
        'scod' => 'SCOD-RPT-1',
        'name' => 'Report School 1',
        'education_level' => 'SMA',
        'district' => 'Sleman',
        'status' => 'active',
    ]);

    $otherSchool = School::create([
        'scod' => '=HYPERLINK("https://evil.example","SCOD-RPT-2")',
        'name' => '+SUM School 2',
        'education_level' => 'SMA',
        'district' => 'Bantul',
        'status' => 'active',
    ]);

    $auditor = User::create([
        'name' => 'Auditor',
        'email' => 'auditor-report@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'auditor',
    ]);

    $period = AmiPeriod::create([
        'name' => 'Periode Report',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $pengurus->id,
    ]);

    $standard = AmiStandard::create([
        'ami_period_id' => $period->id,
        'code' => 'STD-RPT',
        'name' => 'Standar Report',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);

    $indicator = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'STD-RPT.1',
        'statement' => 'Indikator Report',
        'sort_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $assignment = AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $school->id,
        'auditor_id' => $auditor->id,
        'status' => 'submitted',
        'submitted_at' => now()->subDay(),
        'audit_status' => 'completed',
        'audit_started_at' => now()->subDays(3),
        'audit_completed_at' => now()->subDay(),
        'audit_completed_by' => $auditor->id,
    ]);

    AmiAssessment::create([
        'ami_school_assignment_id' => $assignment->id,
        'ami_indicator_id' => $indicator->id,
        'auditor_id' => $auditor->id,
        'status' => 'conform',
        'score' => 4,
        'auditor_note' => 'OK',
        'assessed_at' => now()->subDay(),
    ]);

    $finding = AmiFinding::create([
        'ami_school_assignment_id' => $assignment->id,
        'ami_indicator_id' => $indicator->id,
        'auditor_id' => $auditor->id,
        'type' => 'minor',
        'title' => 'Finding Report',
        'description' => 'Desc',
        'recommendation' => 'Saran',
        'status' => 'open',
    ]);

    $followUp = AmiFollowUp::create([
        'ami_finding_id' => $finding->id,
        'ami_school_assignment_id' => $assignment->id,
        'school_id' => $school->id,
        'action_plan' => 'Action',
        'status' => AmiFollowUpStatus::NEEDS_REVISION->value,
        'submitted_at' => now()->subDay(),
        'verified_by' => $auditor->id,
        'verifier_note' => 'Perlu perbaikan',
    ]);

    AmiFollowUpEvidence::create([
        'ami_follow_up_id' => $followUp->id,
        'title' => 'Bukti',
        'url' => 'https://drive.google.com/file/d/abc/view',
        'note' => 'note',
    ]);

    AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $otherSchool->id,
        'auditor_id' => $auditor->id,
        'status' => 'not_started',
        'audit_status' => 'not_started',
    ]);

    return compact('pengurus', 'school', 'otherSchool', 'auditor', 'period', 'assignment');
}

it('pengurus can open report page and detail page shows read-only sections', function () {
    $data = reportFixture();

    $this->actingAs($data['pengurus'])->get(route('pengurus.ami.reports.index', ['period' => $data['period']->id]))
        ->assertOk()
        ->assertSee('Laporan AMI');

    $response = $this->actingAs($data['pengurus'])->get(route('pengurus.ami.reports.school', $data['assignment']));
    $response->assertOk();
    $response->assertSee('Identitas');
    $response->assertSee('Ringkasan');
    $response->assertSee('Hasil per Standar');
    $response->assertSee('Temuan');
    $response->assertSee('Tindak Lanjut');
    $response->assertDontSee('input type="file"', false);
    $response->assertDontSee('name="action_plan"');
});

it('school and auditor are denied from report pages', function () {
    $data = reportFixture();
    $schoolUser = User::create([
        'name' => 'School User',
        'email' => 'school-report@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'sekolah',
        'school_id' => $data['school']->id,
    ]);

    $this->actingAs($schoolUser)->get(route('pengurus.ami.reports.index'))->assertForbidden();
    $this->actingAs($data['auditor'])->get(route('pengurus.ami.reports.index'))->assertForbidden();
});

it('report filters by period district search name and scod', function () {
    $data = reportFixture();

    $this->actingAs($data['pengurus'])->get(route('pengurus.ami.reports.index', [
        'period' => $data['period']->id,
        'district' => 'Sleman',
        'search' => 'Report School 1',
    ]))->assertOk()->assertSee('Report School 1');

    $this->actingAs($data['pengurus'])->get(route('pengurus.ami.reports.index', [
        'period' => $data['period']->id,
        'search' => 'SCOD-RPT-1',
    ]))->assertOk()->assertSee('SCOD-RPT-1');
});

it('export is xlsx and follows filters with formula injection sanitized', function () {
    $data = reportFixture();

    $response = $this->actingAs($data['pengurus'])->get(route('pengurus.ami.reports.export', [
        'period' => $data['period']->id,
        'district' => 'Sleman',
        'search' => 'Report School 1',
    ]));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=laporan-ami-'.$data['period']->id.'.xlsx');

    $path = tempnam(sys_get_temp_dir(), 'ami_export_').'.xlsx';
    file_put_contents($path, $response->streamedContent());

    $zip = new ZipArchive();
    expect($zip->open($path))->toBeTrue();
    $sheetXml = $zip->getFromName('xl/worksheets/sheet2.xml');
    $zip->close();

    expect($sheetXml)->toContain('Report School 1');
    expect($sheetXml)->not->toContain('SCOD-RPT-2');

    $maliciousResponse = $this->actingAs($data['pengurus'])->get(route('pengurus.ami.reports.export', [
        'period' => $data['period']->id,
    ]));
    $maliciousPath = tempnam(sys_get_temp_dir(), 'ami_export_').'.xlsx';
    file_put_contents($maliciousPath, $maliciousResponse->streamedContent());
    $maliciousZip = new ZipArchive();
    expect($maliciousZip->open($maliciousPath))->toBeTrue();
    $maliciousSheet = $maliciousZip->getFromName('xl/worksheets/sheet2.xml');
    $maliciousZip->close();

    expect($maliciousSheet)->toContain('&#039;=HYPERLINK');
    expect($maliciousSheet)->not->toContain('<f>');
});
