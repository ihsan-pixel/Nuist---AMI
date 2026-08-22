<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_follow_up_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_follow_up_id')->constrained('ami_follow_ups')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('url');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['ami_follow_up_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_follow_up_evidences');
    }
};
