const defaultTheme = require('tailwindcss/defaultTheme.js')
// const colors = require('tailwindcss/colors.js')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./resources/views/app/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans]
      }
    }
  },
  plugins: [require('@tailwindcss/forms'), require('tailwind-scrollbar')({ nocompatible: true })]
}
