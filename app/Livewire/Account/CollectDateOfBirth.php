<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class CollectDateOfBirth extends Component
{
    public string $birthMonth = '';
    public string $birthDay   = '';
    public string $birthYear  = '';

    public function mount(): void
    {
        // Already completed — skip to feed.
        if (Auth::user()->date_of_birth !== null) {
            $this->redirect(route('feed'));
        }
    }

    public function save(): void
    {
        $this->validate([
            'birthMonth' => ['required', 'integer', 'min:1', 'max:12'],
            'birthDay'   => ['required', 'integer', 'min:1', 'max:31'],
            'birthYear'  => [
                'required',
                'integer',
                'min:' . (now()->year - 120),
                'max:' . now()->year,
                function (string $attr, mixed $value, \Closure $fail): void {
                    $month = (int) $this->birthMonth;
                    $day   = (int) $this->birthDay;
                    $year  = (int) $value;

                    if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
                        return;
                    }

                    $dob = Carbon::createSafe($year, $month, $day);

                    if ($dob === false) {
                        $fail('Please enter a valid date of birth.');
                        return;
                    }

                    if ($dob->diffInYears(now()) < 18) {
                        $fail('CommonGrove is currently only available to adults.');
                    }
                },
            ],
        ]);

        $dob = Carbon::createSafe(
            (int) $this->birthYear,
            (int) $this->birthMonth,
            (int) $this->birthDay,
        );

        Auth::user()->update(['date_of_birth' => $dob->toDateString()]);

        $this->redirect(route('feed'));
    }

    public function render(): View
    {
        return view('livewire.account.collect-date-of-birth')
            ->layout('layouts.onboarding', ['title' => 'Date of birth — CommonGrove']);
    }
}
