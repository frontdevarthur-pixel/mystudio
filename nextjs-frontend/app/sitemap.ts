import type { MetadataRoute } from 'next';
import { fetchPortfolio } from '@/lib/api/endpoints';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const base = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://yourdomain.ru';

  let projectUrls: MetadataRoute.Sitemap = [];
  try {
    const portfolio = await fetchPortfolio();
    projectUrls = portfolio.map(p => ({
      url: `${base}/portfolio/${p.slug}`,
      lastModified: new Date(p.published_at),
      changeFrequency: 'monthly' as const,
      priority: 0.7,
    }));
  } catch {
    // Если API недоступен — возвращаем только главную
  }

  return [
    { url: base, lastModified: new Date(), changeFrequency: 'weekly', priority: 1.0 },
    ...projectUrls,
  ];
}
