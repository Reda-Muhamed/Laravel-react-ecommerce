import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.tsx',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  daisyui: {
    themes: [
      'light', // default
      'dark', // dark mode
      {
        mytheme: {
          // custom theme example
          primary: '#6936f5',
        },
      },
    ],
  },

  plugins: [require('daisyui')],
};
