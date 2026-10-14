import type { Config } from 'tailwindcss';

const config: Config = {
  content: [
    './pages/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        accent: '#00ff88',
        'accent-dim': '#00cc6e',
        'bg-primary': '#080b11',
        'bg-secondary': '#0d1117',
      },
      fontFamily: {
        display: ['Space Grotesk', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
      },
      typography: {
        invert: {
          css: {
            '--tw-prose-body': '#94a3b8',
            '--tw-prose-headings': '#ffffff',
            '--tw-prose-links': '#00ff88',
            '--tw-prose-bold': '#ffffff',
          },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
};

export default config;
