<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('sent_by')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->unsignedInteger('recipient_count');
            $table->timestamps();

            $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_logs');
    }
};
