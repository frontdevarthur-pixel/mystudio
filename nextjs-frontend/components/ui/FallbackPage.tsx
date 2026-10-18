'use client';

import type { Lang } from '@/lib/api/endpoints';

const fallbackI18n = {
  uk: {
    title: 'Сайт тимчасово недоступний',
    text: 'Ми проводимо технічні роботи. Спробуйте оновити сторінку за кілька хвилин.',
    reload: 'Оновити сторінку',
  },
  en: {
    title: 'Site temporarily unavailable',
    text: 'We are performing scheduled maintenance. Please try refreshing the page in a few minutes.',
    reload: 'Refresh page',
  },
};

interface FallbackPageProps {
  lang?: Lang;
}

export default function FallbackPage({ lang = 'uk' }: FallbackPageProps) {
  const t = fallbackI18n[lang] ?? fallbackI18n.uk;

  return (
    <main className="min-h-screen bg-[#0f0c29] flex items-center justify-center px-4">
      <div className="text-center max-w-lg">
        <div className="text-6xl mb-6">⚠️</div>
        <h1 className="text-3xl font-bold text-white mb-4">{t.title}</h1>
        <p className="text-gray-400 mb-8">{t.text}</p>
        <button
          onClick={() => window.location.reload()}
          className="px-8 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-medium transition-colors"
        >
          {t.reload}
        </button>
      </div>
    </main>
  );
}
