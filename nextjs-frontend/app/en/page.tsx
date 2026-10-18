import type { Metadata } from 'next';
import { fetchLandingPageData, fetchSettings } from '@/lib/api/endpoints';
import FallbackPage from '@/components/ui/FallbackPage';
import LandingPage from '@/components/LandingPage';

export const revalidate = 3600;

export async function generateMetadata(): Promise<Metadata> {
  try {
    const settings = await fetchSettings('en');
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
        locale: 'en_US',
        alternateLocale: 'uk_UA',
      },
      alternates: {
        canonical: '/en',
        languages: { 'uk': '/', 'en': '/en' },
      },
      robots: { index: true, follow: true },
    };
  } catch {
    return {
      title: { default: 'WebStudio', template: '%s | WebStudio' },
      alternates: { canonical: '/en', languages: { 'uk': '/', 'en': '/en' } },
    };
  }
}

export default async function HomePageEn() {
  let data;
  try {
    data = await fetchLandingPageData('en');
  } catch {
    return <FallbackPage lang="en" />;
  }

  return <LandingPage data={data} lang="en" />;
}
