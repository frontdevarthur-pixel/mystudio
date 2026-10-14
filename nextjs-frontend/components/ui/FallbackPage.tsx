export default function FallbackPage() {
  return (
    <main className="min-h-screen bg-[#0f0c29] flex items-center justify-center px-4">
      <div className="text-center max-w-lg">
        <div className="text-6xl mb-6">⚠️</div>
        <h1 className="text-3xl font-bold text-white mb-4">Сайт временно недоступен</h1>
        <p className="text-gray-400 mb-8">
          Мы проводим технические работы. Попробуйте обновить страницу через несколько минут.
        </p>
        <button
          onClick={() => window.location.reload()}
          className="px-8 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-medium transition-colors"
        >
          Обновить страницу
        </button>
      </div>
    </main>
  );
}
