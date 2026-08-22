<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ami_school_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('ami_school_assignments', 'auditor_id')) {
                $table->foreignId('auditor_id')->nullable()->after('school_id');
            }
        });

        Schema::table('ami_school_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('ami_school_assignments', 'auditor_id')) {
                $table->foreign('auditor_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ami_school_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('ami_school_assignments', 'auditor_id')) {
                $table->dropForeign(['auditor_id']);
            }
        });
    }
};
