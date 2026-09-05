<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name'        => 'Conversation Preferences',
            'slug'        => 'conversation-preferences',
            'description' => 'How you like to interact',
            'sort_order'  => 11,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $subcategoryId = DB::table('subcategories')->insertGetId([
            'category_id' => $categoryId,
            'name'        => 'Conversation Style',
            'slug'        => 'conversation-style',
            'is_sensitive' => false,
            'sort_order'  => 0,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Move every 'vibe'-type tag (conversation-preference tags that were
        // mixed into Identity/Support & Shared Experiences per the Phase 0
        // audit) into the new category. The explicit name list from the task
        // is folded in too, in case any conversation-style tag isn't typed
        // 'vibe' — as verified, every match found was already type='vibe'.
        $names = ['Casual', 'Chill', 'Deep talks', 'Quiet', 'Slow chat', 'Advice welcome', 'Listening only', 'Low pressure'];

        DB::table('tags')
            ->where('type', 'vibe')
            ->orWhereIn('name', $names)
            ->update([
                'category_id'    => $categoryId,
                'subcategory_id' => $subcategoryId,
                'category'       => 'Conversation Preferences',
            ]);
    }

    public function down(): void
    {
        $categoryId = DB::table('categories')->where('slug', 'conversation-preferences')->value('id');

        if ($categoryId !== null) {
            // Tags moved here have no safe automatic home to revert to —
            // leave them pointed at this category's row until it's deleted,
            // then null out the now-dangling FK rather than guess a category.
            DB::table('tags')->where('category_id', $categoryId)->update([
                'category_id'    => null,
                'subcategory_id' => null,
            ]);

            DB::table('subcategories')->where('category_id', $categoryId)->delete();
            DB::table('categories')->where('id', $categoryId)->delete();
        }
    }
};
