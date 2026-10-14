'use client';

import { useEffect, useRef, useState, useCallback } from 'react';
import { motion } from 'framer-motion';
import Image from 'next/image';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import { useContactModal } from '@/components/ui/ContactModal';
import ParticleCanvas from '@/components/ui/ParticleCanvas';
import type { HeroSection as HeroData, SiteSettings } from '@/types/api';

interface HeroSectionProps {
  data: HeroData;
  settings: SiteSettings;
}

function MagneticButton({
  children,
  onClick,
  className,
}: {
  children: React.ReactNode;
  onClick?: () => void;
  className?: string;
}) {
  const ref = useRef<HTMLButtonElement>(null);
  const reducedMotion = useReducedMotion();

  const handleMouse = useCallback((e: React.MouseEvent) => {
    if (reducedMotion || !ref.current) return;
    const rect = ref.current.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;
    ref.current.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
    ref.current.style.transition = 'transform 0.1s ease';
  }, [reducedMotion]);

  const handleLeave = useCallback(() => {
    if (!ref.current) return;
    ref.current.style.transform = 'translate(0, 0)';
    ref.current.style.transition = 'transform 0.4s cubic-bezier(0.23, 1, 0.32, 1)';
  }, []);

  return (
    <button
      ref={ref}
      onClick={onClick}
      className={className}
      onMouseMove={handleMouse}
      onMouseLeave={handleLeave}
    >
      {children}
    </button>
  );
}

function TypingText({ word }: { word: string }) {
  const [displayed, setDisplayed] = useState('');
  const [done, setDone] = useState(false);
  const reducedMotion = useReducedMotion();

  useEffect(() => {
    if (reducedMotion) { setDisplayed(word); setDone(true); return; }
    if (done) return;
    if (displayed.length < word.length) {
      const t = setTimeout(() => setDisplayed(word.slice(0, displayed.length + 1)), 110);
      return () => clearTimeout(t);
    } else {
      setDone(true);
    }
  }, [displayed, done, word, reducedMotion]);

  return (
    <span className="text-accent whitespace-nowrap">
      {displayed}
      <span className="cursor-blink ml-0.5 inline-block w-[3px] h-[0.85em] bg-accent align-middle rounded-sm" />
    </span>
  );
}

export default function HeroSection({ data, settings }: HeroSectionProps) {
  const reducedMotion = useReducedMotion();
  const { open: openContact } = useContactModal();

  const scrollToPortfolio = () => {
    document.querySelector('#portfolio')?.scrollIntoView({ behavior: 'smooth' });
  };

  const words = (data.heading ?? '').split(' ');
  const staticPart = words.slice(0, -1).join(' ');
  const lastWord = words[words.length - 1] ?? '';

  return (
    <section
      id="hero"
      className="relative min-h-screen flex items-center justify-center overflow-hidden"
      style={{
        background: 'radial-gradient(ellipse 80% 60% at 50% -10%, rgba(0,255,136,0.12) 0%, transparent 60%), radial-gradient(ellipse 60% 50% at 80% 80%, rgba(59,130,246,0.07) 0%, transparent 60%), #080b11',
      }}
    >
      <ParticleCanvas />

      {data.background_type === 'image' && data.background_image_url && (
        <Image src={data.background_image_url} alt="Hero" fill priority className="object-cover opacity-10" />
      )}

      {/* Сетка */}
      <div
        className="absolute inset-0 opacity-[0.028] pointer-events-none"
        style={{
          backgroundImage: 'linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px)',
          backgroundSize: '80px 80px',
        }}
      />

      {/* Угасающий оверлей снизу */}
      <div className="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-[#080b11] to-transparent pointer-events-none" />

      <div className="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-24 pb-36">
        {/* Бейдж */}
        <motion.div
          initial={reducedMotion ? false : { opacity: 0, y: 16 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="flex justify-center mb-8"
        >
          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card border-accent/20">
            <span className="w-2 h-2 rounded-full bg-accent glow-pulse flex-shrink-0" />
            <span className="text-xs font-display font-semibold tracking-widest uppercase text-slate-300">
                {settings.site_description || settings.site_name}
              </span>
          </div>
        </motion.div>

        {/* Заголовок */}
        <motion.h1
          initial={reducedMotion ? false : { opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.1 }}
          className="font-display text-5xl sm:text-6xl md:text-7xl lg:text-[5.5rem] font-bold text-white leading-[1.05] mb-6 tracking-tight"
        >
          {staticPart}{' '}
          <TypingText word={lastWord} />
        </motion.h1>

        {/* Подзаголовок */}
        <motion.p
          initial={reducedMotion ? false : { opacity: 0, y: 24 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.2 }}
          className="text-slate-400 text-lg sm:text-xl max-w-2xl mx-auto mb-12 leading-relaxed"
        >
          {data.subheading}
        </motion.p>

        {/* CTA */}
        <motion.div
          initial={reducedMotion ? false : { opacity: 0, y: 24 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.3 }}
          className="flex flex-col sm:flex-row items-center justify-center gap-4"
        >
          <MagneticButton
            onClick={openContact}
            className="btn-accent px-9 py-4 rounded-xl text-base w-full sm:w-auto shadow-xl shadow-accent/20"
          >
            {data.cta_text} →
          </MagneticButton>
          <MagneticButton
            onClick={scrollToPortfolio}
            className="btn-outline px-9 py-4 rounded-xl text-base w-full sm:w-auto"
          >
            Смотреть работы
          </MagneticButton>
        </motion.div>


      </div>

      {/* Scroll */}
      {!reducedMotion && (
        <motion.div
          className="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 cursor-default"
          animate={{ y: [0, 8, 0] }}
          transition={{ repeat: Infinity, duration: 2, ease: 'easeInOut' }}
        >
          <span className="text-[10px] font-display tracking-[0.25em] uppercase text-slate-600">Scroll</span>
          <div className="w-px h-8 bg-gradient-to-b from-accent/50 to-transparent" />
        </motion.div>
      )}
    </section>
  );
}
