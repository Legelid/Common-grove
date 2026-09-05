<?php

declare(strict_types=1);

namespace App\Livewire\Support;

use Livewire\Component;

class ComparePage extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.support.compare-page')
            ->layout('layouts.app', ['title' => 'Free vs Supporter | CommonGrove']);
    }
}
