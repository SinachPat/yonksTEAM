/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './blocks/**/*.php',
    './templates/**/*.html',
    './parts/**/*.html',
    './patterns/**/*.php',
    './*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1a1a2e',
        secondary: '#c97b3a',
        accent: '#d4946a',
        background: '#faf8f5',
        surface: '#f5f0eb',
        foreground: '#1a1a2e',
        muted: '#6b6b7d',
      },
      fontFamily: {
        heading: ['Chronicle Display', 'Georgia', 'serif'],
        body: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      },
      fontSize: {
        display: ['4.5rem', { lineHeight: '1.1' }],
        hero: ['3.5rem', { lineHeight: '1.15' }],
        h1: ['2.5rem', { lineHeight: '1.2' }],
        h2: ['2rem', { lineHeight: '1.25' }],
        h3: ['1.5rem', { lineHeight: '1.3' }],
        body: ['1rem', { lineHeight: '1.7' }],
        large: ['1.25rem', { lineHeight: '1.6' }],
        small: ['0.875rem', { lineHeight: '1.5' }],
      },
      spacing: {
        section: '6rem',
        'section-sm': '3rem',
        'section-lg': '8rem',
      },
      maxWidth: {
        content: '720px',
        wide: '1140px',
      },
    },
  },
  plugins: [],
};