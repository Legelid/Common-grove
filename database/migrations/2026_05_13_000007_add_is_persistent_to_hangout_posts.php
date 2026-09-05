<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hangout_posts', function (Blueprint $table): void {
            $table->boolean('is_persistent')->default(false)->after('is_active');
            $table->timestamp('expires_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('hangout_posts', function (Blueprint $table): void {
            $table->dropColumn('is_persistent');
            $table->timestamp('expires_at')->nullable(false)->change();
        });
    }
};
