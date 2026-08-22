<?php

use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiStandard;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function amiUser(string $role, array $attributes = []): User
{
    return User::create(array_merge([
        'name' => ucfirst($role),
        'email' => $role.'@ami.test',
        'password' => Hash::make('password'),
        'role' => $role,
        'school_id' => null,
    ], $attributes));
}

it('allows only super admin to access instruments', function () {
    $admin = amiUser('super_admin');
    $pengurus = amiUser('pengurus');

    $this->actingAs($admin)->get('/admin/instruments')->assertOk();
    $this->actingAs($pengurus)->get('/admin/instruments')->assertForbidden();
});

it('creates a standard', function () {
    $admin = amiUser('super_admin');
    $period = AmiPeriod::create([
        'name' => 'Periode Uji',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)->post('/admin/standards', [
        'ami_period_id' => $period->id,
        'code' => 'ST1',
        'name' => 'Standar 1',
        'description' => 'Desc',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ])->assertRedirect(route('admin.instruments.index', ['ami_period_id' => $period->id]));

    expect(AmiStandard::count())->toBe(1);
});

it('creates an indicator linked to a valid standard', function () {
    $admin = amiUser('super_admin');
    $period = AmiPeriod::create([
        'name' => 'Periode Uji',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => $admin->id,
    ]);
    $standard = AmiStandard::create([
        'ami_period_id' => $period->id,
        'code' => 'ST1',
        'name' => 'Standar 1',
        'sort_order' => 1,
        'weight' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($admin)->post('/admin/indicators', [
        'ami_standard_id' => $standard->id,
        'code' => 'ST1.1',
        'statement' => 'Pernyataan',
        'description' => 'Desc',
        'guidance' => 'Guidance',
        'evidence_requirement' => 'Evidence',
        'weight' => 1,
        'max_score' => 4,
        'sort_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ])->assertRedirect(route('admin.instruments.index', ['ami_period_id' => $period->id]));

    expect(AmiIndicator::count())->toBe(1);
});

it('rejects indicator creation for invalid standard', function () {
    $admin = amiUser('super_admin');

    $this->actingAs($admin)->post('/admin/indicators', [
        'ami_standard_id' => 999999,
        'code' => 'ST2.1',
        'statement' => 'Pernyataan',
        'sort_order' => 1,
    ])->assertSessionHasErrors('ami_standard_id');
});

it('orders standards by sort_order', function () {
    $period = AmiPeriod::create([
        'name' => 'Periode Uji',
        'year' => 2026,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
        'is_active' => true,
        'created_by' => null,
    ]);

    AmiStandard::create(['ami_period_id' => $period->id, 'code' => 'S2', 'name' => 'B', 'sort_order' => 2, 'is_active' => true]);
    AmiStandard::create(['ami_period_id' => $period->id, 'code' => 'S1', 'name' => 'A', 'sort_order' => 1, 'is_active' => true]);

    expect($period->fresh()->standards->pluck('code')->all())->toBe(['S1', 'S2']);
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
    $user = amiUser('sekolah', ['school_id' => $schoolA->id]);

    expect($user->can('view', $schoolA))->toBeTrue();
    expect($user->can('view', $schoolB))->toBeFalse();
});
