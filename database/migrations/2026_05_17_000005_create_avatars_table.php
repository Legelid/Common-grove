<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatars', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->string('category', 100)->index();
            $table->string('image_path', 255);
            $table->enum('status', ['active', 'retired', 'disabled'])->default('active')->index();
            $table->timestamps();

            $table->unique(['category', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatars');
    }
};
