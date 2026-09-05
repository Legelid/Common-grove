<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->uuid('user_id')->primary();
            $table->boolean('friend_request')->default(true);
            $table->boolean('friend_accepted')->default(true);
            $table->boolean('new_message')->default(true);
            $table->boolean('message_request')->default(true);
            $table->boolean('hangout_from_friend')->default(true);
            $table->boolean('weekly_match')->default(true);
            $table->boolean('milestone')->default(true);
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
