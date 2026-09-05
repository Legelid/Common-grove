<div class="px-6 py-10 max-w-2xl mx-auto space-y-8">

    <div>
        <h1 class="text-2xl font-bold" style="color:var(--text);">Report a problem</h1>
        <p class="mt-2 text-sm leading-relaxed" style="color:var(--text-muted);">
            If something is broken, confusing, or unsafe, let me know.
            I need to know what's wrong to fix it.
        </p>
    </div>

    @if ($submitted)

        {{-- ── Success state ─────────────────────────────────────────────── --}}
        <x-card padding="px-8 py-10" class="text-center space-y-3">
            <p class="text-lg font-semibold" style="color:var(--text);">Thanks — I'll take a look as soon as I can.</p>
            <p class="text-sm" style="color:var(--text-muted);">
                Your report has been received.
                @if ($contactEmail)
                    If I need to follow up, I'll reach out to {{ $contactEmail }}.
                @endif
            </p>
            <p class="pt-4">
                <a
                    href="{{ route('feed') }}"
                    class="text-sm underline transition"
                    style="color:var(--accent);"
                    wire:navigate
                >Back to the feed</a>
            </p>
        </x-card>

    @else

        {{-- ── Form ──────────────────────────────────────────────────────── --}}
        <form
            wire:submit="submit"
            class="space-y-6"
            x-data="{
                type: '',
                helpers: {
                    bug:          'What happened, and what did you expect to happen? Include the page URL if you can.',
                    safety:       'Describe the safety issue. Your report goes directly to the site owner — not to other users.',
                    'user-report':'Who are you reporting and why? Be as specific as you can.',
                    account:      'Describe the problem with your account — login issues, wrong email, or anything else.',
                    feedback:     'Anything you\'d like to see changed, added, or improved. All feedback is read.',
                    other:        'Tell me what\'s on your mind.',
                },
                subjects: {
                    bug:          'e.g. The send button doesn\'t work on mobile',
                    safety:       'e.g. Receiving unwanted messages',
                    'user-report':'e.g. User harassing me in a room',
                    account:      'e.g. Can\'t log in to my account',
                    feedback:     'e.g. Would love a dark/light mode toggle',
                    other:        'Brief summary…',
                }
            }"
        >
            {{-- Honeypot — hidden from real users, filled by bots --}}
            <div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">
                <input type="text" wire:model="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            {{-- Report type --}}
            <div>
                <label for="reportType" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                    What kind of problem is this? <span style="color:var(--danger);">*</span>
                </label>
                <select
                    id="reportType"
                    wire:model="reportType"
                    @change="type = $event.target.value"
                    class="w-full rounded-btn px-4 py-2.5 text-sm bg-surface text-text border transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('reportType') ? 'border-danger' : 'border-border focus:border-accent' }}"
                >
                    <option value="">Choose a type…</option>
                    @foreach (\App\Models\ProblemReport::TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('reportType')
                    <p class="mt-1 text-sm" style="color:var(--danger);">{{ $message }}</p>
                @enderror

                {{-- Type-specific helper text --}}
                <template x-if="type !== ''">
                    <p class="mt-2 text-xs leading-relaxed" style="color:var(--text-faint);" x-text="helpers[type]"></p>
                </template>
            </div>

            {{-- Subject --}}
            <div>
                <label for="subject" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                    Subject <span style="color:var(--danger);">*</span>
                </label>
                <x-input
                    id="subject"
                    type="text"
                    wire:model="subject"
                    maxlength="120"
                    x-bind:placeholder="type ? subjects[type] : 'Brief summary…'"
                    :error="$errors->first('subject')"
                />
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                    Description <span style="color:var(--danger);">*</span>
                </label>
                <x-textarea
                    id="description"
                    wire:model="description"
                    maxlength="3000"
                    rows="6"
                    placeholder="The more detail the better — what happened, what you expected, and any steps to reproduce."
                    class="!resize-y"
                    style="min-height:120px;"
                    :error="$errors->first('description')"
                />
                <div class="flex justify-end mt-1">
                    <span class="text-xs" style="color:var(--text-faint);">{{ strlen($description) }}/3000</span>
                </div>
            </div>

            {{-- Optional fields --}}
            <div class="space-y-5 pt-1">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-faint);">Optional details</p>

                {{-- Page URL --}}
                <div>
                    <label for="pageUrl" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                        Page URL
                    </label>
                    <x-input
                        id="pageUrl"
                        type="url"
                        wire:model="pageUrl"
                        maxlength="500"
                        placeholder="https://…"
                        :error="$errors->first('pageUrl')"
                    />
                </div>

                {{-- Screenshot --}}
                <div>
                    <label for="screenshot" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                        Screenshot <span class="font-normal opacity-70">(PNG or JPEG, max 5 MB)</span>
                    </label>
                    <input
                        id="screenshot"
                        type="file"
                        wire:model="screenshot"
                        accept="image/jpeg,image/png"
                        class="w-full text-sm"
                        style="color:var(--text-muted);outline:none;"
                        onfocus="this.style.outline='2px solid var(--accent)';this.style.outlineOffset='2px'"
                        onblur="this.style.outline='none'"
                    >
                    <div wire:loading wire:target="screenshot" class="mt-1 text-xs" style="color:var(--text-muted);">Uploading…</div>
                    @error('screenshot')
                        <p class="mt-1 text-sm" style="color:var(--danger);">{{ $message }}</p>
                    @enderror
                    @if ($screenshot && ! $errors->has('screenshot'))
                        <p class="mt-1 text-xs" style="color:var(--accent);">Screenshot attached.</p>
                    @endif
                </div>

                {{-- Related user --}}
                <div x-show="['safety', 'user-report'].includes(type)" style="display:none;">
                    <label for="relatedUser" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                        Related user <span class="font-normal opacity-70">(gamertag)</span>
                    </label>
                    <x-input
                        id="relatedUser"
                        type="text"
                        wire:model="relatedUser"
                        maxlength="100"
                        placeholder="Their gamertag…"
                        :error="$errors->first('relatedUser')"
                    />
                </div>

                {{-- Contact email --}}
                <div>
                    <label for="contactEmail" class="block text-sm font-medium mb-1.5" style="color:var(--text-muted);">
                        Contact email <span class="font-normal opacity-70">(optional — for follow-up only)</span>
                    </label>
                    <x-input
                        id="contactEmail"
                        type="email"
                        wire:model="contactEmail"
                        maxlength="255"
                        placeholder="your@email.com"
                        :error="$errors->first('contactEmail')"
                    />
                    <p class="mt-1 text-xs" style="color:var(--text-faint);">Only used if I need to follow up. Never shared.</p>
                </div>
            </div>

            {{-- Error --}}
            @if ($submitError)
                <p class="text-sm" style="color:var(--danger);">{{ $submitError }}</p>
            @endif

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-2">
                <x-button type="submit" wire:loading.attr="disabled" variant="primary" class="!px-6">
                    <span wire:loading.remove>Send report</span>
                    <span wire:loading>Sending…</span>
                </x-button>
                <p class="text-xs" style="color:var(--text-faint);">This goes directly to the site owner.</p>
            </div>

        </form>

    @endif

</div>
