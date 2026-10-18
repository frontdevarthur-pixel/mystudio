'use client';

import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import type { GeographySection as GeoData, CountryData } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

interface GeographySectionProps {
  data: GeoData;
  lang?: Lang;
}

function CountryCard({ country, index, lang = 'uk' }: { country: CountryData; index: number; lang?: Lang }) {
  const [hovered, setHovered] = useState(false);
  const flag = country.country_code.toUpperCase().split('').map(c => String.fromCodePoint(c.charCodeAt(0) + 127397)).join('');

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.4, delay: index * 0.07 }}
      className="relative flex items-center gap-5 p-5 glass-card rounded-2xl hover:border-accent/30 transition-all duration-200 cursor-default group"
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      <span className="text-4xl flex-shrink-0" role="img" aria-label={country.country_name}>{flag}</span>
      <div className="flex-1 min-w-0">
        <p className="font-display font-bold text-white text-base mb-1">{country.country_name}</p>
        <div className="flex gap-4 text-xs text-slate-500">
          <span><span className="text-accent font-bold text-sm">{country.projects_count}</span> {lang === 'en' ? 'projects' : 'проектів'}</span>
          <span><span className="text-accent font-bold text-sm">{country.clients_count}</span> {lang === 'en' ? 'clients' : 'клієнтів'}</span>
        </div>
      </div>
      <div className="w-1.5 h-8 rounded-full bg-white/5 group-hover:bg-accent/50 transition-colors" />

      <AnimatePresence>
        {hovered && country.tooltip_text && (
          <motion.div
            className="absolute bottom-full left-4 mb-2 px-3 py-2 bg-[#0d1117] border border-accent/20 rounded-lg text-xs text-slate-200 shadow-xl z-10 max-w-[220px]"
            initial={{ opacity: 0, y: 6 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 6 }}
            transition={{ duration: 0.15 }}
          >
            {country.tooltip_text}
          </motion.div>
        )}
      </AnimatePresence>
    </motion.div>
  );
}

export default function GeographySection({ data, lang = 'uk' }: GeographySectionProps) {
  const reducedMotion = useReducedMotion();

  return (
    <section id="geography" className="py-28 bg-[#080b11] relative overflow-hidden">
      <div className="glow-blob w-[400px] h-[400px] bg-blue-500/5 top-1/2 right-[-100px] -translate-y-1/2" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-16 items-start">
          {/* Левая — заголовок */}
          <motion.div
            initial={reducedMotion ? false : { opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="lg:sticky lg:top-28"
          >
            <h2 className="heading-outline text-5xl sm:text-6xl mb-6 whitespace-nowrap">
              {data.title ?? (lang === 'en' ? 'Client Geography' : 'Географія клієнтів')}
            </h2>
            <p className="text-slate-500 text-base leading-relaxed">
              {data.description ?? (lang === 'en' ? 'We work with clients worldwide' : 'Ми працюємо з клієнтами по всьому світу')}
            </p>

            {/* Большая цифра */}
            <div className="mt-10 pt-8 border-t border-white/5">
              <div className="font-display text-8xl font-black text-accent leading-none">
                {data.countries?.length ?? 0}
              </div>
              <div className="text-slate-500 text-sm uppercase tracking-widest mt-2">{lang === 'en' ? 'countries' : 'країн'}</div>
            </div>
          </motion.div>

          {/* Правая — карточки */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            {data.countries.map((country, i) => (
              <CountryCard key={country.id} country={country} index={i} lang={lang} />
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
