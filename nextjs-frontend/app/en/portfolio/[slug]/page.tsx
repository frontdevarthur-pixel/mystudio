import type { Metadata } from 'next';
import { fetchPortfolio } from '@/lib/api/endpoints';
import { buildPageMetadata } from '@/lib/utils/seo';
import PortfolioProjectDetail from '@/components/PortfolioProjectDetail';

export const revalidate = 1800;

interface Props {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  try {
    const projects = await fetchPortfolio('en');
    return projects.map(p => ({ slug: p.slug }));
  } catch {
    return [];
  }
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  try {
    const projects = await fetchPortfolio('en');
    const project = projects.find(p => p.slug === slug);
    if (!project) return {};
    return {
      ...buildPageMetadata(project.title, project.short_description, project.screenshot_url),
      alternates: {
        canonical: `/en/portfolio/${slug}`,
        languages: { uk: `/portfolio/${slug}`, en: `/en/portfolio/${slug}` },
      },
    };
  } catch {
    return {};
  }
}

export default async function PortfolioProjectPageEn({ params }: Props) {
  const { slug } = await params;
  const projects = await fetchPortfolio('en');
  const project = projects.find(p => p.slug === slug);

  return <PortfolioProjectDetail project={project} lang="en" />;
}
