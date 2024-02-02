// const defaultTheme = require('tailwindcss/defaultTheme.js')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./resources/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue'],
  theme: {
    extend: {
      fontFamily: {
        // sans: ['Inter var', ...defaultTheme.fontFamily.sans],
        // mono: ['Fira Code VF', ...defaultTheme.fontFamily.mono]
      }
    }
  },
  plugins: [require('@tailwindcss/forms'), require('tailwind-scrollbar')({ nocompatible: true })]
}
