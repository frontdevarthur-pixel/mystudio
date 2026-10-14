import { fetchLandingPageData } from '@/lib/api/endpoints';
import FallbackPage from '@/components/ui/FallbackPage';
import LandingPage from '@/components/LandingPage';

export const revalidate = 3600;

export default async function HomePage() {
  let data;
  try {
    data = await fetchLandingPageData('uk');
  } catch {
    return <FallbackPage />;
  }

  return <LandingPage data={data} lang="uk" />;
}
