<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OnboardingFlow extends Component
{
    public int $step = 1;

    public string $displayName = '';

    public ?int $onboardingCategoryId = null;

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
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function experienceTags(): Collection
    {
        return Tag::approved()->ofType('shared_experience')->orderBy('name')->get();
    }

    public function setOnboardingCategory(?int $categoryId): void
    {
        $this->onboardingCategoryId = $this->onboardingCategoryId === $categoryId ? null : $categoryId;
        unset($this->onboardingSubcats);
    }

    // ── Step navigation ───────────────────────────────────────────────────────

    public function next(): void
    {
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
        $this->step = max($this->step - 1, 1);
    }

    public function skipToStep(int $target): void
    {
        $this->step = $target;
    }

    // ── Toggle helpers ────────────────────────────────────────────────────────

    public function toggleInterest(string $tagId): void
    {
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

    public function toggleExperience(string $tagId): void
    {
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

        $this->redirect(route('feed'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.onboarding.onboarding-flow')
            ->layout('layouts.onboarding', ['title' => 'Welcome — CommonGround']);
    }
}
