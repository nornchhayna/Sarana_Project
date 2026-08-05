export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        // define your palette so classes stay consistent
        brand: {
          blue: '#2563eb',      // your main blue
          'blue-dark': '#1e40af',
          black: '#111111',
        }
      }
    },
  },
  plugins: [],
}