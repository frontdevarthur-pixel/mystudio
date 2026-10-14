'use client';

import { motion } from 'framer-motion';
import { useContactModal } from '@/components/ui/ContactModal';
import type { SiteSettings } from '@/types/api';

interface FloatingContactsProps {
  settings: SiteSettings;
}

export default function FloatingContacts({ settings }: FloatingContactsProps) {
  const { open } = useContactModal();

  const telegramLink = settings.social_links?.find(l =>
    l.platform === 'telegram' || l.url?.includes('t.me')
  );

  return (
    <div className="fixed right-5 bottom-8 z-40 flex flex-col gap-3 items-end">
      {/* Email */}
      {settings.email && (
        <motion.a
          href={`mailto:${settings.email}`}
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 1 }}
          whileHover={{ scale: 1.08 }}
          className="group flex items-center gap-3"
          aria-label="Написать на email"
        >
          <span className="hidden group-hover:block text-xs text-slate-400 bg-[#0d1117] border border-white/8 px-3 py-1.5 rounded-lg whitespace-nowrap">
            {settings.email}
          </span>
          <div className="w-12 h-12 rounded-full bg-[#0d1117] border border-white/10 flex items-center justify-center shadow-lg hover:border-accent/40 hover:bg-accent/10 transition-all">
            <svg width="18" height="14" viewBox="0 0 18 14" fill="none" className="text-accent">
              <path d="M1 1l8 6 8-6M1 13h16V1H1v12z" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          </div>
        </motion.a>
      )}

      {/* Telegram */}
      {telegramLink && (
        <motion.a
          href={telegramLink.url}
          target="_blank"
          rel="noopener noreferrer"
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 1.1 }}
          whileHover={{ scale: 1.08 }}
          className="group flex items-center gap-3"
          aria-label="Написать в Telegram"
        >
          <span className="hidden group-hover:block text-xs text-slate-400 bg-[#0d1117] border border-white/8 px-3 py-1.5 rounded-lg whitespace-nowrap">
            Telegram
          </span>
          <div className="w-12 h-12 rounded-full bg-[#0d1117] border border-white/10 flex items-center justify-center shadow-lg hover:border-accent/40 hover:bg-accent/10 transition-all">
            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" className="text-accent">
              <path d="M1 8L16 1l-5 14-3-5-7-2z" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          </div>
        </motion.a>
      )}

      {/* CTA — попап */}
      <motion.button
        onClick={open}
        initial={{ opacity: 0, x: 20 }}
        animate={{ opacity: 1, x: 0 }}
        transition={{ delay: 1.2 }}
        whileHover={{ scale: 1.08 }}
        className="w-12 h-12 rounded-full bg-accent flex items-center justify-center shadow-xl shadow-accent/30 hover:bg-accent-dim transition-colors"
        aria-label="Обсудить проект"
      >
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" className="text-[#080b11]">
          <path d="M8 1v14M1 8h14" stroke="currentColor" strokeWidth="2" strokeLinecap="round"/>
        </svg>
      </motion.button>
    </div>
  );
}
