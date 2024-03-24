const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: ['./resources/views/app/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue'],
  theme: {
    container: {
      center: true,
      padding: {
        DEFAULT: '2rem',
        sm: '1.5rem'
      }
    },
    extend: {
      colors: {
        primary: {
          50: '#fdfcfa',
          100: '#fcf9f5',
          200: '#f8f3eb',
          300: '#f5ece1',
          400: '#f1e6d7',
          500: '#eee0cd',
          600: '#beb3a4',
          700: '#8f867b',
          800: '#5f5a52',
          900: '#302d29'
        },
        accent: colors.emerald
      },
      screens: {
        xs: '370px',
        mobile: defaultTheme.screens.md
      },
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans]
      },
      animation: {
        'bounce-x': 'bounce-x 1s infinite'
      },
      keyframes: {
        'bounce-x': {
          '0%, 100%': {
            transform: 'translateX(-25%)',
            animationTimingFunction: 'cubic-bezier(0.8,0,1,1)'
          },
          '50%': {
            transform: 'none',
            animationTimingFunction: 'cubic-bezier(0,0,0.2,1)'
          }
        }
      },
      zIndex: {
        60: '60'
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms')({
      strategy: 'base'
    }),
    require('tailwind-scrollbar')({ nocompatible: true })
  ]
}
