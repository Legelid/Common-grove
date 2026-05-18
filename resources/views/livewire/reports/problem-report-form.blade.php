<div class="px-6 py-10 max-w-2xl mx-auto space-y-8">

    <div>
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Report a problem</h1>
        <p class="mt-2 text-sm leading-relaxed" style="color:#8B949E;">
            If something is broken, confusing, or unsafe, let me know.
            I need to know what's wrong to fix it.
        </p>
    </div>

    @if ($submitted)

        {{-- ── Success state ─────────────────────────────────────────────── --}}
        <div class="rounded-xl border px-8 py-10 text-center space-y-3" style="background:#161B22;border-color:#30363D;">
            <p class="text-lg font-semibold" style="color:#E6EDF3;">Thanks — I'll take a look as soon as I can.</p>
            <p class="text-sm" style="color:#8B949E;">
                Your report has been received.
                @if ($contactEmail)
                    If I need to follow up, I'll reach out to {{ $contactEmail }}.
                @endif
            </p>
            <p class="pt-4">
                <a
                    href="{{ route('feed') }}"
                    class="text-sm underline transition"
                    style="color:#1D9E75;"
                    wire:navigate
                >Back to the feed</a>
            </p>
        </div>

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
                <label for="reportType" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                    What kind of problem is this? <span style="color:#E24B4A;">*</span>
                </label>
                <select
                    id="reportType"
                    wire:model="reportType"
                    @change="type = $event.target.value"
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('reportType') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                    <option value="">Choose a type…</option>
                    @foreach (\App\Models\ProblemReport::TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('reportType')
                    <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                @enderror

                {{-- Type-specific helper text --}}
                <template x-if="type !== ''">
                    <p class="mt-2 text-xs leading-relaxed" style="color:#6B737C;" x-text="helpers[type]"></p>
                </template>
            </div>

            {{-- Subject --}}
            <div>
                <label for="subject" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                    Subject <span style="color:#E24B4A;">*</span>
                </label>
                <input
                    id="subject"
                    type="text"
                    wire:model="subject"
                    maxlength="120"
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('subject') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    :placeholder="type ? subjects[type] : 'Brief summary…'"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                @error('subject')
                    <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                    Description <span style="color:#E24B4A;">*</span>
                </label>
                <textarea
                    id="description"
                    wire:model="description"
                    maxlength="3000"
                    rows="6"
                    placeholder="The more detail the better — what happened, what you expected, and any steps to reproduce."
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none resize-y"
                    style="background:#1C2333;border:1px solid {{ $errors->has('description') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;min-height:120px;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                ></textarea>
                <div class="flex justify-between mt-1">
                    @error('description')
                        <p class="text-sm" style="color:#E24B4A;">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <span class="text-xs" style="color:#3d4451;">{{ strlen($description) }}/3000</span>
                </div>
            </div>

            {{-- Optional fields --}}
            <div class="space-y-5 pt-1">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:#3d4451;">Optional details</p>

                {{-- Page URL --}}
                <div>
                    <label for="pageUrl" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                        Page URL
                    </label>
                    <input
                        id="pageUrl"
                        type="url"
                        wire:model="pageUrl"
                        maxlength="500"
                        placeholder="https://…"
                        class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                        style="background:#1C2333;border:1px solid {{ $errors->has('pageUrl') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                    >
                    @error('pageUrl')
                        <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Screenshot --}}
                <div>
                    <label for="screenshot" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                        Screenshot <span class="font-normal opacity-70">(PNG or JPEG, max 5 MB)</span>
                    </label>
                    <input
                        id="screenshot"
                        type="file"
                        wire:model="screenshot"
                        accept="image/jpeg,image/png"
                        class="w-full text-sm focus:outline-none"
                        style="color:#8B949E;"
                    >
                    <div wire:loading wire:target="screenshot" class="mt-1 text-xs" style="color:#8B949E;">Uploading…</div>
                    @error('screenshot')
                        <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                    @enderror
                    @if ($screenshot && ! $errors->has('screenshot'))
                        <p class="mt-1 text-xs" style="color:#1D9E75;">Screenshot attached.</p>
                    @endif
                </div>

                {{-- Related user --}}
                <div x-show="['safety', 'user-report'].includes(type)" style="display:none;">
                    <label for="relatedUser" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                        Related user <span class="font-normal opacity-70">(gamertag)</span>
                    </label>
                    <input
                        id="relatedUser"
                        type="text"
                        wire:model="relatedUser"
                        maxlength="100"
                        placeholder="Their gamertag…"
                        class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                        style="background:#1C2333;border:1px solid {{ $errors->has('relatedUser') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                    >
                    @error('relatedUser')
                        <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contact email --}}
                <div>
                    <label for="contactEmail" class="block text-sm font-medium mb-1.5" style="color:#8B949E;">
                        Contact email <span class="font-normal opacity-70">(optional — for follow-up only)</span>
                    </label>
                    <input
                        id="contactEmail"
                        type="email"
                        wire:model="contactEmail"
                        maxlength="255"
                        placeholder="your@email.com"
                        class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                        style="background:#1C2333;border:1px solid {{ $errors->has('contactEmail') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                    >
                    <p class="mt-1 text-xs" style="color:#3d4451;">Only used if I need to follow up. Never shared.</p>
                    @error('contactEmail')
                        <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Error --}}
            @if ($submitError)
                <p class="text-sm" style="color:#E24B4A;">{{ $submitError }}</p>
            @endif

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl font-semibold text-sm transition disabled:opacity-50"
                    style="background:#1D9E75;color:#fff;"
                    onmouseover="this.style.background='#1a9068'" onmouseout="this.style.background='#1D9E75'"
                >
                    <span wire:loading.remove>Send report</span>
                    <span wire:loading>Sending…</span>
                </button>
                <p class="text-xs" style="color:#3d4451;">This goes directly to the site owner.</p>
            </div>

        </form>

    @endif

</div>
