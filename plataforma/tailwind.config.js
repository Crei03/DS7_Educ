/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/js/**/*.{vue,js,ts}',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#0554f2',
                'primary-dk': '#0540f2',
                accent: '#63a1f2',
                'text-main': '#1f2937',
            },
        },
    },
    plugins: [],
}
