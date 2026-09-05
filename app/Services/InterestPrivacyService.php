<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Centralizes the "never name a sensitive or private interest" rule so it's
 * enforced identically everywhere shared interests are surfaced (People
 * page suggestions, room participant previews), not duplicated per call
 * site where it could drift.
 *
 * Sensitivity is determined by `subcategories.is_sensitive` (Phase 1 of the
 * interests overhaul) rather than string-matching a category name — the
 * flag is queryable and doesn't silently stop working if a category is
 * ever renamed.
 */
class InterestPrivacyService
{
    /**
     * Tags both users have selected, excluding tags whose subcategory is
     * flagged sensitive, and excluding any tag the owner (`$other`) has
     * chosen to hide via their `comfort_preferences.hidden_interest_ids`.
     *
     * @return Collection<int, Tag>
     */
    public function safeSharedTags(User $viewer, User $other, int $limit = 3): Collection
    {
        $viewerTagIds = $viewer->tags()->pluck('tag_id');

        if ($viewerTagIds->isEmpty()) {
            return collect();
        }

        $hiddenByOther = $this->hiddenTagIds($other);

        return $other->tags()
            ->whereIn('tags.id', $viewerTagIds)
            ->when(! empty($hiddenByOther), fn ($q) => $q->whereNotIn('tags.id', $hiddenByOther))
            ->where(function ($query): void {
                $query->whereDoesntHave('subcategory')
                    ->orWhereHas('subcategory', fn ($q) => $q->where('is_sensitive', false));
            })
            ->limit($limit)
            ->get();
    }

    /**
     * The IDs of interests this user has chosen to hide from others, per
     * their `comfort_preferences` (Phase 4 of the interests overhaul).
     *
     * @return list<string>
     */
    private function hiddenTagIds(User $user): array
    {
        return ($user->comfort_preferences ?? [])['hidden_interest_ids'] ?? [];
    }

    /**
     * Total shared-interest count across ALL categories, including
     * sensitive ones. Safe to expose as a bare number since a count never
     * reveals which interest — only "how many."
     */
    public function sharedTagCount(User $viewer, User $other): int
    {
        $viewerTagIds = $viewer->tags()->pluck('tag_id');

        if ($viewerTagIds->isEmpty()) {
            return 0;
        }

        return $other->tags()->whereIn('tags.id', $viewerTagIds)->count();
    }

    /**
     * Privacy-safe explanation copy for why someone is suggested.
     * Only ever names a specific interest when there is exactly one shared
     * interest AND it's outside a sensitive category — every other case
     * (zero safe interests to name, or more than one shared interest)
     * falls back to a generic, non-naming phrase.
     */
    public function explanation(User $viewer, User $other): ?string
    {
        $total = $this->sharedTagCount($viewer, $other);

        if ($total === 0) {
            return null;
        }

        if ($total > 1) {
            return "You have {$total} things in common.";
        }

        $safe = $this->safeSharedTags($viewer, $other, 1);

        return $safe->isNotEmpty()
            ? "You both enjoy {$safe->first()->name}."
            : 'You have things in common.';
    }

    /**
     * Specificity-aware match explanation (Phase 5): prefers naming an
     * exact shared interest, falls back to naming a shared subcategory,
     * then a shared category, and only ever falls back to a fully generic
     * phrase when something was shared but none of it is safe to name.
     * Returns "" when there's truly nothing shared at any tier — callers
     * decide how to handle that (e.g. person-card's `@if ($explanation)`
     * already treats "" the same as no explanation).
     *
     * $sharedTagIds are the caller's already-known EXACT tag overlap
     * (e.g. from the Phase 5 scoring subquery) — passed in to avoid a
     * redundant re-query; subcategory/category overlap is still derived
     * fresh from both users' full tag sets, since by definition it
     * involves tags outside $sharedTagIds.
     *
     * Hidden interests (comfort_preferences.hidden_interest_ids on
     * $subject) and sensitive-subcategory tags are excluded from being
     * named at every tier, on either side of the comparison — a subject's
     * hidden tag can never be named, and a sensitive subcategory/category
     * is never named regardless of whose tag it came from.
     *
     * @param  array<int, string>  $sharedTagIds
     */
    public function getMatchExplanation(User $viewer, User $subject, array $sharedTagIds): string
    {
        $hiddenBySubject = $this->hiddenTagIds($subject);

        $exactAll = Tag::whereIn('id', $sharedTagIds)->with('subcategory')->get();

        $exactSafe = $exactAll
            ->reject(fn (Tag $t) => in_array($t->id, $hiddenBySubject, true))
            ->reject(fn (Tag $t) => $t->subcategory?->is_sensitive === true);

        if ($exactSafe->isNotEmpty()) {
            return $this->exactMatchCopy($exactSafe);
        }

        $viewerTags  = $viewer->tags()->with(['subcategory', 'category'])->get();
        $subjectTags = $subject->tags()->with(['subcategory', 'category'])->get();
        $subjectSafe = $subjectTags->reject(fn (Tag $t) => in_array($t->id, $hiddenBySubject, true));

        $isSafe = fn (Tag $t) => $t->subcategory === null || ! $t->subcategory->is_sensitive;

        $viewerSafeSubcatIds = $viewerTags->filter(fn (Tag $t) => $t->subcategory_id !== null && $isSafe($t))->pluck('subcategory_id')->unique();
        $subjectSafeBySubcat = $subjectSafe->filter(fn (Tag $t) => $t->subcategory_id !== null && $isSafe($t))->keyBy('subcategory_id');

        $sharedSubcatId = $viewerSafeSubcatIds->first(fn ($id) => $subjectSafeBySubcat->has($id));

        if ($sharedSubcatId !== null) {
            return "You're both into {$subjectSafeBySubcat[$sharedSubcatId]->subcategory->name}.";
        }

        $viewerSafeCatIds = $viewerTags->filter(fn (Tag $t) => $t->category_id !== null && $isSafe($t))->pluck('category_id')->unique();
        $subjectSafeByCat = $subjectSafe->filter(fn (Tag $t) => $t->category_id !== null && $isSafe($t))->keyBy('category_id');

        $sharedCatId = $viewerSafeCatIds->first(fn ($id) => $subjectSafeByCat->has($id));

        if ($sharedCatId !== null) {
            $category = $subjectSafeByCat[$sharedCatId]->category;

            if ($category !== null) {
                return "You both enjoy {$category->name}.";
            }
        }

        $hadAnyOverlap = $exactAll->isNotEmpty()
            || $viewerTags->pluck('subcategory_id')->filter()->intersect($subjectTags->pluck('subcategory_id')->filter())->isNotEmpty()
            || $viewerTags->pluck('category_id')->filter()->intersect($subjectTags->pluck('category_id')->filter())->isNotEmpty();

        return $hadAnyOverlap ? 'You have things in common.' : '';
    }

    /**
     * Renders copy for one or more exact safe-to-name shared tags,
     * escalating from naming them to a bare count as the list grows.
     *
     * @param  Collection<int, Tag>  $exactSafe
     */
    private function exactMatchCopy(Collection $exactSafe): string
    {
        $names = $exactSafe->pluck('name');
        $count = $names->count();

        if ($count === 1) {
            return "You both enjoy {$names->first()}.";
        }

        if ($count <= 3) {
            return 'You both enjoy ' . $names->slice(0, -1)->implode(', ') . ' and ' . $names->last() . '.';
        }

        return "You have {$count} interests in common.";
    }
}
