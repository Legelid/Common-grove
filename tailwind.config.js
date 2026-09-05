import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
                display: ['"Playfair Display"', 'Georgia', 'serif'],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                bg: 'var(--bg)',
                surface: 'var(--surface)',
                'surface-raised': 'var(--surface-raised)',
                border: 'var(--border)',
                accent: 'rgb(var(--accent-rgb) / <alpha-value>)',
                'accent-hover': 'var(--accent-hover)',
                text: 'var(--text)',
                'text-muted': 'var(--text-muted)',
                'text-faint': 'var(--text-faint)',
                danger: 'rgb(var(--danger-rgb) / <alpha-value>)',
                'on-accent': 'var(--on-accent)',
                'on-danger': 'var(--on-danger)',
            },
            borderRadius: {
                card: '14px',
                btn: '10px',
            },
            boxShadow: {
                card: 'var(--card-shadow)',
            },
        },
    },
    plugins: [],
};
