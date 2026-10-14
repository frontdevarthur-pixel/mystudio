import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { fetchPortfolio } from '@/lib/api/endpoints';
import { buildPageMetadata } from '@/lib/utils/seo';

export const revalidate = 1800;

interface Props {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  try {
    const projects = await fetchPortfolio();
    return projects.map(p => ({ slug: p.slug }));
  } catch {
    return [];
  }
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  try {
    const projects = await fetchPortfolio();
    const project = projects.find(p => p.slug === slug);
    if (!project) return {};
    return buildPageMetadata(project.title, project.short_description, project.screenshot_url);
  } catch {
    return {};
  }
}

export default async function PortfolioProjectPage({ params }: Props) {
  const { slug } = await params;
  const projects = await fetchPortfolio();
  const project = projects.find(p => p.slug === slug);

  if (!project) {
    return (
      <div className="min-h-screen bg-[#0f0c29] flex items-center justify-center text-white">
        <div className="text-center">
          <h1 className="text-2xl font-bold mb-4">Проект не найден</h1>
          <Link href="/#portfolio" className="text-violet-400 hover:text-violet-300">← Назад к портфолио</Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#0f0c29] text-white">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <Link href="/#portfolio" className="text-violet-400 hover:text-violet-300 text-sm mb-8 inline-block">
          ← Назад к портфолио
        </Link>

        {/* Главное изображение */}
        {project.screenshot_url && (
          <div className="relative aspect-video rounded-2xl overflow-hidden mb-8">
            <Image
              src={project.screenshot_url}
              alt={project.screenshot_alt || project.title}
              fill
              priority
              className="object-cover"
            />
          </div>
        )}

        <h1 className="text-3xl sm:text-4xl font-bold text-white mb-4">{project.title}</h1>
        <p className="text-gray-300 text-lg mb-6">{project.short_description}</p>

        {/* Технологии */}
        {project.technologies?.length > 0 && (
          <div className="flex flex-wrap gap-2 mb-8">
            {project.technologies.map((tech, i) => (
              <span key={i} className="px-3 py-1.5 rounded-full bg-violet-600/20 border border-violet-500/30 text-violet-300 text-sm">
                {tech}
              </span>
            ))}
          </div>
        )}

        {/* Полное описание */}
        {project.full_description && (
          <div
            className="prose prose-invert max-w-none text-gray-300 mb-8"
            dangerouslySetInnerHTML={{ __html: project.full_description }}
          />
        )}

        {/* Галерея */}
        {project.gallery?.length > 0 && (
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            {project.gallery.map((img, i) => (
              <div key={i} className="relative aspect-video rounded-xl overflow-hidden">
                <Image src={img.url} alt={img.alt || `${project.title} ${i + 1}`} fill className="object-cover" />
              </div>
            ))}
          </div>
        )}

        {/* Ссылка на проект */}
        {project.project_url && (
          <a
            href={project.project_url}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-block px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-medium transition-colors"
          >
            Открыть проект →
          </a>
        )}
      </div>
    </div>
  );
}
