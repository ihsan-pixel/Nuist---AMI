<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ami_indicators', function (Blueprint $table) {
            if (! Schema::hasColumn('ami_indicators', 'ami_item_id')) {
                $table->foreignId('ami_item_id')->nullable()->after('id')->constrained('ami_items')->nullOnDelete();
                $table->index(['ami_item_id', 'sort_order']);
                $table->index(['ami_item_id', 'code']);
            }

            foreach ([
                'title' => 'string',
                'operational_definition' => 'text',
                'explanation' => 'text',
                'fulfillment_criteria' => 'text',
                'snp_reference' => 'text',
                'evidence_guidance' => 'text',
                'rubric_kurang' => 'text',
                'rubric_cukup_baik' => 'text',
                'rubric_baik' => 'text',
                'rubric_sangat_baik' => 'text',
            ] as $column => $type) {
                if (! Schema::hasColumn('ami_indicators', $column)) {
                    $table->{$type}($column)->nullable()->after('code');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('ami_indicators', function (Blueprint $table) {
            foreach ([
                'rubric_sangat_baik',
                'rubric_baik',
                'rubric_cukup_baik',
                'rubric_kurang',
                'evidence_guidance',
                'snp_reference',
                'fulfillment_criteria',
                'explanation',
                'operational_definition',
                'title',
            ] as $column) {
                if (Schema::hasColumn('ami_indicators', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('ami_indicators', 'ami_item_id')) {
                $table->dropConstrainedForeignId('ami_item_id');
            }
        });
    }
};
