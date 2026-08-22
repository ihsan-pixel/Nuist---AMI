<?php

use App\Enums\AmiFollowUpStatus;
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

function followUpFixture(): array
{
    $auditor = User::create([
        'name' => 'Auditor',
        'email' => 'auditor-followup@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'auditor',
    ]);

    $school = School::create([
        'scod' => 'SCOD-FU-1',
        'name' => 'School FU 1',
        'education_level' => 'SMA',
        'district' => 'Kabupaten FU',
        'status' => 'active',
    ]);

    $period = AmiPeriod::create([
        'name' => 'Periode FU',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $auditor->id,
    ]);

    $standard = AmiStandard::create([
        'ami_period_id' => $period->id,
        'code' => 'STD-FU',
        'name' => 'Standar FU',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);

    $indicator = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'STD-FU.1',
        'statement' => 'Indikator FU',
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
        'audit_completed_at' => now(),
        'audit_completed_by' => $auditor->id,
    ]);

    $finding = AmiFinding::create([
        'ami_school_assignment_id' => $assignment->id,
        'ami_indicator_id' => $indicator->id,
        'auditor_id' => $auditor->id,
        'type' => 'major',
        'title' => 'Temuan FU',
        'description' => 'Uraian',
        'recommendation' => 'Perbaikan',
        'status' => 'open',
    ]);

    $followUp = AmiFollowUp::create([
        'ami_finding_id' => $finding->id,
        'ami_school_assignment_id' => $assignment->id,
        'school_id' => $school->id,
        'action_plan' => 'Perbaikan awal',
        'status' => AmiFollowUpStatus::DRAFT->value,
    ]);

    $otherSchool = School::create([
        'scod' => 'SCOD-FU-2',
        'name' => 'School FU 2',
        'education_level' => 'SMA',
        'district' => 'Kabupaten FU',
        'status' => 'active',
    ]);

    $otherUser = User::create([
        'name' => 'Sekolah Lain',
        'email' => 'other-followup@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'sekolah',
        'school_id' => $otherSchool->id,
    ]);

    return compact('auditor', 'school', 'assignment', 'finding', 'followUp', 'otherSchool', 'otherUser');
}

it('school can see own follow-up dashboard', function () {
    $data = followUpFixture();

    $schoolUser = User::create([
        'name' => 'School User',
        'email' => 'school-followup@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'sekolah',
        'school_id' => $data['school']->id,
    ]);

    $this->actingAs($schoolUser)->get(route('school.ami.follow-ups.index'))->assertOk()->assertSee('Temuan FU');
});

it('other school is denied from follow-up detail', function () {
    $data = followUpFixture();

    $this->actingAs($data['otherUser'])->get(route('school.ami.follow-ups.show', $data['followUp']))->assertForbidden();
});

it('school can submit follow-up with google drive evidence and invalid url is rejected', function () {
    $data = followUpFixture();
    $schoolUser = User::create([
        'name' => 'School User',
        'email' => 'school-followup-2@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'sekolah',
        'school_id' => $data['school']->id,
    ]);

    $this->actingAs($schoolUser)->post(route('school.ami.follow-ups.evidences.store', $data['followUp']), [
        'title' => 'Bukti 1',
        'url' => 'https://example.com/file',
        'note' => 'invalid',
    ])->assertSessionHasErrors('url');

    $this->actingAs($schoolUser)->post(route('school.ami.follow-ups.evidences.store', $data['followUp']), [
        'title' => 'Bukti 1',
        'url' => 'https://drive.google.com/file/d/abc/view',
        'note' => 'ok',
    ])->assertRedirect();

    $this->actingAs($schoolUser)->post(route('school.ami.follow-ups.submit', $data['followUp']))->assertRedirect();

    expect($data['followUp']->refresh()->status->value)->toBe(AmiFollowUpStatus::SUBMITTED->value);
    expect(AmiFollowUpEvidence::count())->toBe(1);
});

it('auditor can verify follow-up and another auditor is denied', function () {
    $data = followUpFixture();
    $auditor = $data['auditor'];
    $otherAuditor = User::create([
        'name' => 'Auditor Lain',
        'email' => 'auditor-other-followup@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'auditor',
    ]);

    $data['followUp']->update(['status' => AmiFollowUpStatus::SUBMITTED->value]);

    $this->actingAs($otherAuditor)->post(route('auditor.follow-ups.accept', $data['followUp']))->assertForbidden();

    $this->actingAs($auditor)->post(route('auditor.follow-ups.revision', $data['followUp']), [
        'verifier_note' => 'Tambahkan bukti terbaru.',
    ])->assertRedirect();

    expect($data['followUp']->refresh()->status->value)->toBe(AmiFollowUpStatus::NEEDS_REVISION->value);

    $data['followUp']->update(['status' => AmiFollowUpStatus::SUBMITTED->value]);
    $this->actingAs($auditor)->post(route('auditor.follow-ups.accept', $data['followUp']))->assertRedirect();
    expect($data['followUp']->refresh()->status->value)->toBe(AmiFollowUpStatus::ACCEPTED->value);
});

it('accepted follow-up cannot be edited by school', function () {
    $data = followUpFixture();
    $schoolUser = User::create([
        'name' => 'School User',
        'email' => 'school-followup-3@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'sekolah',
        'school_id' => $data['school']->id,
    ]);

    $data['followUp']->update(['status' => AmiFollowUpStatus::ACCEPTED->value]);

    $this->actingAs($schoolUser)->post(route('school.ami.follow-ups.store', $data['finding']), [
        'action_plan' => 'ubah',
        'status' => 'draft',
    ])->assertForbidden();
});
