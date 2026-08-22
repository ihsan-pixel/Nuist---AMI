<?php

use App\Models\AmiAssessment;
use App\Models\AmiFinding;
use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Models\AmiStandard;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function auditorFixture(): array
{
    $auditor = User::create([
        'name' => 'Auditor',
        'email' => 'auditor-fixture@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'auditor',
        'school_id' => null,
    ]);

    $otherUser = User::create([
        'name' => 'Pengurus',
        'email' => 'pengurus-fixture@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'pengurus',
        'school_id' => null,
    ]);

    $school = School::create([
        'scod' => 'SCOD-AUD-1',
        'name' => 'School Audit 1',
        'education_level' => 'SMA',
        'district' => 'Kabupaten Audit',
        'status' => 'active',
    ]);

    $period = AmiPeriod::create([
        'name' => 'Periode Audit',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'submission_start_at' => '2026-02-01 00:00:00',
        'submission_end_at' => '2026-06-30 23:59:59',
        'review_start_at' => '2026-07-01 00:00:00',
        'review_end_at' => '2026-08-31 23:59:59',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $auditor->id,
    ]);

    $standard = AmiStandard::create([
        'ami_period_id' => $period->id,
        'code' => 'STD-1',
        'name' => 'Standar Audit 1',
        'description' => null,
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);

    $indicatorRequired = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'STD-1.1',
        'statement' => 'Indikator wajib audit',
        'description' => null,
        'guidance' => null,
        'evidence_requirement' => null,
        'weight' => 1,
        'max_score' => 4,
        'sort_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $indicatorOptional = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'STD-1.2',
        'statement' => 'Indikator opsional audit',
        'description' => null,
        'guidance' => null,
        'evidence_requirement' => null,
        'weight' => 1,
        'max_score' => 4,
        'sort_order' => 2,
        'is_required' => false,
        'is_active' => true,
    ]);

    $assignment = AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $school->id,
        'auditor_id' => $auditor->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'audit_status' => 'not_started',
    ]);

    $otherAssignment = AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => School::create([
            'scod' => 'SCOD-AUD-2',
            'name' => 'School Audit 2',
            'education_level' => 'SMA',
            'district' => 'Kabupaten Audit',
            'status' => 'active',
        ])->id,
        'auditor_id' => $otherUser->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'audit_status' => 'not_started',
    ]);

    return compact('auditor', 'otherUser', 'school', 'period', 'standard', 'indicatorRequired', 'indicatorOptional', 'assignment', 'otherAssignment');
}

it('super admin not required and auditor can access own assignment', function () {
    $data = auditorFixture();

    $this->actingAs($data['auditor'])->get(route('auditor.ami.show', $data['assignment']))->assertOk();
});

it('role other than auditor is denied', function () {
    $data = auditorFixture();

    $this->actingAs($data['otherUser'])->get(route('auditor.ami.index'))->assertForbidden();
});

it('assignment can be assessed and finding can be created', function () {
    $data = auditorFixture();

    $this->actingAs($data['auditor'])->post(route('auditor.ami.assessments.store', [$data['assignment'], $data['indicatorRequired']]), [
        'status' => 'conform',
        'score' => 4,
        'auditor_note' => 'OK',
    ])->assertRedirect();

    $this->actingAs($data['auditor'])->post(route('auditor.ami.findings.store', [$data['assignment'], $data['indicatorRequired']]), [
        'type' => 'observation',
        'title' => 'Catatan',
        'description' => 'Deskripsi',
        'recommendation' => 'Saran',
    ])->assertRedirect();

    expect(AmiAssessment::count())->toBe(1);
    expect(AmiFinding::count())->toBe(1);
    expect($data['assignment']->refresh()->audit_status)->toBe('in_progress');
});

it('prevents duplicate assessment for same assignment and indicator', function () {
    $data = auditorFixture();

    $payload = [
        'status' => 'conform',
        'score' => 4,
        'auditor_note' => 'OK',
    ];

    $this->actingAs($data['auditor'])->post(route('auditor.ami.assessments.store', [$data['assignment'], $data['indicatorRequired']]), $payload);
    $this->actingAs($data['auditor'])->post(route('auditor.ami.assessments.store', [$data['assignment'], $data['indicatorRequired']]), [
        'status' => 'non_conform',
        'score' => 1,
        'auditor_note' => 'Changed',
    ]);

    expect(AmiAssessment::count())->toBe(1);
    expect(AmiAssessment::first()->status->value)->toBe('non_conform');
});

it('invalid indicator from other period is rejected', function () {
    $data = auditorFixture();
    $otherPeriod = AmiPeriod::create([
        'name' => 'Periode Lain',
        'year' => 2027,
        'start_date' => '2027-01-01',
        'end_date' => '2027-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $data['auditor']->id,
    ]);
    $otherStandard = AmiStandard::create([
        'ami_period_id' => $otherPeriod->id,
        'code' => 'X1',
        'name' => 'X1',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);
    $otherIndicator = AmiIndicator::create([
        'ami_standard_id' => $otherStandard->id,
        'code' => 'X1.1',
        'statement' => 'Invalid',
        'sort_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $this->actingAs($data['auditor'])->post(route('auditor.ami.assessments.store', [$data['assignment'], $otherIndicator]), [
        'status' => 'conform',
        'score' => 4,
        'auditor_note' => 'invalid',
    ])->assertForbidden();
});

it('sorts assignments by latest and can complete when required assessments exist', function () {
    $data = auditorFixture();

    $this->actingAs($data['auditor'])->post(route('auditor.ami.assessments.store', [$data['assignment'], $data['indicatorRequired']]), [
        'status' => 'conform',
        'score' => 4,
        'auditor_note' => 'OK',
    ]);

    $this->actingAs($data['auditor'])->get(route('auditor.ami.index'))->assertOk()->assertSee($data['school']->name);

    $this->actingAs($data['auditor'])->get(route('auditor.ami.review', $data['assignment']))->assertOk();

    $this->actingAs($data['auditor'])->post(route('auditor.ami.complete', $data['assignment']))->assertRedirect(route('auditor.ami.show', $data['assignment']));
    expect($data['assignment']->refresh()->audit_status)->toBe('completed');
});

it('prevents completion when required assessments are missing', function () {
    $data = auditorFixture();

    $this->actingAs($data['auditor'])->post(route('auditor.ami.complete', $data['assignment']))->assertStatus(422);
});
