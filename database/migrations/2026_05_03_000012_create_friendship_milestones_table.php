<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friendship_milestones', function (Blueprint $table): void {
            $table->uuid('friendship_id');
            $table->unsignedTinyInteger('milestone_days');
            $table->timestamp('notified_at')->useCurrent();

            $table->primary(['friendship_id', 'milestone_days']);
            $table->foreign('friendship_id')->references('id')->on('friendships')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendship_milestones');
    }
};
