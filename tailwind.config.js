/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './*.html',
    './src/**/*.{js,ts,css}'
  ],
  theme: {
    extend: {
      boxShadow: {
        soft: '0 30px 60px rgba(15, 23, 42, 0.45)',
      },
    },
  },
  plugins: [],
}

