<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_standard_id')->constrained('ami_standards')->cascadeOnDelete();
            $table->string('code');
            $table->text('statement');
            $table->text('description')->nullable();
            $table->text('guidance')->nullable();
            $table->text('evidence_requirement')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->unsignedTinyInteger('max_score')->default(4);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['ami_standard_id', 'sort_order']);
            $table->index(['ami_standard_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_indicators');
    }
};
