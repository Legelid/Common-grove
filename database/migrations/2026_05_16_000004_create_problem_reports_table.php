<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_reports', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->string('report_type', 30);
            $table->string('subject', 120);
            $table->text('description');
            $table->string('page_url', 500)->nullable();
            $table->string('screenshot_path', 500)->nullable();

            // Free-text context fields — not FK-constrained so the report survives deletions
            $table->string('related_user', 100)->nullable();
            $table->string('related_room', 200)->nullable();
            $table->string('related_message', 500)->nullable();

            $table->string('contact_email', 255)->nullable();

            $table->string('status', 20)->default('open');    // open | reviewing | resolved | dismissed
            $table->string('priority', 20)->default('normal'); // low | normal | high | urgent

            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->index('report_type');
            $table->index('status');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_reports');
    }
};
