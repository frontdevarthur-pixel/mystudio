'use client';

import { useState, useEffect } from 'react';
import Image from 'next/image';
import { useContactModal } from '@/components/ui/ContactModal';
import type { SiteSettings } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

const NAV_LINKS_UK = [
  { label: 'Про нас', href: '#about' },
  { label: 'Послуги', href: '#services' },
  { label: 'Технології', href: '#technologies' },
  { label: 'Портфоліо', href: '#portfolio' },
  { label: 'Контакти', href: '#contact' },
];

const NAV_LINKS_EN = [
  { label: 'About', href: '#about' },
  { label: 'Services', href: '#services' },
  { label: 'Technologies', href: '#technologies' },
  { label: 'Portfolio', href: '#portfolio' },
  { label: 'Contact', href: '#contact' },
];

interface HeaderProps {
  settings: SiteSettings;
  lang?: Lang;
}

export default function Header({ settings, lang = 'uk' }: HeaderProps) {
  const [menuOpen, setMenuOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const { open: openContact } = useContactModal();

  const NAV_LINKS = lang === 'en' ? NAV_LINKS_EN : NAV_LINKS_UK;
  const ctaLabel = lang === 'en' ? 'Discuss project' : 'Обговорити проект';
  const altLangHref = lang === 'en' ? '/' : '/en';
  const altLangLabel = lang === 'en' ? 'UA' : 'EN';

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, href: string) => {
    e.preventDefault();
    setMenuOpen(false);
    const el = document.querySelector(href);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  };

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled
          ? 'bg-[#080b11]/95 backdrop-blur-xl border-b border-white/5 shadow-xl shadow-black/30'
          : 'bg-transparent'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-18 py-4">
        {/* Логотип */}
        <a
          href="#hero"
          onClick={e => handleNavClick(e, '#hero')}
          className="flex items-center gap-3 group"
        >
          {settings.logo_url ? (
            <Image
              src={settings.logo_url}
              alt={settings.site_name}
              width={36}
              height={36}
              className="object-contain"
            />
          ) : (
            <div className="w-8 h-8 rounded-lg bg-accent flex items-center justify-center">
              <span className="text-[#080b11] font-bold text-sm font-display">
                {settings.site_name?.charAt(0) ?? 'W'}
              </span>
            </div>
          )}
          <span className="text-lg font-bold text-white font-display group-hover:text-accent transition-colors">
            {settings.site_name}
          </span>
        </a>

        {/* Десктоп навигация */}
        <nav className="hidden md:flex items-center gap-8">
          {NAV_LINKS.map(link => (
            <a
              key={link.href}
              href={link.href}
              onClick={e => handleNavClick(e, link.href)}
              className="text-sm text-slate-400 hover:text-white transition-colors duration-200 font-medium relative group"
            >
              {link.label}
              <span className="absolute -bottom-1 left-0 w-0 h-px bg-accent transition-all duration-200 group-hover:w-full" />
            </a>
          ))}
        </nav>

        {/* CTA кнопка + переключатель языка */}
        <div className="hidden md:flex items-center gap-3">
          <a
            href={altLangHref}
            className="text-xs font-display font-bold tracking-widest uppercase text-slate-500 hover:text-accent transition-colors px-2 py-1 rounded border border-white/10 hover:border-accent/40"
          >
            {altLangLabel}
          </a>
          <button
            onClick={openContact}
            className="btn-accent px-5 py-2 rounded-lg text-sm"
          >
            {ctaLabel}
          </button>
        </div>

        {/* Мобильный бургер */}
        <button
          className="md:hidden p-2 text-slate-300 hover:text-white"
          onClick={() => setMenuOpen(prev => !prev)}
          aria-label="Открыть меню"
        >
          <div className="space-y-1.5 w-6">
            <span className={`block h-px bg-current transition-all duration-200 ${menuOpen ? 'rotate-45 translate-y-2.5 w-6' : 'w-6'}`} />
            <span className={`block h-px bg-current transition-all duration-200 ${menuOpen ? 'opacity-0 w-3' : 'w-4'}`} />
            <span className={`block h-px bg-current transition-all duration-200 ${menuOpen ? '-rotate-45 -translate-y-2.5 w-6' : 'w-5'}`} />
          </div>
        </button>
      </div>

      {/* Мобильное меню */}
      {menuOpen && (
        <nav className="md:hidden bg-[#0d1117] border-t border-white/5 px-4 py-6 flex flex-col gap-5">
          {NAV_LINKS.map(link => (
            <a
              key={link.href}
              href={link.href}
              onClick={e => handleNavClick(e, link.href)}
              className="text-slate-300 hover:text-white transition-colors font-medium"
            >
              {link.label}
            </a>
          ))}
          <button
            onClick={openContact}
            className="btn-accent px-5 py-3 rounded-lg text-sm text-center mt-2"
          >
            {ctaLabel}
          </button>
          <a
            href={altLangHref}
            className="text-center text-xs font-display font-bold tracking-widest uppercase text-slate-500 hover:text-accent transition-colors py-2"
          >
            {altLangLabel === 'EN' ? '🇬🇧 English' : '🇺🇦 Українська'}
          </a>
        </nav>
      )}
    </header>
  );
}
