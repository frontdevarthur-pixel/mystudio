'use client';

import { motion } from 'framer-motion';
import Image from 'next/image';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import { useContactModal } from '@/components/ui/ContactModal';
import { filterVisible, sortBySortOrder } from '@/lib/utils/sort-filter';
import type { Service, SiteSettings, SectionLabels } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

const ICONS = ['⚡', '🏢', '🛒', '🚀', '📈', '🔧'];

interface ServicesSectionProps {
  services: Service[];
  settings: SiteSettings;
  labels?: Pick<SectionLabels, 'services_title' | 'services_section_desc' | 'services_cta'>;
  lang?: Lang;
}

export default function ServicesSection({ services, settings, labels, lang = 'uk' }: ServicesSectionProps) {
  const reducedMotion = useReducedMotion();
  const { open } = useContactModal();
  const items = sortBySortOrder(filterVisible(services));

  const title = labels?.services_title || (lang === 'en' ? 'What we do' : 'Що ми вміємо');
  const description = labels?.services_section_desc || settings.site_description || (lang === 'en' ? 'Full cycle — from idea and design to launch and support' : 'Повний цикл — від ідеї та дизайну до запуску і підтримки');
  const ctaBtn = labels?.services_cta || (lang === 'en' ? 'Discuss project →' : 'Обговорити проект →');

  return (
    <section id="services" className="py-28 bg-[#0d1117] relative overflow-hidden">
      <div className="glow-blob w-[500px] h-[500px] bg-blue-500/5 bottom-0 left-[-150px]" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Асимметричный заголовок — большой текст слева, CTA справа */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-20">
          <motion.div
            initial={reducedMotion ? false : { opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="heading-outline text-5xl sm:text-6xl lg:text-7xl whitespace-nowrap">
              {title}
            </h2>
          </motion.div>

          <motion.div
            initial={reducedMotion ? false : { opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.15 }}
            className="lg:max-w-xs"
          >
            <p className="text-slate-400 text-lg leading-relaxed mb-6">
              {description}
            </p>
            <button onClick={open} className="btn-outline px-6 py-3 rounded-xl text-sm">
              {ctaBtn}
            </button>
          </motion.div>
        </div>

        {/* Сетка услуг */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px bg-white/5 rounded-2xl overflow-hidden">
          {items.map((service, i) => (
            <motion.div
              key={service.id}
              initial={reducedMotion ? false : { opacity: 0 }}
              whileInView={{ opacity: 1 }}
              viewport={{ once: true }}
              transition={{ duration: 0.4, delay: i * 0.06 }}
              className="bg-[#0d1117] p-8 hover:bg-accent/5 transition-colors duration-300 group cursor-default"
            >
              <div className="flex items-start justify-between mb-6">
                {service.icon_url ? (
                  <div className="relative w-12 h-12">
                    <Image src={service.icon_url} alt={service.title} fill className="object-contain" />
                  </div>
                ) : (
                  <span className="text-3xl">{ICONS[i % ICONS.length]}</span>
                )}
                <span className="font-display text-5xl font-black text-white/[0.04] group-hover:text-white/[0.07] transition-colors leading-none">
                  {String(i + 1).padStart(2, '0')}
                </span>
              </div>
              <h3 className="font-display text-xl font-bold text-white mb-3 group-hover:text-accent transition-colors">
                {service.title}
              </h3>
              <p className="text-slate-500 text-sm leading-relaxed">{service.description}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}
