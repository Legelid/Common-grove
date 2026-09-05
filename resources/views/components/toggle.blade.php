@props(['checked' => false])

<label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox" @checked($checked) {{ $attributes->merge(['class' => 'sr-only peer']) }}>
    <div class="w-10 h-6 rounded-full bg-surface-raised border border-border transition-colors duration-150
                peer-checked:bg-accent peer-checked:border-accent
                relative after:content-[''] after:absolute after:top-0.5 after:left-0.5
                after:w-4 after:h-4 after:rounded-full after:bg-bg after:transition-transform after:duration-150
                peer-checked:after:translate-x-4"></div>
</label>
