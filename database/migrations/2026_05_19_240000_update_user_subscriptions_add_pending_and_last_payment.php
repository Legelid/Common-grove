<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add last_payment_at column.
        Schema::table('user_subscriptions', function (Blueprint $table): void {
            $table->timestamp('last_payment_at')->nullable()->after('ends_at');
        });

        // Extend the status enum to include 'pending' for newly-created subscriptions
        // awaiting webhook confirmation. MySQL requires a full MODIFY to change enum values.
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status
            ENUM('pending','active','cancelled','expired','suspended','payment_failed')
            NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table): void {
            $table->dropColumn('last_payment_at');
        });

        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status
            ENUM('active','cancelled','expired','suspended','payment_failed')
            NOT NULL DEFAULT 'active'");
    }
};
