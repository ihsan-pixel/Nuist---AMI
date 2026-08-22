<?php

use App\Models\AmiPeriod;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function makeUser(string $role, array $attributes = []): User
{
    return User::create(array_merge([
        'name' => ucfirst($role),
        'email' => $role.'@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
    ], $attributes));
}

it('allows only super admin to access admin routes', function () {
    $admin = makeUser('super_admin');
    $pengurus = makeUser('pengurus');

    $this->actingAs($admin)->get('/admin/schools')->assertOk();
    $this->actingAs($pengurus)->get('/admin/schools')->assertForbidden();
});

it('creates school and rejects duplicate scod', function () {
    $admin = makeUser('super_admin');

    $payload = [
        'scod' => 'SCOD-100',
        'name' => 'Sekolah Uji',
        'education_level' => 'SMA',
        'district' => 'Kabupaten Uji',
        'address' => 'Alamat',
        'email' => 'school@test.local',
        'phone' => '08123456789',
        'status' => 'active',
    ];

    $this->actingAs($admin)->post('/admin/schools', $payload)->assertRedirect('/admin/schools');
    $this->actingAs($admin)->post('/admin/schools', $payload)->assertSessionHasErrors('scod');
});

it('activating a period turns off the previous active one', function () {
    $admin = makeUser('super_admin');
    $first = AmiPeriod::create([
        'name' => 'Periode 1',
        'year' => 2025,
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $admin->id,
    ]);
    $second = AmiPeriod::create([
        'name' => 'Periode 2',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'draft',
        'is_active' => false,
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)->post("/admin/ami-periods/{$second->id}/activate")->assertRedirect('/admin/ami-periods');

    expect($first->refresh()->is_active)->toBeFalse();
    expect($second->refresh()->is_active)->toBeTrue();
});

it('prevents school users from accessing other schools through policy', function () {
    $schoolA = School::create([
        'scod' => 'SCOD-A',
        'name' => 'School A',
        'education_level' => 'SMA',
        'district' => 'District A',
        'status' => 'active',
    ]);
    $schoolB = School::create([
        'scod' => 'SCOD-B',
        'name' => 'School B',
        'education_level' => 'SMA',
        'district' => 'District B',
        'status' => 'active',
    ]);
    $user = makeUser('sekolah', ['school_id' => $schoolA->id]);

    $this->actingAs($user);

    expect($user->can('view', $schoolA))->toBeTrue();
    expect($user->can('view', $schoolB))->toBeFalse();
    expect($user->can('update', $schoolB))->toBeFalse();
});
