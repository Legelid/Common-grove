<?php

declare(strict_types=1);

namespace App\Livewire\Support;

use Livewire\Component;

class SupportPage extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.support.support-page')
            ->layout('layouts.app', ['title' => 'Support CommonGrove']);
    }
}
