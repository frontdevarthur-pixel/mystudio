import { fetchLandingPageData } from '@/lib/api/endpoints';
import FallbackPage from '@/components/ui/FallbackPage';
import LandingPage from '@/components/LandingPage';

export const revalidate = 3600;

export const metadata = {
  alternates: {
    canonical: '/en',
    languages: { 'uk': '/', 'en': '/en' },
  },
};

export default async function HomePageEn() {
  let data;
  try {
    data = await fetchLandingPageData('en');
  } catch {
    return <FallbackPage />;
  }

  return <LandingPage data={data} lang="en" />;
}
