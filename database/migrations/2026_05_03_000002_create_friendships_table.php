<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('requester_id');
            $table->uuid('recipient_id');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();

            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['requester_id', 'recipient_id']);
            $table->index('requester_id');
            $table->index('recipient_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
