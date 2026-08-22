<?php

use App\Models\AmiIndicator;
use App\Models\AmiItem;
use App\Models\AmiPeriod;
use App\Models\AmiStandard;
use Database\Seeders\AmiInstrument2024Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedIa2024(): void
{
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
    ]);

    app(AmiInstrument2024Seeder::class)->run();
}

it('seeds 4 components 16 items and 45 indicators with complete IA2024 fields', function () {
    seedIa2024();

    expect(AmiStandard::count())->toBe(4);
    expect(AmiItem::count())->toBe(16);
    expect(AmiIndicator::count())->toBe(45);
    expect(AmiIndicator::pluck('code')->unique()->count())->toBe(45);
    expect(AmiIndicator::where('code', '2.5.4')->count())->toBe(0);

    $indicator = AmiIndicator::first();
    expect($indicator->operational_definition)->not->toBeEmpty();
    expect($indicator->evidence_guidance)->not->toBeEmpty();
    expect($indicator->rubric_kurang)->not->toBeEmpty();
    expect($indicator->rubric_cukup_baik)->not->toBeEmpty();
    expect($indicator->rubric_baik)->not->toBeEmpty();
    expect($indicator->rubric_sangat_baik)->not->toBeEmpty();
});

it('is idempotent when seeder runs repeatedly', function () {
    seedIa2024();
    $snapshot = [
        'standards' => AmiStandard::count(),
        'items' => AmiItem::count(),
        'indicators' => AmiIndicator::count(),
        'codes' => AmiIndicator::pluck('code')->sort()->values()->all(),
    ];

    seedIa2024();

    expect(AmiStandard::count())->toBe($snapshot['standards']);
    expect(AmiItem::count())->toBe($snapshot['items']);
    expect(AmiIndicator::count())->toBe($snapshot['indicators']);
    expect(AmiIndicator::pluck('code')->sort()->values()->all())->toBe($snapshot['codes']);
});
