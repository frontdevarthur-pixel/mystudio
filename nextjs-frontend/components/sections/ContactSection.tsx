'use client';

import { motion } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import ContactForm from '@/components/ui/ContactForm';
import MapEmbed from '@/components/ui/MapEmbed';
import type { SiteSettings } from '@/types/api';

interface ContactSectionProps {
  settings: SiteSettings;
}

export default function ContactSection({ settings }: ContactSectionProps) {
  const reducedMotion = useReducedMotion();

  return (
    <section id="contact" className="py-28 bg-[#0d1117] relative overflow-hidden">
      <div className="glow-blob w-[600px] h-[600px] bg-accent/7 bottom-[-200px] left-1/2 -translate-x-1/2" />

      {/* Большой фоновый текст */}
      <div className="absolute bottom-0 left-0 right-0 flex justify-center pointer-events-none select-none overflow-hidden">
        <span className="font-display text-[18vw] font-black text-white/[0.012] leading-none tracking-tighter whitespace-nowrap">
          LET'S TALK
        </span>
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
          {/* Левая — заголовок + контакты */}
          <motion.div
            initial={reducedMotion ? false : { opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="heading-outline text-5xl sm:text-6xl lg:text-7xl mb-8">
              Готовы начать?
            </h2>

            <p className="text-slate-400 text-lg leading-relaxed mb-10 max-w-sm">
              {settings.site_description || 'Расскажите о задаче — обсудим детали и предложим решение'}
            </p>

            {/* Контакты */}
            <div className="space-y-4 mb-8">
              {settings.phone && (
                <a href={`tel:${settings.phone}`} className="flex items-center gap-4 group">
                  <div className="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" className="text-accent">
                      <path d="M16.5 13.5c0 .33-.07.655-.22.97a3.665 3.665 0 01-.6.88C15.2 15.87 14.62 16.12 14 16.12c-.65 0-1.35-.165-2.09-.5a16.775 16.775 0 01-2.09-1.345 16.385 16.385 0 01-1.875-1.875 16.68 16.68 0 01-1.34-2.085c-.33-.74-.495-1.44-.495-2.095 0-.63.135-1.215.405-1.755.27-.545.66-1.04 1.185-1.48.63-.525 1.32-.785 2.05-.785.285 0 .57.06.825.18.26.12.49.3.67.555l2.245 3.165c.18.25.31.48.4.695.09.21.14.415.14.6 0 .24-.065.48-.195.715a3.49 3.49 0 01-.525.715c.195.27.405.53.63.78.225.25.46.49.7.72.255.225.515.44.785.635.255.195.51.375.765.54.22.135.445.195.675.195z" stroke="currentColor" strokeWidth="1.2" strokeMiterlimit="10"/>
                    </svg>
                  </div>
                  <span className="text-slate-300 font-medium group-hover:text-white transition-colors">
                    {settings.phone}
                  </span>
                </a>
              )}
              {settings.email && (
                <a href={`mailto:${settings.email}`} className="flex items-center gap-4 group">
                  <div className="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors">
                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" className="text-accent">
                      <path d="M1 1l8 6 8-6M1 13h16V1H1v12z" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                  </div>
                  <span className="text-slate-300 font-medium group-hover:text-white transition-colors">
                    {settings.email}
                  </span>
                </a>
              )}
              {settings.address && (
                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg width="14" height="18" viewBox="0 0 14 18" fill="none" className="text-accent">
                      <path d="M7 1C4.24 1 2 3.24 2 6c0 3.75 5 11 5 11s5-7.25 5-11c0-2.76-2.24-5-5-5zm0 6.5A1.5 1.5 0 115 6a1.5 1.5 0 012 1.5z" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                  </div>
                  <span className="text-slate-400 leading-relaxed mt-2">{settings.address}</span>
                </div>
              )}
            </div>

            {/* Соцсети */}
            {settings.social_links?.length > 0 && (
              <div className="flex flex-wrap gap-3">
                {settings.social_links.filter(l => l.label && l.url).map((link, i) => (
                  <a
                    key={i}
                    href={link.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="btn-outline px-4 py-2 rounded-lg text-sm"
                  >
                    {link.label}
                  </a>
                ))}
              </div>
            )}
          </motion.div>

          {/* Правая — форма + карта */}
          <motion.div
            initial={reducedMotion ? false : { opacity: 0, x: 20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="space-y-6"
          >
            <div className="glass-card rounded-2xl p-7 border-accent/10">
              <ContactForm />
            </div>

            {settings.map_coordinates?.lat && settings.map_coordinates?.lng && (
              <div className="rounded-2xl overflow-hidden border border-white/6 h-48">
                <MapEmbed
                  lat={settings.map_coordinates.lat}
                  lng={settings.map_coordinates.lng}
                  markerLabel={settings.office_marker_label || settings.site_name}
                />
              </div>
            )}
          </motion.div>
        </div>
      </div>
    </section>
  );
}
