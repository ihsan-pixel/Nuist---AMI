<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_response_id')->constrained('ami_responses')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('url');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['ami_response_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_evidences');
    }
};
