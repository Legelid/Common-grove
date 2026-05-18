<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\MoodOption;
use App\Models\Tag;
use App\Rules\ValidGamertag;
use App\Services\GamertagSuggestionService;
use App\Services\SupporterService;
use App\Services\TonePackService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProfileSettings extends Component
{
    // -------------------------------------------------------------------------
    // Identity section
    // -------------------------------------------------------------------------

    public int    $identityMode      = 1;
    public string $displayNameInput  = '';
    public bool   $showNamesPref     = true;

    // -------------------------------------------------------------------------
    // Bio section
    // -------------------------------------------------------------------------

    public string $bio = '';

    // -------------------------------------------------------------------------
    // Gamertag change section
    // -------------------------------------------------------------------------

    public string  $gamertagInput   = '';
    public ?string $gamertagStatus  = null;

    /** @var list<string> */
    public array $gamertagSuggestions = [];

    // -------------------------------------------------------------------------
    // Currently Into section (Group 3)
    // -------------------------------------------------------------------------

    public string $currentlyPlaying  = '';
    public string $currentlyReading  = '';
    public string $currentlyWatching = '';

    // -------------------------------------------------------------------------
    // Read receipts section (Group 9)
    // -------------------------------------------------------------------------

    public bool $showReadReceipts = false;

    // -------------------------------------------------------------------------
    // Profile expression section
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
    // Appearance section
    // -------------------------------------------------------------------------

    public string $personalGradientTheme = '';
    public bool   $birthdayThemeEnabled  = true;
    public bool   $holidayThemesEnabled  = true;

    // -------------------------------------------------------------------------
    // Tone pack section
    // -------------------------------------------------------------------------

    public string  $tonePackKey     = 'default';
    public ?string $tonePackMessage = null;

    // -------------------------------------------------------------------------
    // Advanced comfort section
    // -------------------------------------------------------------------------

    /** @var list<string> */
    public array   $advancedComfortSettings = [];
    public ?string $advancedComfortMessage  = null;

    // -------------------------------------------------------------------------
    // Prompt packs section
    // -------------------------------------------------------------------------

    public bool    $showConversationPrompts = true;
    /** @var list<string> */
    public array   $enabledPromptPacks  = [];
    public ?string $promptPacksMessage  = null;

    // -------------------------------------------------------------------------
    // Reaction visibility section
    // -------------------------------------------------------------------------

    public bool    $hideReactions        = false;
    public ?string $hideReactionsMessage = null;

    // -------------------------------------------------------------------------
    // Discovery section (Group 11)
    // -------------------------------------------------------------------------

    public bool $showConnectionSuggestions = true;
    public bool $lowStimulationMode        = false;
    public bool $showOfficialRooms         = true;

    // -------------------------------------------------------------------------
    // Flash messages (per section)
    // -------------------------------------------------------------------------

    public ?string $identityMessage      = null;
    public ?string $bioMessage           = null;
    public ?string $gamertagMessage      = null;
    public ?string $currentlyMessage     = null;
    public ?string $expressionMessage    = null;
    public ?string $readReceiptsMessage  = null;
    public ?string $discoveryMessage     = null;
    public ?string $officialRoomsMessage = null;
    public ?string $comfortMessage       = null;
    public ?string $appearanceMessage    = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->identityMode      = $user->identity_mode;
        $this->displayNameInput  = $user->getRawOriginal('display_name') ?? '';
        $this->showNamesPref     = $user->show_names_pref;
        $this->bio               = $user->bio ?? '';
        $this->gamertagInput     = $user->gamertag;
        $this->currentlyPlaying  = $user->currently_playing ?? '';
        $this->currentlyReading  = $user->currently_reading ?? '';
        $this->currentlyWatching = $user->currently_watching ?? '';
        $this->profileStatus = $user->profile_status ?? '';
        $this->accentColor   = $user->accent_color ?? '';
        $this->bannerStyle   = $user->banner_style ?? '';
        $this->comfortThings = $user->comfort_things ?? [];
        $this->socialStyles  = $user->social_styles ?? [];
        $this->openTo        = $user->open_to ?? [];
        $this->personalGradientTheme     = $user->personal_gradient_theme ?? '';
        $this->birthdayThemeEnabled      = (bool) ($user->birthday_theme_enabled ?? true);
        $this->holidayThemesEnabled      = (bool) ($user->holiday_themes_enabled ?? true);
        $this->tonePackKey               = $user->tone_pack ?: 'default';
        $this->advancedComfortSettings   = $user->advanced_comfort_settings ?? [];
        $this->showConversationPrompts   = (bool) ($user->show_conversation_prompts ?? true);
        $this->enabledPromptPacks        = $user->enabled_prompt_packs ?? [];
        $this->showReadReceipts          = (bool) $user->show_read_receipts;
        $this->hideReactions             = (bool) ($user->hide_reactions ?? false);
        $this->showConnectionSuggestions = (bool) ($user->show_connection_suggestions ?? true);
        $this->lowStimulationMode        = (bool) ($user->low_stimulation_mode ?? false);
        $this->showOfficialRooms         = (bool) ($user->show_official_rooms ?? true);
    }

    // -------------------------------------------------------------------------
    // Unified save
    // -------------------------------------------------------------------------

    /**
     * Save every settings section at once. Called by the sticky save bar.
     * Individual section validation failures are recorded in the error bag but
     * do not prevent the remaining sections from saving.
     */
    public function saveAll(): void
    {
        $sections = [
            'saveIdentity', 'saveBio', 'saveCurrently', 'saveExpression',
            'saveReadReceiptPref', 'saveDiscoveryPreferences', 'saveOfficialRoomsPref',
            'saveComfortPreferences', 'saveHideReactions', 'saveAdvancedComfort',
            'savePromptPreferences', 'saveAppearance', 'saveTonePack',
        ];

        foreach ($sections as $method) {
            try {
                $this->$method();
            } catch (\Illuminate\Validation\ValidationException) {
                // Error bag is populated; continue saving remaining sections
            }
        }

        $this->dispatch('settings-saved');
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Gradient keys the current user cannot select (empty for supporters/admins).
     *
     * @return list<string>
     */
    #[Computed]
    public function lockedGradientKeys(): array
    {
        return app(SupporterService::class)->lockedGradientKeys(Auth::user());
    }

    #[Computed]
    public function userIsSupporter(): bool
    {
        return Auth::user()->is_admin || Auth::user()->isSupporter();
    }

    /**
     * All prompt pack definitions, each annotated with whether it is locked.
     *
     * @return array<string, array<string, mixed>>
     */
    #[Computed]
    public function promptPacks(): array
    {
        $isSupporter = Auth::user()->is_admin || Auth::user()->isSupporter();
        $packs       = config('prompts.packs', []);

        foreach ($packs as &$pack) {
            $pack['locked'] = ($pack['supporter_only'] ?? false) && ! $isSupporter;
        }

        return $packs;
    }

    /**
     * All tone pack definitions, each annotated with whether it is locked.
     *
     * @return array<string, array<string, mixed>>
     */
    #[Computed]
    public function tonePacks(): array
    {
        $service = app(TonePackService::class);
        $user    = Auth::user();
        $packs   = $service->allPacks();

        foreach ($packs as $key => &$pack) {
            $pack['locked'] = $service->isLocked($user, $key);
        }

        return $packs;
    }

    /**
     * Live preview of how the user's name will appear to others.
     */
    #[Computed]
    public function previewName(): string
    {
        $raw = trim($this->displayNameInput);

        return match ($this->identityMode) {
            2       => Auth::user()->gamertag . ($raw ? ' (tooltip: Prefers ' . $raw . ')' : ''),
            3       => $raw ?: Auth::user()->gamertag,
            default => Auth::user()->gamertag,
        };
    }

    // -------------------------------------------------------------------------
    // Identity section
    // -------------------------------------------------------------------------

    public function saveIdentity(): void
    {
        $this->validate([
            'identityMode'     => ['required', 'integer', 'in:1,2,3'],
            'displayNameInput' => ['nullable', 'string', 'max:50'],
        ]);

        $this->identityMessage = null;

        Auth::user()->update([
            'identity_mode'  => $this->identityMode,
            'display_name'   => trim($this->displayNameInput) ?: null,
            'show_names_pref' => $this->showNamesPref,
        ]);

        $this->identityMessage = 'Identity settings saved.';
    }

    // -------------------------------------------------------------------------
    // Bio section
    // -------------------------------------------------------------------------

    public function saveBio(): void
    {
        $this->validate([
            'bio' => ['nullable', 'string', 'max:300'],
        ]);

        $this->bioMessage = null;

        Auth::user()->update(['bio' => trim($this->bio) ?: null]);

        $this->bioMessage = 'Bio saved.';
    }

    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // Gamertag change section
    // -------------------------------------------------------------------------

    public function updatedGamertagInput(): void
    {
        $this->gamertagStatus      = null;
        $this->gamertagSuggestions = [];
        $this->gamertagMessage     = null;

        $this->checkGamertag();
    }

    public function checkGamertag(): void
    {
        $key = 'gamertag-check.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return;
        }
        RateLimiter::hit($key, 60);

        $currentGamertag = Auth::user()->gamertag;

        // No change — clear status and return
        if (strtolower(trim($this->gamertagInput)) === strtolower($currentGamertag)) {
            $this->gamertagStatus = null;
            return;
        }

        $result = validator(
            ['gamertag' => $this->gamertagInput],
            ['gamertag' => ['required', 'string', new ValidGamertag()]],
        );

        if ($result->fails()) {
            $this->gamertagStatus = null;
            return;
        }

        /** @var GamertagSuggestionService $service */
        $service = app(GamertagSuggestionService::class);

        if ($service->isTaken($this->gamertagInput)) {
            $this->gamertagStatus      = 'taken';
            $this->gamertagSuggestions = $service->suggest($this->gamertagInput);
        } else {
            $this->gamertagStatus      = 'available';
            $this->gamertagSuggestions = [];
        }
    }

    public function changeGamertag(): void
    {
        $this->gamertagMessage = null;

        $user = Auth::user();

        if (strtolower(trim($this->gamertagInput)) === strtolower($user->gamertag)) {
            return;
        }

        // Rate-limit: once every 30 days
        if ($user->last_gamertag_changed_at !== null) {
            $nextAllowed = $user->last_gamertag_changed_at->addDays(30);

            if (now()->isBefore($nextAllowed)) {
                $this->gamertagMessage = 'You can change your gamertag once every 30 days. '
                    . 'Next change available: ' . $nextAllowed->toFormattedDayDateString() . '.';
                return;
            }
        }

        $this->validate([
            'gamertagInput' => ['required', 'string', new ValidGamertag(), 'unique:users,gamertag'],
        ], [
            'gamertagInput.unique' => 'That gamertag is already taken.',
        ]);

        $user->update([
            'gamertag'                 => $this->gamertagInput,
            'last_gamertag_changed_at' => now(),
        ]);

        $this->gamertagStatus  = null;
        $this->gamertagMessage = 'Gamertag updated to ' . $this->gamertagInput . '.';
    }

    // -------------------------------------------------------------------------
    // Currently Into section (Group 3)
    // -------------------------------------------------------------------------

    public function saveCurrently(): void
    {
        $this->validate([
            'currentlyPlaying'  => ['nullable', 'string', 'max:60'],
            'currentlyReading'  => ['nullable', 'string', 'max:60'],
            'currentlyWatching' => ['nullable', 'string', 'max:60'],
        ]);

        $this->currentlyMessage = null;

        Auth::user()->update([
            'currently_playing'  => trim($this->currentlyPlaying) ?: null,
            'currently_reading'  => trim($this->currentlyReading) ?: null,
            'currently_watching' => trim($this->currentlyWatching) ?: null,
        ]);

        $this->currentlyMessage = 'Comfort lately saved.';
    }

    // -------------------------------------------------------------------------
    // Profile expression section
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

    public function saveExpression(): void
    {
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

        $this->expressionMessage = null;

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

        $this->comfortThings     = $comfortFiltered;
        $this->expressionMessage = 'Profile updated.';
    }

    // -------------------------------------------------------------------------
    // Read receipts section (Group 9)
    // -------------------------------------------------------------------------

    public function saveReadReceiptPref(): void
    {
        $this->readReceiptsMessage = null;

        Auth::user()->update(['show_read_receipts' => $this->showReadReceipts]);

        $this->readReceiptsMessage = 'Read receipt preference saved.';
    }

    // -------------------------------------------------------------------------
    // Discovery section (Group 11)
    // -------------------------------------------------------------------------

    public function saveDiscoveryPreferences(): void
    {
        $this->discoveryMessage     = null;
        $this->officialRoomsMessage = null;

        Auth::user()->update([
            'show_connection_suggestions' => $this->showConnectionSuggestions,
            'show_official_rooms'         => $this->showOfficialRooms,
        ]);

        $this->discoveryMessage = $this->showConnectionSuggestions
            ? 'Discovery suggestions turned on.'
            : "Got it — we'll keep things simpler for you.";
    }

    public function saveOfficialRoomsPref(): void
    {
        $this->officialRoomsMessage = null;

        Auth::user()->update(['show_official_rooms' => $this->showOfficialRooms]);

        $this->officialRoomsMessage = $this->showOfficialRooms
            ? 'CommonGrove starter rooms will show in your feed.'
            : "Got it — we'll keep your feed more personal.";
    }

    // -------------------------------------------------------------------------
    // Appearance section
    // -------------------------------------------------------------------------

    public function saveAppearance(): void
    {
        $this->validate([
            'personalGradientTheme' => ['nullable', 'string', 'in:' . implode(',', array_keys(config('gradients')))],
        ]);

        // Silently clear a supporter-only gradient if the user no longer qualifies.
        if ($this->personalGradientTheme !== '' && in_array($this->personalGradientTheme, $this->lockedGradientKeys, true)) {
            $this->personalGradientTheme = '';
        }

        $this->appearanceMessage = null;

        Auth::user()->update([
            'personal_gradient_theme' => $this->personalGradientTheme ?: null,
            'birthday_theme_enabled'  => $this->birthdayThemeEnabled,
            'holiday_themes_enabled'  => $this->holidayThemesEnabled,
        ]);

        $this->appearanceMessage = 'Appearance saved.';
    }

    // -------------------------------------------------------------------------
    // Tone pack section
    // -------------------------------------------------------------------------

    public function saveTonePack(): void
    {
        $validKeys = array_keys(config('tone_packs', []));

        if (! in_array($this->tonePackKey, $validKeys, true)) {
            return;
        }

        // Silently reset to default if the user selects a supporter pack without access.
        if (app(TonePackService::class)->isLocked(Auth::user(), $this->tonePackKey)) {
            $this->tonePackKey = 'default';
        }

        Auth::user()->update([
            'tone_pack' => $this->tonePackKey !== 'default' ? $this->tonePackKey : null,
        ]);

        $this->tonePackMessage = 'Tone pack saved. Refresh to hear the new voice.';
    }

    // -------------------------------------------------------------------------
    // Advanced comfort section
    // -------------------------------------------------------------------------

    private const ADVANCED_COMFORT_KEYS = [
        'ultra_minimal', 'extra_spacing', 'simple_room_cards', 'hide_gradients',
        'reduce_sidebar', 'hide_suggestions', 'hide_phrases', 'compact_chat', 'larger_text',
    ];

    public function toggleAdvancedComfort(string $key): void
    {
        if (! in_array($key, self::ADVANCED_COMFORT_KEYS, true)) {
            return;
        }

        if (! $this->userIsSupporter) {
            return;
        }

        if (in_array($key, $this->advancedComfortSettings, true)) {
            $this->advancedComfortSettings = array_values(
                array_diff($this->advancedComfortSettings, [$key]),
            );
        } else {
            $this->advancedComfortSettings[] = $key;
        }
    }

    public function saveAdvancedComfort(): void
    {
        $this->advancedComfortMessage = null;

        if (! $this->userIsSupporter) {
            $this->advancedComfortSettings = [];
        }

        $validated = array_values(array_filter(
            $this->advancedComfortSettings,
            static fn (string $k): bool => in_array($k, self::ADVANCED_COMFORT_KEYS, true),
        ));

        $this->advancedComfortSettings = $validated;

        Auth::user()->update([
            'advanced_comfort_settings' => ! empty($validated) ? $validated : null,
        ]);

        $this->advancedComfortMessage = 'Comfort settings saved.';
    }

    // -------------------------------------------------------------------------
    // Prompt packs section
    // -------------------------------------------------------------------------

    public function togglePromptPack(string $packKey): void
    {
        $allPacks = config('prompts.packs', []);

        if (! isset($allPacks[$packKey])) {
            return;
        }

        $pack        = $allPacks[$packKey];
        $isSupporter = Auth::user()->is_admin || Auth::user()->isSupporter();

        if (($pack['supporter_only'] ?? false) && ! $isSupporter) {
            return;
        }

        if (in_array($packKey, $this->enabledPromptPacks, true)) {
            $this->enabledPromptPacks = array_values(array_diff($this->enabledPromptPacks, [$packKey]));
        } else {
            $this->enabledPromptPacks[] = $packKey;
        }
    }

    public function savePromptPreferences(): void
    {
        $this->promptPacksMessage = null;

        $allPacks    = config('prompts.packs', []);
        $isSupporter = Auth::user()->is_admin || Auth::user()->isSupporter();

        $validated = array_values(array_filter(
            $this->enabledPromptPacks,
            static function (string $k) use ($allPacks, $isSupporter): bool {
                if (! isset($allPacks[$k])) {
                    return false;
                }

                return ! (($allPacks[$k]['supporter_only'] ?? false) && ! $isSupporter);
            },
        ));

        $this->enabledPromptPacks = $validated;

        Auth::user()->update([
            'show_conversation_prompts' => $this->showConversationPrompts,
            'enabled_prompt_packs'      => ! empty($validated) ? $validated : null,
        ]);

        $this->promptPacksMessage = 'Conversation prompt preferences saved.';
    }

    // -------------------------------------------------------------------------
    // Reaction visibility section
    // -------------------------------------------------------------------------

    public function saveHideReactions(): void
    {
        $this->hideReactionsMessage = null;

        Auth::user()->update(['hide_reactions' => $this->hideReactions]);

        $this->hideReactionsMessage = $this->hideReactions
            ? 'Reactions hidden.'
            : 'Reactions visible.';
    }

    // -------------------------------------------------------------------------
    // Comfort section
    // -------------------------------------------------------------------------

    public function saveComfortPreferences(): void
    {
        $this->comfortMessage = null;

        Auth::user()->update([
            'low_stimulation_mode' => $this->lowStimulationMode,
        ]);

        $this->comfortMessage = $this->lowStimulationMode
            ? 'Low-stimulation mode on. Suggestions and effects are hidden.'
            : 'Full experience restored.';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profile.profile-settings')
            ->layout('layouts.app', ['title' => 'Profile Settings — CommonGround']);
    }
}
