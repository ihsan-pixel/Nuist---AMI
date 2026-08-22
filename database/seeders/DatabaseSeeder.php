<?php

namespace Database\Seeders;

use App\Models\AmiPeriod;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $schools = collect([
            ['scod' => 'SCOD-001', 'name' => "SMK Ma'arif Demo 1", 'education_level' => 'SMK', 'district' => 'Kabupaten Demo', 'status' => 'active'],
            ['scod' => 'SCOD-002', 'name' => "MA Ma'arif Demo 2", 'education_level' => 'MA', 'district' => 'Kabupaten Demo', 'status' => 'active'],
            ['scod' => 'SCOD-003', 'name' => "SMA Ma'arif Demo 3", 'education_level' => 'SMA', 'district' => 'Kabupaten Demo', 'status' => 'active'],
        ])->map(fn (array $data) => School::updateOrCreate(['scod' => $data['scod']], $data));

        $admin = User::updateOrCreate(['email' => 'admin@ami.test'], [
            'name' => 'Admin AMI',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'school_id' => null,
        ]);

        User::updateOrCreate(['email' => 'pengurus@ami.test'], [
            'name' => 'Pengurus AMI',
            'password' => Hash::make('password123'),
            'role' => 'pengurus',
            'school_id' => null,
        ]);

        User::updateOrCreate(['email' => 'auditor@ami.test'], [
            'name' => 'Auditor AMI',
            'password' => Hash::make('password123'),
            'role' => 'auditor',
            'school_id' => null,
        ]);

        $schoolUser = User::updateOrCreate(['email' => 'sekolah@ami.test'], [
            'name' => 'Sekolah AMI',
            'password' => Hash::make('password123'),
            'role' => 'sekolah',
            'school_id' => null,
        ]);

        $schoolUser->update(['school_id' => $schools->first()->id]);

        AmiPeriod::updateOrCreate(['name' => 'Periode AMI 2026'], [
            'year' => 2026,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'submission_start_at' => '2026-02-01 00:00:00',
            'submission_end_at' => '2026-06-30 23:59:59',
            'review_start_at' => '2026-07-01 00:00:00',
            'review_end_at' => '2026-08-31 23:59:59',
            'status' => 'active',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $period = AmiPeriod::where('name', 'Periode AMI 2026')->firstOrFail();

        $this->call(AmiInstrument2024Seeder::class);

        \App\Models\AmiSchoolAssignment::updateOrCreate(
            ['ami_period_id' => $period->id, 'school_id' => $schools->first()->id],
            ['status' => 'in_progress', 'started_at' => now()]
        );

        \App\Models\AmiSchoolAssignment::updateOrCreate(
            ['ami_period_id' => $period->id, 'school_id' => $schools->get(1)->id],
            [
                'status' => 'submitted',
                'submitted_at' => now(),
                'auditor_id' => User::where('email', 'auditor@ami.test')->value('id'),
                'audit_status' => 'in_progress',
                'audit_started_at' => now()->subDay(),
            ]
        );

        \App\Models\AmiSchoolAssignment::updateOrCreate(
            ['ami_period_id' => $period->id, 'school_id' => $schools->get(2)->id],
            [
                'status' => 'submitted',
                'submitted_at' => now(),
                'auditor_id' => User::where('email', 'auditor@ami.test')->value('id'),
                'audit_status' => 'completed',
                'audit_started_at' => now()->subDays(2),
                'audit_completed_at' => now()->subDay(),
                'audit_completed_by' => User::where('email', 'auditor@ami.test')->value('id'),
            ]
        );
    }
}
