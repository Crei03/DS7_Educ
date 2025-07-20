module.exports = {
    content: [
        './resources/js/**/*.{vue,js}',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#0554f2',
                'primary-dk': '#0540f2',
                accent: '#63a1f2',
                bg: '#f2f2f2',
                'text-main': '#1f2937',
            },
            spacing: {
                'container': '1.25rem',
            },
        },
    },
    plugins: [],
};
