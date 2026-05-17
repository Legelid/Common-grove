<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\MoodOption;
use App\Models\Tag;
use App\Rules\ValidGamertag;
use App\Services\GamertagSuggestionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\Exceptions\RuntimeException as ImageRuntimeException;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileSettings extends Component
{
    use WithFileUploads;

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
    // Avatar section
    // -------------------------------------------------------------------------

    #[Validate(['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'])]
    public $avatarUpload = null;

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

    public string $profileStatus      = '';
    public string $accentColor        = '';
    public string $bannerStyle        = '';
    public string $promptComfortThing = '';
    public string $promptRambleTopic  = '';

    /** @var list<string> */
    public array $socialStyles = [];

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
    public ?string $avatarMessage        = null;
    public ?string $gamertagMessage      = null;
    public ?string $currentlyMessage     = null;
    public ?string $expressionMessage    = null;
    public ?string $readReceiptsMessage  = null;
    public ?string $discoveryMessage     = null;
    public ?string $officialRoomsMessage = null;
    public ?string $comfortMessage       = null;

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
        $this->profileStatus      = $user->profile_status ?? '';
        $this->accentColor        = $user->accent_color ?? '';
        $this->bannerStyle        = $user->banner_style ?? '';
        $this->promptComfortThing = $user->prompt_comfort_thing ?? '';
        $this->promptRambleTopic  = $user->prompt_ramble_topic ?? '';
        $this->socialStyles       = $user->social_styles ?? [];
        $this->showReadReceipts          = (bool) $user->show_read_receipts;
        $this->showConnectionSuggestions = (bool) ($user->show_connection_suggestions ?? true);
        $this->lowStimulationMode        = (bool) ($user->low_stimulation_mode ?? false);
        $this->showOfficialRooms         = (bool) ($user->show_official_rooms ?? true);
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Live preview of how the user's name will appear to others.
     */
    #[Computed]
    public function previewName(): string
    {
        $raw = trim($this->displayNameInput);

        return match ($this->identityMode) {
            2       => $raw ? Auth::user()->gamertag . ' · goes by ' . $raw : Auth::user()->gamertag,
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
    // Avatar section
    // -------------------------------------------------------------------------

    public function saveAvatar(): void
    {
        $this->validateOnly('avatarUpload', [
            'avatarUpload' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $this->avatarMessage = null;

        $user      = Auth::user();
        $ext       = strtolower($this->avatarUpload->getClientOriginalExtension());
        $outputExt = in_array($ext, ['png', 'webp'], true) ? $ext : 'jpg';
        $filename  = Str::uuid() . '.' . $outputExt;
        $path      = 'avatars/' . $user->id . '/' . $filename;

        try {
            $manager = new ImageManager(new GdDriver());
            $image   = $manager->read($this->avatarUpload->getPathname());

            $imageData = match ($outputExt) {
                'png'  => (string) $image->encode(new PngEncoder()),
                'webp' => (string) $image->encode(new WebpEncoder(quality: 85)),
                default => (string) $image->encode(new JpegEncoder(quality: 85)),
            };
        } catch (DecoderException | ImageRuntimeException) {
            $this->avatarMessage = "We couldn't process that image. Try a different one.";
            $this->avatarUpload  = null;
            return;
        }

        // Delete the previous avatar before storing the new one
        if ($user->avatar_path) {
            Storage::disk('s3')->delete($user->avatar_path);
        }

        Storage::disk('s3')->put($path, $imageData);

        $user->update(['avatar_path' => $path]);

        $this->avatarUpload  = null;
        $this->avatarMessage = 'Avatar updated.';
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar_path) {
            Storage::disk('s3')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        $this->avatarMessage = 'Avatar removed.';
    }

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

        $this->currentlyMessage = 'Currently Into saved.';
    }

    // -------------------------------------------------------------------------
    // Profile expression section
    // -------------------------------------------------------------------------

    public function toggleSocialStyle(string $style): void
    {
        $allowed = ['quiet_chatter', 'mostly_listening', 'slow_replies', 'deep_talks', 'late_night', 'introvert_friendly'];

        if (! in_array($style, $allowed, true)) {
            return;
        }

        if (in_array($style, $this->socialStyles, true)) {
            $this->socialStyles = array_values(array_diff($this->socialStyles, [$style]));
        } else {
            $this->socialStyles[] = $style;
        }
    }

    public function saveExpression(): void
    {
        $this->validate([
            'profileStatus'      => ['nullable', 'string', 'max:120'],
            'accentColor'        => ['nullable', 'string', 'in:green,blue,amber,purple,slate'],
            'bannerStyle'        => ['nullable', 'string', 'in:rain_window,forest,night_sky,cozy_room,gradient'],
            'promptComfortThing' => ['nullable', 'string', 'max:120'],
            'promptRambleTopic'  => ['nullable', 'string', 'max:120'],
            'socialStyles'       => ['array', 'max:6'],
            'socialStyles.*'     => ['string', 'in:quiet_chatter,mostly_listening,slow_replies,deep_talks,late_night,introvert_friendly'],
        ]);

        $this->expressionMessage = null;

        Auth::user()->update([
            'profile_status'       => trim($this->profileStatus) ?: null,
            'accent_color'         => $this->accentColor ?: null,
            'banner_style'         => $this->bannerStyle ?: null,
            'prompt_comfort_thing' => trim($this->promptComfortThing) ?: null,
            'prompt_ramble_topic'  => trim($this->promptRambleTopic) ?: null,
            'social_styles'        => ! empty($this->socialStyles) ? array_values($this->socialStyles) : null,
        ]);

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
