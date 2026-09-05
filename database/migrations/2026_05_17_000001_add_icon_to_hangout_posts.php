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
            $table->string('icon', 32)->nullable()->after('is_official');
        });
    }

    public function down(): void
    {
        Schema::table('hangout_posts', function (Blueprint $table): void {
            $table->dropColumn('icon');
        });
    }
};
