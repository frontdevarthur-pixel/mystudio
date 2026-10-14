import type { MetadataRoute } from 'next';

export default function robots(): MetadataRoute.Robots {
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://yourdomain.ru';
  return {
    rules: { userAgent: '*', allow: '/' },
    sitemap: `${siteUrl}/sitemap.xml`,
  };
}
