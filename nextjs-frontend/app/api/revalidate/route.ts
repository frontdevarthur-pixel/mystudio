// ISR webhook-обработчик для принудительной регенерации страниц Next.js
// Вызывается WordPress при сохранении контента в Admin

import { revalidatePath } from 'next/cache';
import { NextRequest, NextResponse } from 'next/server';

export async function POST(request: NextRequest) {
  const secret = request.headers.get('x-revalidate-secret');

  // Проверяем секрет для защиты от несанкционированных вызовов
  if (secret !== process.env.REVALIDATE_SECRET) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  let body: { paths?: string[] } = {};
  try {
    body = await request.json();
  } catch {
    // Если тело пустое — используем дефолт
  }

  const paths = body.paths ?? ['/'];

  // Регенерируем все указанные пути
  paths.forEach(path => revalidatePath(path));

  return NextResponse.json({ revalidated: true, paths });
}
