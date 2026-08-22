<?php

use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function assignmentUser(string $role, array $attributes = []): User
{
    return User::create(array_merge([
        'name' => ucfirst($role),
        'email' => $role.'_assignment@ami.test',
        'password' => Hash::make('password123'),
        'role' => $role,
        'school_id' => null,
    ], $attributes));
}

it('allows super admin to view assignment page', function () {
    $admin = assignmentUser('super_admin');

    $this->actingAs($admin)->get('/admin/assignments')->assertOk();
});

it('rejects non super admin from assignment page', function () {
    $pengurus = assignmentUser('pengurus');

    $this->actingAs($pengurus)->get('/admin/assignments')->assertForbidden();
});

it('creates assignment and prevents duplicate for same period and school', function () {
    $admin = assignmentUser('super_admin');
    $period = AmiPeriod::create([
        'name' => 'Periode Uji',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $admin->id,
    ]);
    $school = School::create([
        'scod' => 'SCOD-X1',
        'name' => 'School X1',
        'education_level' => 'SMA',
        'district' => 'Kabupaten X',
        'status' => 'active',
    ]);

    $payload = [
        'ami_period_id' => $period->id,
        'school_ids' => [$school->id],
    ];

    $this->actingAs($admin)->post('/admin/assignments', $payload)->assertRedirect(route('admin.assignments.index', ['ami_period_id' => $period->id]));
    $this->actingAs($admin)->post('/admin/assignments', $payload)->assertRedirect(route('admin.assignments.index', ['ami_period_id' => $period->id]));

    expect(AmiSchoolAssignment::count())->toBe(1);
    expect(AmiSchoolAssignment::first()->ami_period_id)->toBe($period->id);
    expect(AmiSchoolAssignment::first()->school_id)->toBe($school->id);
});

it('seeders are idempotent for dummy accounts', function () {
    $this->seed();
    $first = User::where('email', 'admin@ami.test')->first();
    $this->seed();
    $second = User::where('email', 'admin@ami.test')->first();

    expect($first->id)->toBe($second->id);
    expect($first->role)->toBe('super_admin');
});
