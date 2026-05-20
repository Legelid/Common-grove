<?php

declare(strict_types=1);

namespace App\Livewire\Feed;

use App\Models\Category;
use App\Models\HangoutPost;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CreateHangoutPost extends Component
{
    /** @deprecated Use $user->persistentRoomLimit() — kept for any view references. */
    public const FREE_ROOM_LIMIT      = 3;
    public const SUPPORTER_ROOM_LIMIT = 10;

    /** 'hangout' | 'room' | '' (not yet chosen) */
    public string $postType = '';

    /** Duration in hours — only relevant when $postType === 'hangout' */
    public string $duration = '4';

    public string $content = '';

    /** @var list<string> Interest tag IDs (1–5 required) */
    public array $selectedTagIds = [];

    /** @var list<string> Shared experience tag IDs (optional, up to 3) */
    public array $selectedExperienceIds = [];

    /** @var list<string> Vibe tag IDs (optional, up to 3) */
    public array $selectedVibeIds = [];

    /** Title for persistent rooms (required when postType === 'room'). */
    public string $roomTitle = '';

    /** Guided category browser state for the Interests section. */
    public ?int $tagCategoryId = null;

    public ?int $tagSubcategoryId = null;

    public string $tagSearch = '';

    public bool $confirmNoBranding = false;

    public bool $hasUrlWarning = false;

    public ?string $roomLimitMessage = null;

    public function selectType(string $type): void
    {
        $this->postType         = $type;
        $this->roomLimitMessage = null;

        if ($type === 'room' && ! Auth::user()->is_admin) {
            $this->checkRoomLimit();
        }
    }

    // ── Guided interest tag browser ───────────────────────────────────────────

    /** @return Collection<int, Category> */
    #[Computed]
    public function tagCategories(): Collection
    {
        return Category::where('is_active', true)->orderBy('sort_order')->get();
    }

    /** @return Collection<int, Subcategory> */
    #[Computed]
    public function tagSubcategories(): Collection
    {
        if ($this->tagCategoryId === null) {
            return collect();
        }

        return Subcategory::where('category_id', $this->tagCategoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Tags to display in the interest browser.
     * — Search ≥ 2 chars → global results (≤ 24)
     * — Subcategory set  → that subcategory's tags
     * — Category set     → top tags for that category (≤ 40)
     * — Nothing set      → empty (category list shown instead)
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function tagSelectionTags(): Collection
    {
        if (strlen($this->tagSearch) < 2 && $this->tagCategoryId === null) {
            return collect();
        }

        if (strlen($this->tagSearch) >= 2) {
            $tags = Tag::approved()
                ->ofType('interest')
                ->where('name', 'like', '%' . $this->tagSearch . '%')
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->limit(24)
                ->get();
        } elseif ($this->tagSubcategoryId !== null) {
            $tags = Tag::approved()
                ->ofType('interest')
                ->where('subcategory_id', $this->tagSubcategoryId)
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->get();
        } else {
            $tags = Tag::approved()
                ->ofType('interest')
                ->where('category_id', $this->tagCategoryId)
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->limit(40)
                ->get();
        }

        return $tags->sortBy(fn (Tag $tag): array => [
            in_array($tag->id, $this->selectedTagIds, true) ? 0 : 1,
            -$tag->usage_count,
            $tag->name,
        ])->values();
    }

    public function setTagCategory(int $categoryId): void
    {
        $this->tagCategoryId    = $this->tagCategoryId === $categoryId ? null : $categoryId;
        $this->tagSubcategoryId = null;
        $this->tagSearch        = '';
    }

    public function setTagSubcategory(int $subcategoryId): void
    {
        $this->tagSubcategoryId = $this->tagSubcategoryId === $subcategoryId ? null : $subcategoryId;
    }

    public function clearTagNav(): void
    {
        $this->tagCategoryId    = null;
        $this->tagSubcategoryId = null;
        $this->tagSearch        = '';
    }

    // ── Shared experience and vibe tags ───────────────────────────────────────

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function experienceTags(): Collection
    {
        return Tag::approved()->ofType('shared_experience')->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function vibeTags(): Collection
    {
        return Tag::approved()->ofType('vibe')->orderBy('name')->get();
    }

    public function toggleInterest(string $tagId): void
    {
        if (in_array($tagId, $this->selectedTagIds, true)) {
            $this->selectedTagIds = array_values(array_diff($this->selectedTagIds, [$tagId]));
        } elseif (count($this->selectedTagIds) < 5) {
            $this->selectedTagIds[] = $tagId;
        }
    }

    public function toggleExperience(string $tagId): void
    {
        if (in_array($tagId, $this->selectedExperienceIds, true)) {
            $this->selectedExperienceIds = array_values(array_diff($this->selectedExperienceIds, [$tagId]));
        } elseif (count($this->selectedExperienceIds) < 3) {
            $this->selectedExperienceIds[] = $tagId;
        }
    }

    public function toggleVibe(string $tagId): void
    {
        if (in_array($tagId, $this->selectedVibeIds, true)) {
            $this->selectedVibeIds = array_values(array_diff($this->selectedVibeIds, [$tagId]));
        } elseif (count($this->selectedVibeIds) < 3) {
            $this->selectedVibeIds[] = $tagId;
        }
    }

    public function updatedContent(): void
    {
        $this->hasUrlWarning = (bool) preg_match('/https?:\/\//i', $this->content);
    }

    /**
     * Create and publish the hangout post or persistent room.
     */
    public function submit(): void
    {
        $this->validate([
            'postType'                   => ['required', 'in:hangout,room'],
            'roomTitle'                  => ['required_if:postType,room', 'nullable', 'string', 'min:1', 'max:60'],
            'duration'                   => ['required_if:postType,hangout', 'in:1,4,12,24'],
            'content'                    => ['required', 'string', 'max:280'],
            'selectedTagIds'             => ['required', 'array', 'min:1', 'max:5'],
            'selectedTagIds.*'           => ['uuid', 'exists:tags,id'],
            'selectedExperienceIds'      => ['array', 'max:3'],
            'selectedExperienceIds.*'    => ['uuid', 'exists:tags,id'],
            'selectedVibeIds'            => ['array', 'max:3'],
            'selectedVibeIds.*'          => ['uuid', 'exists:tags,id'],
            'confirmNoBranding'          => ['accepted'],
        ], [
            'postType.required'          => 'Please choose a type.',
            'postType.in'                => 'Please choose a valid type.',
            'roomTitle.required_if'      => 'Please give your room a name.',
            'roomTitle.max'              => 'Room name must be 60 characters or less.',
            'content.required'           => 'Your post cannot be empty.',
            'selectedTagIds.required'    => 'Pick at least one tag.',
            'selectedTagIds.min'         => 'Pick at least one tag.',
            'selectedTagIds.max'         => 'You can attach at most 5 interest tags.',
            'confirmNoBranding.accepted' => 'Please confirm you are not promoting a stream, channel, or external link.',
        ]);

        if ($this->hasUrlWarning) {
            $this->addError('content', 'Posts cannot contain external links.');
            return;
        }

        $trimmed = trim($this->content);
        if ($trimmed === '') {
            $this->addError('content', 'Your post cannot be empty.');
            return;
        }

        $rateLimitKey = 'create-hangout:' . Auth::id();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = (int) ceil($seconds / 60);
            $this->addError('content', "You've posted too many times. Try again in {$minutes} minute(s).");
            return;
        }

        RateLimiter::hit($rateLimitKey, 3600);

        $isPersistent = $this->postType === 'room';

        // Enforce persistent room creation limit for non-admin users.
        if ($isPersistent && ! Auth::user()->is_admin) {
            if ($this->checkRoomLimit()) {
                return;
            }
        }

        $post = HangoutPost::create([
            'user_id'       => Auth::id(),
            'title'         => $isPersistent ? trim($this->roomTitle) : null,
            'content'       => $trimmed,
            'is_persistent' => $isPersistent,
            'is_official'   => false,
            'expires_at'    => $isPersistent ? null : now()->addHours((int) $this->duration),
            'is_active'     => true,
            'joined_count'  => 1,
        ]);

        $allTagIds = array_values(array_unique(array_merge(
            $this->selectedTagIds,
            $this->selectedExperienceIds,
            $this->selectedVibeIds,
        )));

        $post->tags()->attach($allTagIds);

        $this->redirect(route('feed'), navigate: true);
    }

    /**
     * Check whether the user has hit the persistent room creation limit.
     * Sets $roomLimitMessage and returns true if the limit has been reached.
     * Admins always return false (unlimited).
     */
    private function checkRoomLimit(): bool
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return false;
        }

        $limit = $user->persistentRoomLimit();

        $existing = HangoutPost::where('user_id', $user->id)
            ->where('is_persistent', true)
            ->where('is_official', false)
            ->count();

        if ($existing >= $limit) {
            $this->roomLimitMessage = 'limit_reached';
            return true;
        }

        $this->roomLimitMessage = null;
        return false;
    }

    public function render(): View
    {
        return view('livewire.feed.create-hangout-post')
            ->layout('layouts.app', ['title' => 'Open a Hangout or Room — CommonGrove']);
    }
}
