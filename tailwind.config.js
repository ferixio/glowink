/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./app/Livewire/**/*.php",
        "./resources/views/livewire/**/*.blade.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
