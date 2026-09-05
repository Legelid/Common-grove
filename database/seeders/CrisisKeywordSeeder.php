<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CrisisKeyword;
use Illuminate\Database\Seeder;

class CrisisKeywordSeeder extends Seeder
{
    public function run(): void
    {
        $phrases = [
            'want to kill myself',
            'want to die',
            'thinking about suicide',
            'ending my life',
            'end it all',
            'no reason to live',
            'can\'t go on',
            'don\'t want to be here anymore',
            'thinking about ending it',
            'suicidal thoughts',
            'hurt myself',
            'self harm',
            'cutting myself',
            'overdose',
            'nothing to live for',
        ];

        foreach ($phrases as $phrase) {
            CrisisKeyword::firstOrCreate(['phrase' => $phrase]);
        }
    }
}
