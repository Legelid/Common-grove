<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\Avatar;
use App\Models\HangoutPost;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EditProfileCustomization extends Component
{
    // -------------------------------------------------------------------------
    // Avatar
    // -------------------------------------------------------------------------

    public ?int $selectedAvatarId = null;

    // -------------------------------------------------------------------------
    // Profile expression fields
    // -------------------------------------------------------------------------

    public string $profileStatus = '';
    public string $accentColor   = '';
    public string $bannerStyle   = '';

    /** @var list<array{label: string, value: string}> */
    public array $comfortThings = [];

    /** @var list<string> */
    public array $socialStyles = [];

    /** @var list<string> */
    public array $openTo = [];

    // -------------------------------------------------------------------------
    // Interest tag search
    // -------------------------------------------------------------------------

    public string $tagSearch = '';

    // -------------------------------------------------------------------------
    // Allowed values (mirrors ProfileSettings)
    // -------------------------------------------------------------------------

    private const BANNER_STYLES = [
        'night_rain', 'forest', 'cozy_room', 'pixel_sky', 'aquarium', 'snowfall',
        'sunset_fog', 'moonlight', 'coffee_shop', 'soft_abstract', 'deep_blue', 'warm_lamp',
    ];

    private const SOCIAL_STYLES = [
        'quiet_chatter', 'mostly_listening', 'slow_replies', 'deep_talks', 'late_night',
        'introvert_friendly', 'listener_first', 'casual_conversations', 'low_pressure',
        'group_chats_okay', 'one_on_one_preferred', 'small_groups', 'usually_multitasking',
        'social_battery', 'thoughtful_replies', 'cozy_energy', 'random_conversations',
        'comfortable_online', 'sometimes_awkward', 'better_warmed_up', 'open_to_friends',
        'quiet_but_friendly', 'easygoing', 'rambles_sometimes', 'comfortable_silence',
    ];

    private const OPEN_TO_OPTIONS = [
        'new_friends', 'quiet_conversations', 'group_chats', 'one_on_one_chats',
        'shared_hobbies', 'deep_talks', 'casual_conversation', 'listening_more',
        'gaming_together', 'book_discussions', 'slow_conversations', 'creative_discussions',
        'nighttime_chats', 'similar_experiences', 'just_existing', 'advice_support',
        'meeting_slowly', 'cozy_conversation', 'joining_quietly', 'talking_when_comfortable',
    ];

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    public function mount(): void
    {
        Gate::authorize('update', Auth::user());

        $user                 = Auth::user();
        $this->selectedAvatarId = $user->avatar_id;
        $this->profileStatus = $user->profile_status ?? '';
        $this->accentColor   = $user->accent_color ?? '';
        $this->bannerStyle   = $user->banner_style ?? '';
        $this->comfortThings = $user->comfort_things ?? [];
        $this->socialStyles  = $user->social_styles ?? [];
        $this->openTo        = $user->open_to ?? [];
    }

    // -------------------------------------------------------------------------
    // Computed — interests
    // -------------------------------------------------------------------------

    /**
     * The authenticated user's currently selected interest tags.
     *
     * @return EloquentCollection<int, Tag>
     */
    #[Computed]
    public function selectedTags(): EloquentCollection
    {
        return Auth::user()->tags()->ofType('interest')->orderByDesc('usage_count')->get();
    }

    /**
     * Live search results for adding interest tags (excludes already-selected).
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function tagSearchResults(): Collection
    {
        if (strlen($this->tagSearch) < 2) {
            return collect();
        }

        $selectedIds = Auth::user()->tags()->pluck('tags.id');

        return Tag::approved()
            ->ofType('interest')
            ->where('name', 'like', '%' . $this->tagSearch . '%')
            ->whereNotIn('id', $selectedIds)
            ->orderByDesc('usage_count')
            ->limit(12)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Computed — avatar picker
    // -------------------------------------------------------------------------

    /**
     * Active avatars grouped by category, ordered to match config/avatars.php.
     * Only active avatars appear in the picker; retired ones stay on profiles.
     *
     * @return Collection<string, EloquentCollection<int, Avatar>>
     */
    #[Computed]
    public function activeAvatarsByCategory(): Collection
    {
        $categoryOrder = array_keys(config('avatars.categories'));

        return Avatar::active()
            ->orderBy('id')
            ->get()
            ->groupBy('category')
            ->sortBy(static fn ($_, string $key): int => (int) array_search($key, $categoryOrder, true));
    }

    // -------------------------------------------------------------------------
    // Computed — usually found in (read-only)
    // -------------------------------------------------------------------------

    /**
     * @return EloquentCollection<int, HangoutPost>
     */
    #[Computed]
    public function usualRooms(): EloquentCollection
    {
        return HangoutPost::where('user_id', Auth::id())
            ->where('is_persistent', true)
            ->where('is_active', true)
            ->where('is_official', false)
            ->whereNotNull('title')
            ->orderByDesc('joined_count')
            ->limit(3)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Interest tag actions
    // -------------------------------------------------------------------------

    public function addTag(string $tagId): void
    {
        $tag = Tag::approved()->ofType('interest')->find($tagId);

        if ($tag) {
            Auth::user()->selectTag($tag);
            unset($this->selectedTags, $this->tagSearchResults);
            $this->tagSearch = '';
        }
    }

    public function removeTag(string $tagId): void
    {
        $tag = Tag::find($tagId);

        if ($tag) {
            Auth::user()->deselectTag($tag);
            unset($this->selectedTags);
        }
    }

    public function updatedTagSearch(): void
    {
        unset($this->tagSearchResults);
    }

    // -------------------------------------------------------------------------
    // Comfort things
    // -------------------------------------------------------------------------

    public function addComfortThing(): void
    {
        if (count($this->comfortThings) < 5) {
            $this->comfortThings[] = ['label' => '', 'value' => ''];
        }
    }

    public function removeComfortThing(int $index): void
    {
        array_splice($this->comfortThings, $index, 1);
        $this->comfortThings = array_values($this->comfortThings);
    }

    // -------------------------------------------------------------------------
    // Social style / open to toggles
    // -------------------------------------------------------------------------

    public function toggleSocialStyle(string $style): void
    {
        if (! in_array($style, self::SOCIAL_STYLES, true)) {
            return;
        }

        if (in_array($style, $this->socialStyles, true)) {
            $this->socialStyles = array_values(array_diff($this->socialStyles, [$style]));
        } elseif (count($this->socialStyles) < 5) {
            $this->socialStyles[] = $style;
        }
    }

    public function toggleOpenTo(string $option): void
    {
        if (! in_array($option, self::OPEN_TO_OPTIONS, true)) {
            return;
        }

        if (in_array($option, $this->openTo, true)) {
            $this->openTo = array_values(array_diff($this->openTo, [$option]));
        } else {
            $this->openTo[] = $option;
        }
    }

    // -------------------------------------------------------------------------
    // Avatar — curated selection
    // -------------------------------------------------------------------------

    public function selectAvatar(int $id): void
    {
        Gate::authorize('update', Auth::user());

        // Only active avatars may be selected via the picker
        $avatar = Avatar::active()->find($id);
        if ($avatar === null) {
            return;
        }

        $user    = Auth::user();
        $oldPath = $user->avatar_path;

        // Delete any legacy S3 upload being replaced
        if ($oldPath && ! str_starts_with($oldPath, 'curated:')) {
            Storage::disk('s3')->delete($oldPath);
        }

        $user->update(['avatar_id' => $avatar->id, 'avatar_path' => null]);
        $this->selectedAvatarId = $avatar->id;
        unset($this->activeAvatarsByCategory);
    }

    public function resetAvatar(): void
    {
        Gate::authorize('update', Auth::user());

        $user    = Auth::user();
        $oldPath = $user->avatar_path;

        if ($oldPath && ! str_starts_with($oldPath, 'curated:')) {
            Storage::disk('s3')->delete($oldPath);
        }

        $user->update(['avatar_id' => null, 'avatar_path' => null]);
        $this->selectedAvatarId = null;
    }

    // -------------------------------------------------------------------------
    // Save
    // -------------------------------------------------------------------------

    public function save(): void
    {
        Gate::authorize('update', Auth::user());

        $this->validate([
            'profileStatus'          => ['nullable', 'string', 'max:120'],
            'accentColor'            => ['nullable', 'string', 'in:green,blue,amber,purple,slate'],
            'bannerStyle'            => ['nullable', 'string', 'in:' . implode(',', self::BANNER_STYLES)],
            'comfortThings'          => ['array', 'max:5'],
            'comfortThings.*.label'  => ['nullable', 'string', 'max:60'],
            'comfortThings.*.value'  => ['nullable', 'string', 'max:120'],
            'socialStyles'           => ['array', 'max:5'],
            'socialStyles.*'         => ['string', 'in:' . implode(',', self::SOCIAL_STYLES)],
            'openTo'                 => ['array'],
            'openTo.*'               => ['string', 'in:' . implode(',', self::OPEN_TO_OPTIONS)],
        ]);

        $comfortFiltered = array_values(array_filter(
            $this->comfortThings,
            static fn (array $t): bool => trim($t['value'] ?? '') !== '',
        ));

        Auth::user()->update([
            'profile_status' => trim($this->profileStatus) ?: null,
            'accent_color'   => $this->accentColor ?: null,
            'banner_style'   => $this->bannerStyle ?: null,
            'comfort_things' => ! empty($comfortFiltered) ? $comfortFiltered : null,
            'social_styles'  => ! empty($this->socialStyles) ? array_values($this->socialStyles) : null,
            'open_to'        => ! empty($this->openTo) ? array_values($this->openTo) : null,
        ]);

        $this->redirect(route('profile.show', Auth::user()->gamertag), navigate: true);
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.profile.edit-profile-customization')
            ->layout('layouts.app', ['title' => 'Edit Profile | CommonGrove']);
    }
}
