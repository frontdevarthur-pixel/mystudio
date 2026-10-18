'use client';

import { createContext, useContext, useState, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import ContactForm from '@/components/ui/ContactForm';
import type { Lang } from '@/lib/api/endpoints';

const ContactModalContext = createContext<{ open: () => void }>({ open: () => {} });

export function useContactModal() {
  return useContext(ContactModalContext);
}

const modalI18n = {
  uk: { title: 'Обговорити проект', subtitle: 'Розкажіть про задачу — відповімо протягом години', close: 'Закрити' },
  en: { title: 'Discuss a project', subtitle: 'Tell us about your task — we\'ll reply within an hour', close: 'Close' },
};

export function ContactModalProvider({ children, lang = 'uk' }: { children: React.ReactNode; lang?: Lang }) {
  const [isOpen, setIsOpen] = useState(false);
  const open = useCallback(() => setIsOpen(true), []);
  const close = useCallback(() => setIsOpen(false), []);
  const t = modalI18n[lang] ?? modalI18n.uk;

  return (
    <ContactModalContext.Provider value={{ open }}>
      {children}

      <AnimatePresence>
        {isOpen && (
          <motion.div
            className="fixed inset-0 z-[100] flex items-center justify-center p-4"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
          >
            {/* Backdrop */}
            <motion.div
              className="absolute inset-0 bg-black/80 backdrop-blur-md"
              onClick={close}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
            />

            {/* Modal */}
            <motion.div
              className="relative z-10 w-full max-w-lg"
              initial={{ scale: 0.92, opacity: 0, y: 20 }}
              animate={{ scale: 1, opacity: 1, y: 0 }}
              exit={{ scale: 0.92, opacity: 0, y: 20 }}
              transition={{ duration: 0.25, ease: [0.23, 1, 0.32, 1] }}
            >
              {/* Glow */}
              <div className="absolute -inset-px rounded-2xl bg-gradient-to-br from-accent/30 via-transparent to-transparent blur-sm" />

              <div className="relative bg-[#0d1117] rounded-2xl border border-accent/20 shadow-2xl shadow-accent/5 overflow-hidden">
                {/* Header */}
                <div className="px-7 pt-7 pb-5 border-b border-white/5">
                  <div className="flex items-start justify-between gap-4">
                    <div>
                      <h2 className="font-display text-2xl font-bold text-white">{t.title}</h2>
                      <p className="text-slate-400 text-sm mt-1">{t.subtitle}</p>
                    </div>
                    <button
                      onClick={close}
                      className="flex-shrink-0 w-8 h-8 rounded-lg glass-card flex items-center justify-center text-slate-400 hover:text-white hover:border-white/20 transition-colors"
                      aria-label={t.close}
                    >
                      <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                      </svg>
                    </button>
                  </div>
                </div>

                {/* Form */}
                <div className="p-7">
                  <ContactForm onSuccess={close} lang={lang} />
                </div>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </ContactModalContext.Provider>
  );
}
