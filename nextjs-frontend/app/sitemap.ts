import type { MetadataRoute } from 'next';
import { fetchPortfolio } from '@/lib/api/endpoints';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const base = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://yourdomain.ru';

  let projectUrls: MetadataRoute.Sitemap = [];
  try {
    const portfolio = await fetchPortfolio('uk');
    projectUrls = portfolio.flatMap(p => [
      {
        url: `${base}/portfolio/${p.slug}`,
        lastModified: new Date(p.published_at),
        changeFrequency: 'monthly' as const,
        priority: 0.7,
      },
      {
        url: `${base}/en/portfolio/${p.slug}`,
        lastModified: new Date(p.published_at),
        changeFrequency: 'monthly' as const,
        priority: 0.6,
      },
    ]);
  } catch {
    // Если API недоступен — возвращаем только статические страницы
  }

  return [
    { url: base, lastModified: new Date(), changeFrequency: 'weekly', priority: 1.0 },
    { url: `${base}/en`, lastModified: new Date(), changeFrequency: 'weekly', priority: 0.9 },
    ...projectUrls,
  ];
}
