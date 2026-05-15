<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('reporter_id');
            $table->uuid('reported_user_id');
            $table->string('reportable_type');
            $table->uuid('reportable_id');
            $table->string('reason', 100);
            $table->text('detail')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed', 'actioned'])->default('pending');
            $table->uuid('reviewed_by')->nullable();
            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['reporter_id', 'reported_user_id', 'reportable_type', 'reportable_id'], 'reports_unique_reporter_reportable');
            $table->index('status');
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
