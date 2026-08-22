<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ami_school_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('ami_school_assignments', 'audit_status')) {
                $table->string('audit_status')->default('not_started')->after('auditor_id');
            }
            if (! Schema::hasColumn('ami_school_assignments', 'audit_started_at')) {
                $table->timestamp('audit_started_at')->nullable()->after('audit_status');
            }
            if (! Schema::hasColumn('ami_school_assignments', 'audit_completed_at')) {
                $table->timestamp('audit_completed_at')->nullable()->after('audit_started_at');
            }
            if (! Schema::hasColumn('ami_school_assignments', 'audit_completed_by')) {
                $table->foreignId('audit_completed_by')->nullable()->after('audit_completed_at');
            }
        });

        Schema::table('ami_school_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('ami_school_assignments', 'audit_completed_by')) {
                $table->foreign('audit_completed_by')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasColumn('ami_school_assignments', 'audit_status')) {
                $table->index(['auditor_id', 'audit_status']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('ami_school_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('ami_school_assignments', 'audit_completed_by')) {
                $table->dropForeign(['audit_completed_by']);
            }
        });
    }
};
