/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/assets/css/**/*.css",
    "./public/assets/src/**/*.ts",
    "./public/assets/js/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('tailwindcss'),
    require('autoprefixer'),
  ],
}
