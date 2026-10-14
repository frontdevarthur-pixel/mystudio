// Генераторы JSON-LD схем для структурированных данных (SEO)
// Используются на главной странице для индексации поисковыми системами

import type { SiteSettings } from '@/types/api';

// JSON-LD Schema.org Organization — для главной страницы студии
export function buildOrganizationJsonLd(settings: SiteSettings): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: settings.site_name,
    description: settings.seo.description,
    url: process.env.NEXT_PUBLIC_SITE_URL ?? '',
    logo: settings.logo_url,
    contactPoint: {
      '@type': 'ContactPoint',
      telephone: settings.phone,
      email: settings.email,
      contactType: 'customer support',
    },
    sameAs: settings.social_links.map(link => link.url),
  };
}

// JSON-LD Schema.org WebSite — для поисковой строки и поиска по сайту
export function buildWebSiteJsonLd(settings: SiteSettings): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: settings.site_name,
    description: settings.seo.description,
    url: process.env.NEXT_PUBLIC_SITE_URL ?? '',
  };
}
