'use client';

import { useState, useEffect, useCallback } from 'react';
import { motion, AnimatePresence, type Variants } from 'framer-motion';
import Image from 'next/image';
import Modal from '@/components/ui/Modal';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import type { PortfolioProject } from '@/types/api';

interface CarouselProps {
  projects: PortfolioProject[];
}

export default function Carousel({ projects }: CarouselProps) {
  const reducedMotion = useReducedMotion();
  const [current, setCurrent] = useState(0);
  const [direction, setDirection] = useState(1);
  const [isHovered, setIsHovered] = useState(false);
  const [modalProject, setModalProject] = useState<PortfolioProject | null>(null);

  const go = useCallback((index: number, dir: number) => {
    setDirection(dir);
    setCurrent((index + projects.length) % projects.length);
  }, [projects.length]);

  const prev = () => go(current - 1, -1);
  const next = () => go(current + 1, 1);

  // Автопрокрутка каждые 5 секунд
  useEffect(() => {
    if (isHovered || projects.length < 2) return;
    const timer = setInterval(() => go(current + 1, 1), 5000);
    return () => clearInterval(timer);
  }, [current, isHovered, projects.length, go]);

  const slideVariants: Variants = reducedMotion
    ? {}
    : {
        enter: (d: number) => ({ x: d > 0 ? '100%' : '-100%', opacity: 0 }),
        center: { x: 0, opacity: 1, transition: { duration: 0.4, ease: 'easeInOut' } },
        exit: (d: number) => ({ x: d > 0 ? '-100%' : '100%', opacity: 0, transition: { duration: 0.3 } }),
      };

  const project = projects[current];
  if (!project) return null;

  return (
    <>
      <div
        className="relative overflow-hidden rounded-2xl glass-card"
        onMouseEnter={() => setIsHovered(true)}
        onMouseLeave={() => setIsHovered(false)}
      >
        <AnimatePresence custom={direction} mode="popLayout">
          <motion.div
            key={project.id}
            custom={direction}
            variants={slideVariants}
            initial="enter"
            animate="center"
            exit="exit"
            className="grid md:grid-cols-2 gap-0 glass-card"
          >
            {/* Изображение */}
            <div
              className="relative aspect-video md:aspect-auto md:min-h-[360px] cursor-pointer group"
              onClick={() => setModalProject(project)}
            >
              {project.screenshot_url ? (
                <Image
                  src={project.screenshot_url}
                  alt={project.screenshot_alt || project.title}
                  fill
                  className="object-cover group-hover:scale-105 transition-transform duration-500"
                />
              ) : (
                <div className="absolute inset-0 bg-gradient-to-br from-accent/10 to-blue-500/10 flex items-center justify-center">
                  <span className="font-display text-6xl font-black text-white/10">
                    {project.title.charAt(0)}
                  </span>
                </div>
              )}
              <div className="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors" />
            </div>

            {/* Контент */}
            <div className="p-8 flex flex-col justify-center">
              <h3 className="font-display text-2xl font-bold text-white mb-3">{project.title}</h3>
              <p className="text-slate-400 text-sm leading-relaxed mb-6">{project.short_description}</p>

              {project.technologies?.length > 0 && (
                <div className="flex flex-wrap gap-2 mb-6">
                  {project.technologies.map((tech, i) => (
                    <span key={i} className="px-3 py-1 rounded-full glass-card border-accent/20 text-accent/80 text-xs font-display">
                      {tech}
                    </span>
                  ))}
                </div>
              )}

              <div className="flex gap-3">
                <button
                  onClick={() => setModalProject(project)}
                  className="btn-accent px-5 py-2.5 rounded-xl text-sm"
                >
                  Подробнее
                </button>
                {project.project_url && (
                  <a
                    href={project.project_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="btn-outline px-5 py-2.5 rounded-xl text-sm"
                  >
                    Открыть →
                  </a>
                )}
              </div>
            </div>
          </motion.div>
        </AnimatePresence>

        {/* Навигация */}
        {projects.length > 1 && (
          <>
            <button
              onClick={prev}
              className="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors z-10"
              aria-label="Предыдущий"
            >
              ‹
            </button>
            <button
              onClick={next}
              className="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors z-10"
              aria-label="Следующий"
            >
              ›
            </button>

            {/* Точки пагинации */}
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
              {projects.map((_, i) => (
                <button
                  key={i}
                  onClick={() => go(i, i > current ? 1 : -1)}
                  className={`w-2 h-2 rounded-full transition-all ${i === current ? 'bg-violet-400 w-6' : 'bg-white/30'}`}
                  aria-label={`Слайд ${i + 1}`}
                />
              ))}
            </div>
          </>
        )}
      </div>

      {/* Модальное окно с деталями */}
      <Modal isOpen={!!modalProject} onClose={() => setModalProject(null)}>
        {modalProject && (
          <div className="p-6">
            {modalProject.screenshot_url && (
              <div className="relative aspect-video rounded-xl overflow-hidden mb-6">
                <Image src={modalProject.screenshot_url} alt={modalProject.screenshot_alt || modalProject.title} fill className="object-cover" />
              </div>
            )}
            <h3 className="text-2xl font-bold text-white mb-3">{modalProject.title}</h3>
            {modalProject.full_description ? (
              <div
                className="text-gray-300 text-sm leading-relaxed mb-4 prose prose-invert prose-sm max-w-none"
                dangerouslySetInnerHTML={{ __html: modalProject.full_description }}
              />
            ) : (
              <p className="text-gray-300 text-sm mb-4">{modalProject.short_description}</p>
            )}
            {modalProject.technologies?.length > 0 && (
              <div className="flex flex-wrap gap-2 mb-4">
                  {project.technologies.map((t, i) => (
                    <span key={i} className="px-3 py-1 rounded-full glass-card border-accent/20 text-accent/80 text-xs font-display">{t}</span>
                  ))}
              </div>
            )}
            {modalProject.project_url && (
              <a
                href={modalProject.project_url}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-block btn-accent px-5 py-2.5 rounded-xl text-sm"
              >
                Открыть проект →
              </a>
            )}
          </div>
        )}
      </Modal>
    </>
  );
}
