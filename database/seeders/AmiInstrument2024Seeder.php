<?php

namespace Database\Seeders;

use App\Models\AmiIndicator;
use App\Models\AmiItem;
use App\Models\AmiPeriod;
use App\Models\AmiStandard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use RuntimeException;

class AmiInstrument2024Seeder extends Seeder
{
    public function run(): void
    {
        $period = AmiPeriod::where('name', 'Periode AMI 2026')->first();
        if (! $period) {
            return;
        }

        $path = database_path('seeders/data/ia2024_master.json');
        if (! File::exists($path)) {
            throw new RuntimeException('IA2024 master JSON not found at '.$path);
        }

        $components = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($components as $componentOrder => $component) {
            $standard = AmiStandard::updateOrCreate(
                ['ami_period_id' => $period->id, 'code' => 'K'.($componentOrder + 1)],
                [
                    'name' => $component['name'],
                    'description' => null,
                    'sort_order' => $componentOrder + 1,
                    'weight' => null,
                    'is_active' => true,
                ]
            );

            foreach ($component['items'] as $itemOrder => $item) {
                $itemTitle = $this->cleanTitle($item['title']);

                $itemModel = AmiItem::updateOrCreate(
                    ['ami_standard_id' => $standard->id, 'code' => $this->itemCode($componentOrder + 1, $item['number'])],
                    [
                        'number' => $item['number'],
                        'title' => $itemTitle,
                        'description' => null,
                        'sort_order' => $itemOrder + 1,
                    ]
                );

                foreach ($item['indicators'] as $indicatorOrder => $indicator) {
                    AmiIndicator::updateOrCreate(
                        ['ami_item_id' => $itemModel->id, 'code' => $indicator['code']],
                        [
                            'ami_standard_id' => $standard->id,
                            'title' => $indicator['title'],
                            'statement' => $indicator['title'],
                            'operational_definition' => $indicator['operational_definition'],
                            'description' => null,
                            'explanation' => $indicator['explanation'],
                            'fulfillment_criteria' => $indicator['fulfillment_criteria'],
                            'snp_reference' => $indicator['snp_reference'],
                            'guidance' => null,
                            'evidence_guidance' => $indicator['evidence_guidance'],
                            'rubric_kurang' => $indicator['rubric_kurang'],
                            'rubric_cukup_baik' => $indicator['rubric_cukup_baik'],
                            'rubric_baik' => $indicator['rubric_baik'],
                            'rubric_sangat_baik' => $indicator['rubric_sangat_baik'],
                            'weight' => null,
                            'max_score' => 4,
                            'sort_order' => $indicatorOrder + 1,
                            'is_required' => true,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }

    protected function cleanTitle(string $value): string
    {
        return preg_replace('/^###\s*/', '', trim($value)) ?? trim($value);
    }

    protected function itemCode(int $componentNumber, int $itemNumber): string
    {
        return $componentNumber.'.'.$itemNumber;
    }
}
