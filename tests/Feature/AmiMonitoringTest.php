<?php

use App\Enums\AmiFollowUpStatus;
use App\Models\AmiFinding;
use App\Models\AmiFollowUp;
use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Models\AmiStandard;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function monitoringFixture(): array
{
    $pengurus = User::create([
        'name' => 'Pengurus',
        'email' => 'pengurus-monitor@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'pengurus',
    ]);

    $auditor = User::create([
        'name' => 'Auditor',
        'email' => 'auditor-monitor@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'auditor',
    ]);

    $school = School::create([
        'scod' => 'SCOD-MON-1',
        'name' => 'School Monitor 1',
        'education_level' => 'SMA',
        'district' => 'Sleman',
        'status' => 'active',
    ]);

    $period = AmiPeriod::create([
        'name' => 'Periode Monitor',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $pengurus->id,
    ]);

    $standard = AmiStandard::create([
        'ami_period_id' => $period->id,
        'code' => 'STD-MON',
        'name' => 'Standar Monitor',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);

    $indicator = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'STD-MON.1',
        'statement' => 'Indikator Monitor',
        'sort_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $assignment = AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $school->id,
        'auditor_id' => $auditor->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'audit_status' => 'completed',
        'audit_started_at' => now()->subDays(5),
        'audit_completed_at' => now()->subDays(2),
        'audit_completed_by' => $auditor->id,
    ]);

    $finding = AmiFinding::create([
        'ami_school_assignment_id' => $assignment->id,
        'ami_indicator_id' => $indicator->id,
        'auditor_id' => $auditor->id,
        'type' => 'major',
        'title' => 'Finding Monitor',
        'description' => 'Desc',
        'recommendation' => 'Saran',
        'status' => 'open',
    ]);

    $followUp = AmiFollowUp::create([
        'ami_finding_id' => $finding->id,
        'ami_school_assignment_id' => $assignment->id,
        'school_id' => $school->id,
        'action_plan' => 'Action',
        'status' => AmiFollowUpStatus::ACCEPTED->value,
        'submitted_at' => now()->subDay(),
        'verified_at' => now(),
        'verified_by' => $auditor->id,
    ]);

    $otherSchool = School::create([
        'scod' => 'SCOD-MON-2',
        'name' => 'School Monitor 2',
        'education_level' => 'SMA',
        'district' => 'Bantul',
        'status' => 'active',
    ]);

    AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $otherSchool->id,
        'status' => 'not_started',
    ]);

    return compact('pengurus', 'auditor', 'school', 'period', 'standard', 'indicator', 'assignment', 'followUp', 'otherSchool');
}

it('pengurus can access monitoring dashboard and detail', function () {
    $data = monitoringFixture();

    $this->actingAs($data['pengurus'])->get(route('pengurus.ami.index'))->assertOk()->assertSee('Monitoring AMI');
    $this->actingAs($data['pengurus'])->get(route('pengurus.ami.show', $data['assignment']))->assertOk()->assertSee('Status Akhir');
});

it('role pengurus filtering works server-side', function () {
    $data = monitoringFixture();

    $this->actingAs($data['pengurus'])->get(route('pengurus.ami.index', [
        'ami_period_id' => $data['period']->id,
        'search' => 'SCOD-MON-1',
        'district' => 'Sleman',
        'status' => 'completed',
    ]))->assertOk()->assertSee('School Monitor 1');
});

it('role selain pengurus ditolak', function () {
    $data = monitoringFixture();

    $this->actingAs($data['auditor'])->get(route('pengurus.ami.index'))->assertForbidden();
    $this->actingAs($data['auditor'])->get(route('pengurus.findings.index'))->assertForbidden();
});

it('findings monitoring page loads', function () {
    $data = monitoringFixture();

    $this->actingAs($data['pengurus'])->get(route('pengurus.findings.index'))->assertOk()->assertSee('Monitoring Findings');
});
