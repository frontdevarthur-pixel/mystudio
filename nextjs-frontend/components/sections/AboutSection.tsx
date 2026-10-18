'use client';

import { motion } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import { useContactModal } from '@/components/ui/ContactModal';
import TeamCard from '@/components/ui/TeamCard';
import { useRef, useState } from 'react';
import type { AboutSection as AboutData, TeamMember, SectionLabels } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

interface AboutSectionProps {
  data: AboutData;
  team: TeamMember[];
  settings?: unknown;
  labels?: Partial<SectionLabels>;
  lang?: Lang;
}

function TeamSlider({ team, reducedMotion, teamTitle, lang = 'uk' }: { team: TeamMember[]; reducedMotion: boolean; teamTitle: string; lang?: Lang }) {
  const scrollRef = useRef<HTMLDivElement>(null);
  const [current, setCurrent] = useState(0);
  const CARD_W = 256 + 20;

  const scrollTo = (index: number) => {
    const next = Math.max(0, Math.min(index, team.length - 1));
    setCurrent(next);
    scrollRef.current?.scrollTo({ left: next * CARD_W, behavior: 'smooth' });
  };

  return (
    <>
      <motion.div
        initial={reducedMotion ? false : { opacity: 0 }}
        whileInView={{ opacity: 1 }}
        viewport={{ once: true }}
        transition={{ duration: 0.5 }}
        className="flex items-end justify-between mb-8"
      >
        <h2 className="heading-outline text-4xl sm:text-5xl">{teamTitle}</h2>
        <div className="flex gap-2">
          <button onClick={() => scrollTo(current - 1)} disabled={current === 0} className="w-10 h-10 rounded-full glass-card flex items-center justify-center text-slate-400 hover:text-white hover:border-accent/40 disabled:opacity-30 transition-all" aria-label={lang === 'en' ? 'Previous' : 'Назад'}>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
          </button>
          <button onClick={() => scrollTo(current + 1)} disabled={current >= team.length - 1} className="w-10 h-10 rounded-full glass-card flex items-center justify-center text-slate-400 hover:text-white hover:border-accent/40 disabled:opacity-30 transition-all" aria-label={lang === 'en' ? 'Next' : 'Вперед'}>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
          </button>
        </div>
      </motion.div>

      <div className="relative">
        <div ref={scrollRef} className="flex gap-5 overflow-x-auto pb-2 snap-x snap-mandatory scrollbar-hide -mx-4 px-4"
          onScroll={e => { const idx = Math.round((e.currentTarget.scrollLeft) / CARD_W); setCurrent(idx); }}>
          {team.map((member, i) => (
            <motion.div key={member.id} initial={reducedMotion ? false : { opacity: 0, x: 30 }} whileInView={{ opacity: 1, x: 0 }} viewport={{ once: true }} transition={{ duration: 0.4, delay: i * 0.06 }} className="flex-shrink-0 snap-start w-56 sm:w-64">
              <TeamCard member={member} large />
            </motion.div>
          ))}
        </div>
      </div>

      <div className="flex justify-center gap-2 mt-5">
        {team.map((_, i) => (
          <button key={i} onClick={() => scrollTo(i)} className={`transition-all duration-200 rounded-full ${i === current ? 'w-6 h-1.5 bg-accent' : 'w-1.5 h-1.5 bg-white/20 hover:bg-white/40'}`} aria-label={`${lang === 'en' ? 'Slide' : 'Слайд'} ${i + 1}`} />
        ))}
      </div>
    </>
  );
}

export default function AboutSection({ data, team, labels, lang = 'uk' }: AboutSectionProps) {
  const reducedMotion = useReducedMotion();
  const { open } = useContactModal();

  const teamTitle = labels?.about_team_title || (lang === 'en' ? 'Team' : 'Команда');
  const ctaBtn    = labels?.about_cta        || (lang === 'en' ? 'Discuss project →' : 'Обговорити проект →');

  return (
    <section id="about" className="py-28 bg-[#080b11] relative overflow-hidden">
      <div className="glow-blob w-[500px] h-[500px] bg-accent/5 top-0 right-[-200px]" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-24">
          <motion.div initial={reducedMotion ? false : { opacity: 0, x: -30 }} whileInView={{ opacity: 1, x: 0 }} viewport={{ once: true }} transition={{ duration: 0.7 }}>
            <h2 className="heading-outline text-5xl sm:text-6xl lg:text-7xl mb-6">{data.title ?? (lang === 'en' ? 'About us' : 'Про нас')}</h2>
            <div className="accent-line mb-8" />
            {data.history && <div className="prose prose-invert prose-lg max-w-none text-slate-400 mb-8" dangerouslySetInnerHTML={{ __html: data.history }} />}
            {data.mission && <blockquote className="border-l-2 border-accent pl-5 mb-8"><p className="text-slate-300 text-lg italic">{data.mission}</p></blockquote>}
            {data.values?.length > 0 && (
              <div className="flex flex-wrap gap-2 mb-8">
                {data.values.map((val, i) => (
                  <span key={i} className="inline-flex items-center gap-1.5 px-4 py-2 glass-card rounded-full text-sm text-slate-300 hover:border-accent/30 hover:text-white transition-colors">
                    <span className="w-1.5 h-1.5 rounded-full bg-accent flex-shrink-0" />{val}
                  </span>
                ))}
              </div>
            )}
            <button onClick={open} className="btn-accent px-7 py-3.5 rounded-xl text-sm">{ctaBtn}</button>
          </motion.div>

          <motion.div initial={reducedMotion ? false : { opacity: 0, x: 30 }} whileInView={{ opacity: 1, x: 0 }} viewport={{ once: true }} transition={{ duration: 0.7, delay: 0.1 }} className="relative">
            <div className="relative aspect-[4/3] rounded-2xl overflow-hidden">
              <div className="absolute inset-0 bg-gradient-to-br from-accent/10 via-transparent to-blue-500/10 flex items-center justify-center">
                <div className="text-center">
                  <div className="text-6xl mb-4">🏢</div>
                  <p className="text-slate-500 text-sm">{lang === 'en' ? 'Team photo' : 'Фото команди'}</p>
                  <p className="text-slate-600 text-xs mt-1">{lang === 'en' ? 'Upload via WordPress Admin' : 'Завантажте через WordPress Admin'}</p>
                </div>
              </div>
              <div className="absolute inset-0 rounded-2xl border border-white/8" />
            </div>
            {data.stats?.[0] && (
              <div className="absolute -bottom-5 -left-5 glass-card rounded-xl px-5 py-4 border-accent/20">
                <div className="font-display text-3xl font-bold text-accent">{data.stats[0].value}{data.stats[0].suffix}</div>
                <div className="text-xs text-slate-400 mt-0.5">{data.stats[0].label}</div>
              </div>
            )}
            {data.stats?.[1] && (
              <div className="absolute -top-5 -right-5 glass-card rounded-xl px-5 py-4 border-accent/20">
                <div className="font-display text-3xl font-bold text-white">{data.stats[1].value}{data.stats[1].suffix}</div>
                <div className="text-xs text-slate-400 mt-0.5">{data.stats[1].label}</div>
              </div>
            )}
          </motion.div>
        </div>

        {team?.length > 0 && <TeamSlider team={team} reducedMotion={reducedMotion} teamTitle={teamTitle} lang={lang} />}
      </div>
    </section>
  );
}
