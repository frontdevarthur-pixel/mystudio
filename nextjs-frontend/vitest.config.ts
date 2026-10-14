import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  test: {
    // Использовать jsdom для симуляции DOM-окружения (React-компоненты)
    environment: 'jsdom',

    // Автоматически подключать jest-dom матчеры (toBeInTheDocument, etc.)
    setupFiles: ['./vitest.setup.ts'],

    // Настройки покрытия кода
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/**',
        '.next/**',
        'vitest.config.ts',
        'vitest.setup.ts',
        'next.config.ts',
        'tailwind.config.ts',
        'postcss.config.mjs',
      ],
    },

    // Минимум 100 итераций для property-based тестов (fast-check)
    // Настраивается в каждом тесте через fc.assert(fc.property(...), { numRuns: 100 })

    // Глобальные переменные окружения для тестов
    env: {
      NEXT_PUBLIC_API_URL: 'http://localhost/wp-json/studio/v1',
      NEXT_PUBLIC_SITE_URL: 'http://localhost:3000',
      REVALIDATE_SECRET: 'test-secret',
    },
  },
  resolve: {
    alias: {
      // Алиас @/* → корневая директория проекта (соответствует tsconfig paths)
      '@': path.resolve(__dirname, '.'),
    },
  },
});
