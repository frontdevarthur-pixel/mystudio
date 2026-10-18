import type { Metadata } from 'next';
import { fetchLandingPageData, fetchSettings } from '@/lib/api/endpoints';
import FallbackPage from '@/components/ui/FallbackPage';
import LandingPage from '@/components/LandingPage';

export const revalidate = 3600;

export async function generateMetadata(): Promise<Metadata> {
  try {
    const settings = await fetchSettings('uk');
    return {
      title: {
        default: settings.seo?.title || settings.site_name || 'WebStudio',
        template: `%s | ${settings.site_name || 'WebStudio'}`,
      },
      description: settings.seo?.description || settings.site_description || '',
      icons: settings.favicon_url ? {
        icon: settings.favicon_url,
        shortcut: settings.favicon_url,
        apple: settings.favicon_url,
      } : undefined,
      openGraph: {
        ...(settings.seo?.og_image_url && { images: [{ url: settings.seo.og_image_url }] }),
        type: 'website',
        locale: 'uk_UA',
        alternateLocale: 'en_US',
      },
      alternates: {
        canonical: '/',
        languages: { 'uk': '/', 'en': '/en' },
      },
      robots: { index: true, follow: true },
    };
  } catch {
    return {
      title: { default: 'WebStudio', template: '%s | WebStudio' },
      robots: { index: true, follow: true },
    };
  }
}

export default async function HomePage() {
  let data;
  try {
    data = await fetchLandingPageData('uk');
  } catch {
    return <FallbackPage />;
  }

  return <LandingPage data={data} lang="uk" />;
}
