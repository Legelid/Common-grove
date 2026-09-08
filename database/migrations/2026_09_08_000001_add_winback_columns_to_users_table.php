<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('winback_emailed_at')->nullable()->after('last_seen_at');
            $table->boolean('marketing_emails_opt_out')->default(false)->after('winback_emailed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['winback_emailed_at', 'marketing_emails_opt_out']);
        });
    }
};
