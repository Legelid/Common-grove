<x-card padding="p-8" class="space-y-6">

    <div class="space-y-2">
        <h1 class="text-xl font-bold" style="color:var(--text);">Choose your gamertag</h1>
        <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
            This is how you'll be known on CommonGrove — pick something that feels like you.
        </p>
    </div>

    <div>
        <label for="gamertag" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Gamertag</label>
        <div class="relative">
            <x-input
                id="gamertag"
                type="text"
                wire:model.live.debounce.400ms="gamertag"
                autocomplete="username"
                maxlength="20"
                placeholder="YourGamertag"
                class="pr-10"
                :error="$errors->first('gamertag')"
            />
            @if ($gamertagStatus === 'available')
                <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold text-accent">✓</span>
            @elseif ($gamertagStatus === 'taken')
                <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:var(--danger);">✗</span>
            @endif
        </div>
        <p class="mt-1 text-xs" style="color:var(--text-muted);">3–20 characters · starts with a letter · letters, numbers, _ and - only</p>

        @if ($gamertagStatus === 'taken')
            <div class="mt-2">
                <p class="text-sm mb-1" style="color:var(--danger);">That gamertag is taken. Try one of these:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($suggestions as $suggestion)
                        <button
                            type="button"
                            wire:click="useSuggestion('{{ $suggestion }}')"
                            class="px-3 py-1 text-sm rounded-full transition bg-surface-raised text-accent hover:bg-accent hover:text-on-accent"
                        >{{ $suggestion }}</button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <x-button type="button" wire:click="save" wire:loading.attr="disabled" variant="primary" class="w-full">
        <span wire:loading.remove>Continue</span>
        <span wire:loading>Saving…</span>
    </x-button>

    <form method="POST" action="/logout" class="text-center">
        @csrf
        <button type="submit" class="text-xs transition underline" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">
            Sign out
        </button>
    </form>

</x-card>
