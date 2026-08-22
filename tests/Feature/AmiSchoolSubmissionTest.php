<?php

use App\Models\AmiEvidence;
use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiResponse;
use App\Models\AmiSchoolAssignment;
use App\Models\AmiStandard;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function schoolUser(string $email, School $school): User
{
    return User::create([
        'name' => $email,
        'email' => $email,
        'password' => Hash::make('password123'),
        'role' => 'sekolah',
        'school_id' => $school->id,
    ]);
}

function schoolFixture(): array
{
    $school = School::create([
        'scod' => 'SCOD-101',
        'name' => 'School 101',
        'education_level' => 'SMA',
        'district' => 'District 1',
        'status' => 'active',
    ]);

    $otherSchool = School::create([
        'scod' => 'SCOD-102',
        'name' => 'School 102',
        'education_level' => 'SMA',
        'district' => 'District 2',
        'status' => 'active',
    ]);

    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin-school@ami.test',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
    ]);

    $period = AmiPeriod::create([
        'name' => 'Periode Sekolah',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'submission_start_at' => '2026-02-01 00:00:00',
        'submission_end_at' => '2026-06-30 23:59:59',
        'review_start_at' => null,
        'review_end_at' => null,
        'status' => 'active',
        'is_active' => true,
        'created_by' => $admin->id,
    ]);

    $standard = AmiStandard::create([
        'ami_period_id' => $period->id,
        'code' => 'S1',
        'name' => 'Standar 1',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);

    $indicator1 = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'S1.1',
        'statement' => 'Indikator wajib',
        'sort_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $indicator2 = AmiIndicator::create([
        'ami_standard_id' => $standard->id,
        'code' => 'S1.2',
        'statement' => 'Indikator opsional',
        'sort_order' => 2,
        'is_required' => false,
        'is_active' => true,
    ]);

    $assignment = AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $school->id,
        'status' => 'not_started',
    ]);

    AmiSchoolAssignment::create([
        'ami_period_id' => $period->id,
        'school_id' => $otherSchool->id,
        'status' => 'not_started',
    ]);

    return compact('school', 'otherSchool', 'admin', 'period', 'standard', 'indicator1', 'indicator2', 'assignment');
}

it('school can open its own assignment', function () {
    $data = schoolFixture();
    $user = schoolUser('school1@ami.test', $data['school']);

    $this->actingAs($user)->get('/school/ami')->assertOk();
});

it('school cannot modify another school evidence', function () {
    $data = schoolFixture();
    $user = schoolUser('school2@ami.test', $data['school']);
    $otherUser = schoolUser('school3@ami.test', $data['otherSchool']);

    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 3,
        'answer' => 'Jawaban 1',
        'note' => 'Catatan',
        'status' => 'draft',
        'save_action' => 'draft',
    ]);

    $response = AmiResponse::first();

    $this->actingAs($otherUser)->post(route('school.ami.evidences.store', $response), [
        'title' => 'Bukti 1',
        'url' => 'https://drive.google.com/file/d/abc',
        'description' => 'Deskripsi',
    ])->assertForbidden();
});

it('response can be saved without duplicating', function () {
    $data = schoolFixture();
    $user = schoolUser('school3@ami.test', $data['school']);

    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 3,
        'answer' => 'Jawaban 1',
        'note' => 'Catatan',
        'status' => 'draft',
        'save_action' => 'draft',
    ])->assertRedirect();

    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 4,
        'answer' => 'Jawaban 2',
        'note' => 'Catatan 2',
        'status' => 'draft',
        'save_action' => 'draft',
    ])->assertRedirect();

    expect(AmiResponse::count())->toBe(1);
});

it('adds evidence and rejects invalid url', function () {
    $data = schoolFixture();
    $user = schoolUser('school4@ami.test', $data['school']);

    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 3,
        'answer' => 'Jawaban 1',
        'note' => 'Catatan',
        'status' => 'draft',
        'save_action' => 'draft',
    ]);

    $response = AmiResponse::first();

    $this->actingAs($user)->post(route('school.ami.evidences.store', $response), [
        'title' => 'Bukti 1',
        'url' => 'https://drive.google.com/file/d/abc',
        'description' => 'Deskripsi',
    ])->assertRedirect();

    $this->actingAs($user)->post(route('school.ami.evidences.store', $response), [
        'title' => 'Bukti 2',
        'url' => 'https://example.com/file',
        'description' => 'Deskripsi',
    ])->assertSessionHasErrors('url');
});

it('progress is calculated correctly and assignment moves to in_progress', function () {
    $data = schoolFixture();
    $user = schoolUser('school5@ami.test', $data['school']);

    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 4,
        'answer' => 'Jawaban lengkap',
        'note' => 'Catatan',
        'status' => 'draft',
        'save_action' => 'draft',
    ]);

    $this->actingAs($user)->get('/school/ami')->assertSee('100%');
    expect($data['assignment']->refresh()->status)->toBe('in_progress');
});

it('submit is blocked until required indicators complete and then locks edits', function () {
    $data = schoolFixture();
    $user = schoolUser('school6@ami.test', $data['school']);

    $this->actingAs($user)->post(route('school.ami.submit'))->assertSessionHasErrors('submit');

    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 4,
        'answer' => 'Jawaban lengkap',
        'note' => 'Catatan',
        'status' => 'draft',
        'save_action' => 'draft',
    ]);
    $this->actingAs($user)->post(route('school.ami.submit'))->assertRedirect(route('school.ami.index'));

    expect($data['assignment']->refresh()->status)->toBe('submitted');

    $response = AmiResponse::first();
    $this->actingAs($user)->put(route('school.ami.update', $data['indicator1']), [
        'self_score' => 1,
        'answer' => 'ubah',
        'note' => 'ubah',
        'status' => 'draft',
        'save_action' => 'draft',
    ])->assertForbidden();
});
