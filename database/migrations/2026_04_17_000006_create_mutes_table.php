<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('muter_id');
            $table->uuid('muted_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('muter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('muted_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['muter_id', 'muted_id']);
            $table->index('muter_id');
            $table->index('muted_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutes');
    }
};
