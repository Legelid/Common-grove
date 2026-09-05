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
        Schema::table('subcategories', function (Blueprint $table): void {
            $table->boolean('is_sensitive')->default(false)->after('is_active');
        });

        // Backfill: every subcategory under "Identity, Support & Shared
        // Experiences" is sensitive. Replaces the string-matching done in
        // InterestPrivacyService with a real, queryable flag.
        $categoryId = DB::table('categories')
            ->where('name', 'Identity, Support & Shared Experiences')
            ->value('id');

        if ($categoryId !== null) {
            DB::table('subcategories')
                ->where('category_id', $categoryId)
                ->update(['is_sensitive' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('subcategories', function (Blueprint $table): void {
            $table->dropColumn('is_sensitive');
        });
    }
};
