<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ami_items')) {
            return;
        }

        Schema::create('ami_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_standard_id')->constrained('ami_standards')->cascadeOnDelete();
            $table->string('code');
            $table->unsignedInteger('number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['ami_standard_id', 'sort_order']);
            $table->index(['ami_standard_id', 'code']);
            $table->unique(['ami_standard_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_items');
    }
};
