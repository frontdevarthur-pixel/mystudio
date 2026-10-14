// Утилиты для формирования метаданных страниц Next.js
import type { Metadata } from 'next';

/**
 * Формирует объект Metadata для конкретной страницы
 */
export function buildPageMetadata(
  title: string,
  description: string,
  ogImage?: string
): Metadata {
  return {
    title,
    description,
    openGraph: {
      title,
      description,
      ...(ogImage && { images: [{ url: ogImage }] }),
    },
    twitter: {
      card: 'summary_large_image',
      title,
      description,
      ...(ogImage && { images: [ogImage] }),
    },
  };
}
