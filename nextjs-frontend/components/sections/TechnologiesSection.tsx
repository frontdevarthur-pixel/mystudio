'use client';

import { motion } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import TechCard from '@/components/ui/TechCard';
import { filterVisible, sortBySortOrder } from '@/lib/utils/sort-filter';
import type { TechnologiesSection as TechData, SiteSettings } from '@/types/api';

interface TechnologiesSectionProps {
  data: TechData;
  settings: SiteSettings;
}

export default function TechnologiesSection({ data, settings }: TechnologiesSectionProps) {
  const reducedMotion = useReducedMotion();
  const items = sortBySortOrder(filterVisible(data.items));

  const containerVariants = { hidden: {}, visible: { transition: { staggerChildren: 0.04 } } };
  const itemVariants = reducedMotion
    ? {}
    : { hidden: { opacity: 0, scale: 0.8 }, visible: { opacity: 1, scale: 1, transition: { duration: 0.25 } } };

  return (
    <section id="technologies" className="py-28 bg-[#0d1117] relative overflow-hidden">
      <div className="glow-blob w-[500px] h-[500px] bg-accent/4 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Заголовок в одну строку */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-16">
          <motion.div
            initial={reducedMotion ? false : { opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="heading-outline text-5xl sm:text-6xl lg:text-7xl whitespace-nowrap mb-6">
              Технологии
            </h2>
          </motion.div>

          <motion.p
            initial={reducedMotion ? false : { opacity: 0 }}
            whileInView={{ opacity: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.15 }}
            className="text-slate-500 text-lg max-w-xs lg:text-right"
          >
            Инструменты, которыми мы строим продукты
          </motion.p>
        </div>

        {/* Сетка всех технологий */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true }}
          className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3"
        >
          {items.map(tech => (
            <motion.div key={tech.id} variants={itemVariants as Parameters<typeof motion.div>[0]['variants']}>
              <TechCard tech={tech} />
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
}
