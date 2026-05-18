<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('show_conversation_prompts')->default(true)->after('tone_pack');
            $table->json('enabled_prompt_packs')->nullable()->after('show_conversation_prompts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['show_conversation_prompts', 'enabled_prompt_packs']);
        });
    }
};
