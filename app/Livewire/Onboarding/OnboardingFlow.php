<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Rules\ValidCustomTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OnboardingFlow extends Component
{
    public int $step = 1;

    public string $displayName = '';

    public ?int $onboardingCategoryId = null;

    public string $onboardingSearch = '';

    public ?string $onboardingCustomMessage = null;

    /** @var list<string> */
    public array $selectedInterestIds = [];

    /** @var list<string> */
    public array $selectedExperienceIds = [];

    /** @var list<string> */
    public array $selectedComfortOptions = [];

    /** Available comfort options (not stored as tags — stored as JSON). */
    public const COMFORT_OPTIONS = [
        'talk-groups'         => 'I want to talk in groups',
        'listen-first'        => 'I\'d rather listen first',
        'bad-at-starting'     => 'I\'m not great at starting conversations',
        'just-exist'          => 'I just want somewhere to exist for now',
        'quiet-rooms'         => 'I like quiet rooms',
        'casual-rooms'        => 'I like casual rooms',
        'advice-ok'           => 'Advice is okay',
        'no-unsolicited-advice' => 'No advice unless I ask',
    ];

    public function mount(): void
    {
        if (Auth::user()->onboarding_completed) {
            $this->redirect(route('feed'), navigate: true);
            return;
        }

        $raw = Auth::user()->getRawOriginal('display_name');
        $this->displayName = $raw ?? '';
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function interestCategories(): Collection
    {
        return Category::where('is_active', true)
            ->where('slug', '!=', 'identity-support-shared-experiences')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Subcategories with their interest tags for the selected onboarding category.
     *
     * @return Collection<int, Subcategory>
     */
    #[Computed]
    public function onboardingSubcats(): Collection
    {
        if ($this->onboardingCategoryId === null) {
            return collect();
        }

        return Subcategory::where('category_id', $this->onboardingCategoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['tags' => fn ($q) => $q->approved()->ofType('interest')->orderBy('name')])
            ->get();
    }

    /**
     * Tags matching the onboarding search query across all interest categories.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function onboardingSearchResults(): Collection
    {
        $q = trim($this->onboardingSearch);

        if ($q === '') {
            return collect();
        }

        return Tag::approved()
            ->ofType('interest')
            ->where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(40)
            ->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function experienceTags(): Collection
    {
        return Tag::approved()->ofType('shared_experience')->orderBy('name')->get();
    }

    public function setOnboardingCategory(?int $categoryId): void
    {
        Log::info('Onboarding.action', ['action' => 'setOnboardingCategory', 'step' => $this->step, 'auth' => Auth::id()]);
        $this->onboardingCategoryId = $this->onboardingCategoryId === $categoryId ? null : $categoryId;
        $this->onboardingSearch     = '';
        unset($this->onboardingSubcats);
        unset($this->onboardingSearchResults);
    }

    // ── Step navigation ───────────────────────────────────────────────────────

    public function next(): void
    {
        Log::info('Onboarding.action', ['action' => 'next', 'step' => $this->step, 'auth' => Auth::id()]);
        if ($this->step === 2) {
            $trimmed = trim($this->displayName);
            if ($trimmed !== '') {
                $this->validate(['displayName' => ['string', 'max:50']]);
            }
        }

        $this->step = min($this->step + 1, 6);
    }

    public function back(): void
    {
        Log::info('Onboarding.action', ['action' => 'back', 'step' => $this->step, 'auth' => Auth::id()]);
        $this->step = max($this->step - 1, 1);
    }

    public function skipToStep(int $target): void
    {
        Log::info('Onboarding.action', ['action' => 'skipToStep', 'target' => $target, 'auth' => Auth::id()]);
        $this->step = $target;
    }

    // ── Toggle helpers ────────────────────────────────────────────────────────

    public function toggleInterest(string $tagId): void
    {
        Log::info('Onboarding.action', ['action' => 'toggleInterest', 'step' => $this->step, 'auth' => Auth::id()]);
        if (in_array($tagId, $this->selectedInterestIds, true)) {
            $this->selectedInterestIds = array_values(
                array_diff($this->selectedInterestIds, [$tagId])
            );
        } else {
            if (count($this->selectedInterestIds) < 30) {
                $this->selectedInterestIds[] = $tagId;
            }
        }
    }

    /**
     * Create a personal custom interest from the onboarding search term and
     * queue it for selection on complete(). The tag itself is persisted
     * immediately so complete() can attach it via selectTag().
     */
    public function addCustomInterest(): void
    {
        $this->onboardingCustomMessage = null;
        Log::info('Onboarding.action', ['action' => 'addCustomInterest', 'step' => $this->step, 'auth' => Auth::id()]);

        $name = (string) preg_replace('/\s+/', ' ', trim($this->onboardingSearch));

        if ($name === '') {
            return;
        }

        if (count($this->selectedInterestIds) >= 30) {
            $this->onboardingCustomMessage = 'You\'ve reached the maximum of 30 interests.';
            return;
        }

        if (Tag::where('source', 'custom')->where('created_by_user_id', Auth::id())->count() >= 10) {
            $this->onboardingCustomMessage = 'You\'ve reached the limit of 10 personal interests.';
            return;
        }

        $validator = Validator::make(
            ['interest' => $name],
            ['interest' => ['required', 'string', new ValidCustomTag()]],
        );
        if ($validator->fails()) {
            $this->onboardingCustomMessage = $validator->errors()->first('interest');
            return;
        }

        $slug     = Str::slug($name);
        $existing = Tag::where('slug', $slug)->first();

        if ($existing) {
            if (! in_array($existing->id, $this->selectedInterestIds, true)) {
                $this->selectedInterestIds[] = $existing->id;
                $this->onboardingCustomMessage = '"' . $existing->name . '" added!';
            }
            $this->onboardingSearch = '';
            unset($this->onboardingSearchResults);
            return;
        }

        $tag = Tag::create([
            'name'               => $name,
            'slug'               => $slug,
            'type'               => 'interest',
            'source'             => 'custom',
            'category'           => 'User Submitted',
            'created_by_user_id' => Auth::id(),
            'is_curated'         => false,
            'is_approved'        => false,
            'usage_count'        => 0,
        ]);

        $this->selectedInterestIds[] = $tag->id;
        $this->onboardingSearch       = '';
        unset($this->onboardingSearchResults);

        $this->onboardingCustomMessage = '"' . $name . '" added as your own interest.';
    }

    public function toggleExperience(string $tagId): void
    {
        Log::info('Onboarding.action', ['action' => 'toggleExperience', 'step' => $this->step, 'auth' => Auth::id()]);
        if (in_array($tagId, $this->selectedExperienceIds, true)) {
            $this->selectedExperienceIds = array_values(
                array_diff($this->selectedExperienceIds, [$tagId])
            );
        } else {
            $this->selectedExperienceIds[] = $tagId;
        }
    }

    public function toggleComfort(string $key): void
    {
        Log::info('Onboarding.action', ['action' => 'toggleComfort', 'step' => $this->step, 'auth' => Auth::id()]);
        if (in_array($key, $this->selectedComfortOptions, true)) {
            $this->selectedComfortOptions = array_values(
                array_diff($this->selectedComfortOptions, [$key])
            );
        } else {
            $this->selectedComfortOptions[] = $key;
        }
    }

    // ── Completion ────────────────────────────────────────────────────────────

    /**
     * Persist all onboarding selections and mark onboarding complete.
     * Wrapped in a transaction so the flag is only set once all tags are saved.
     */
    public function complete(): void
    {
        Log::info('Onboarding.action', ['action' => 'complete', 'step' => $this->step, 'auth' => Auth::id()]);
        $user = Auth::user();

        $updates = ['onboarding_completed' => true];

        $trimmed = trim($this->displayName);
        if ($trimmed !== '') {
            $updates['display_name'] = $trimmed;
        }

        if (! empty($this->selectedComfortOptions)) {
            $updates['comfort_preferences'] = $this->selectedComfortOptions;
        }

        $allTagIds = array_values(array_unique(array_merge(
            $this->selectedInterestIds,
            $this->selectedExperienceIds,
        )));

        DB::transaction(function () use ($user, $updates, $allTagIds): void {
            $user->forceFill($updates)->save();

            if (! empty($allTagIds)) {
                $tags = Tag::whereIn('id', $allTagIds)->get()->keyBy('id');
                foreach ($allTagIds as $id) {
                    if ($tags->has($id)) {
                        $user->selectTag($tags->get($id));
                    }
                }
            }
        });

        $this->redirect(route('feed'));
    }

    public function render(): View
    {
        // Temporary diagnostic — remove once the production CSRF issue is resolved.
        Log::info('Onboarding render', [
            'session_id_prefix'    => substr(session()->getId(), 0, 10),
            'csrf_token_prefix'    => substr(csrf_token(), 0, 10),
            'auth_id'              => Auth::id(),
        ]);

        return view('livewire.onboarding.onboarding-flow')
            ->layout('layouts.onboarding', ['title' => 'Welcome — CommonGrove']);
    }
}
