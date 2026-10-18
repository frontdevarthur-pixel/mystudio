'use client';

import { motion } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import { useContactModal } from '@/components/ui/ContactModal';
import AnimatedCounter from '@/components/ui/AnimatedCounter';
import type { AboutStat } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

interface StatsSectionProps {
  stats: AboutStat[];
  siteName?: string;
  labels?: { stats_cta_text?: string; stats_cta_btn?: string };
  lang?: Lang;
}

export default function StatsSection({ stats, siteName, labels, lang = 'uk' }: StatsSectionProps) {
  const reducedMotion = useReducedMotion();
  const { open } = useContactModal();

  if (!stats?.length) return null;

  return (
    <section className="py-24 bg-[#0d1117] relative overflow-hidden">
      {/* Акцентная полоса сверху */}
      <div className="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" />
      <div className="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/5 to-transparent" />

      {/* Большой фоновый текст */}
      <div className="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
        <span className="font-display text-[20vw] font-black text-white/[0.015] leading-none tracking-tighter">
          {siteName ?? 'STUDIO'}
        </span>
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-0 divide-x divide-y lg:divide-y-0 divide-white/5">
          {stats.map((stat, i) => (
            <motion.div
              key={i}
              initial={reducedMotion ? false : { opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: i * 0.1 }}
              className="px-8 py-12 lg:py-16 text-center group"
            >
              <div className="font-display text-6xl lg:text-8xl font-black text-white mb-3 leading-none tracking-tight group-hover:text-accent transition-colors duration-300">
                <AnimatedCounter value={stat.value} suffix={stat.suffix} />
              </div>
              <div className="text-slate-500 text-sm uppercase tracking-widest">{stat.label}</div>
            </motion.div>
          ))}
        </div>

        {/* CTA под цифрами */}
        <motion.div
          initial={reducedMotion ? false : { opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.4 }}
          className="text-center mt-16"
        >
          <p className="text-slate-400 mb-6 text-lg">{labels?.stats_cta_text || (lang === 'en' ? 'Ready to be part of this statistics?' : 'Готові стати частиною цієї статистики?')}</p>
          <button
            onClick={open}
            className="btn-accent px-8 py-4 rounded-xl text-base shadow-lg shadow-accent/20"
          >
            {labels?.stats_cta_btn || (lang === 'en' ? 'Discuss project →' : 'Обговорити проект →')}
          </button>
        </motion.div>
      </div>
    </section>
  );
}
