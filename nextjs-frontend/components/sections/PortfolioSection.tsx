'use client';

import { motion } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import Carousel from '@/components/ui/Carousel';
import { filterVisible, sortBySortOrder } from '@/lib/utils/sort-filter';
import type { PortfolioProject, SiteSettings } from '@/types/api';

interface PortfolioSectionProps {
  projects: PortfolioProject[];
  settings: SiteSettings;
}

export default function PortfolioSection({ projects, settings }: PortfolioSectionProps) {
  const reducedMotion = useReducedMotion();
  const items = sortBySortOrder(filterVisible(projects));

  return (
    <section id="portfolio" className="py-28 bg-[#080b11] relative overflow-hidden">
      <div className="glow-blob w-[600px] h-[400px] bg-accent/5 top-0 right-[-200px]" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Заголовок — текст по левому краю, число справа */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-16">
          <motion.div
            initial={reducedMotion ? false : { opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="heading-outline text-5xl sm:text-6xl lg:text-7xl">
              Избранные проекты
            </h2>
          </motion.div>
        </div>

        <motion.div
          initial={reducedMotion ? false : { opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.1 }}
        >
          <Carousel projects={items} />
        </motion.div>
      </div>
    </section>
  );
}
