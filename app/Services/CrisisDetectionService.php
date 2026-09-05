<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CrisisDetection;
use App\Models\CrisisKeyword;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CrisisDetectionService
{
    private const CACHE_KEY = 'crisis_keywords';
    private const CACHE_TTL = 3600;

    /**
     * Checks the message for crisis keywords.
     * If a match is found, logs a detection and returns the matched keyword.
     * Returns null if no match.
     */
    public function check(string $message, ?User $user = null, ?string $conversationId = null): ?string
    {
        $lower    = mb_strtolower($message);
        $keywords = $this->keywords();

        foreach ($keywords as $phrase) {
            if (str_contains($lower, $phrase)) {
                if ($user !== null) {
                    CrisisDetection::create([
                        'user_id'          => $user->id,
                        'conversation_id'  => $conversationId,
                        'triggered_keyword' => $phrase,
                    ]);
                }

                return $phrase;
            }
        }

        return null;
    }

    /**
     * Mark the detection banner as dismissed for a given detection record.
     */
    public function markDismissed(User $user, string $conversationId): void
    {
        CrisisDetection::where('user_id', $user->id)
            ->where('conversation_id', $conversationId)
            ->where('was_dismissed', false)
            ->latest('detected_at')
            ->limit(1)
            ->update(['was_dismissed' => true]);
    }

    /**
     * Returns crisis support resources.
     *
     * @return array<int, array{name: string, contact: string, description: string}>
     */
    public function getResources(): array
    {
        return [
            [
                'name'        => '988 Suicide & Crisis Lifeline',
                'contact'     => 'Call or text 988',
                'description' => 'Free, confidential support 24/7 in the US.',
            ],
            [
                'name'        => 'Crisis Text Line',
                'contact'     => 'Text HOME to 741741',
                'description' => 'Free crisis counseling via text, 24/7.',
            ],
            [
                'name'        => 'IASP — Find a Crisis Centre',
                'contact'     => 'https://www.iasp.info/resources/Crisis_Centres/',
                'description' => 'International directory of crisis centres.',
            ],
        ];
    }

    /** @return list<string> */
    private function keywords(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return CrisisKeyword::pluck('phrase')
                ->map(fn (string $p) => mb_strtolower($p))
                ->values()
                ->all();
        });
    }
}
