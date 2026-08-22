<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ami_assessments', function (Blueprint $table) {
            if (! Schema::hasColumn('ami_assessments', 'rating')) {
                $table->string('rating')->nullable()->after('status');
                $table->index(['ami_school_assignment_id', 'rating']);
            }

            if (! Schema::hasColumn('ami_assessments', 'verification_methods')) {
                $table->text('verification_methods')->nullable()->after('auditor_note');
            }

            if (! Schema::hasColumn('ami_assessments', 'verification_note')) {
                $table->text('verification_note')->nullable()->after('verification_methods');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ami_assessments', function (Blueprint $table) {
            foreach (['verification_note', 'verification_methods', 'rating'] as $column) {
                if (Schema::hasColumn('ami_assessments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
