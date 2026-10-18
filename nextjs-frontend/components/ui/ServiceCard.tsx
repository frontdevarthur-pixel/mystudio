'use client';

import { motion } from 'framer-motion';
import Image from 'next/image';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import type { Service } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

const SERVICE_ICONS = ['⚡', '🏢', '🛒', '🚀', '📈', '🔧'];

interface ServiceCardProps {
  service: Service;
  index?: number;
  lang?: Lang;
}

export default function ServiceCard({ service, index = 0, lang = 'uk' }: ServiceCardProps) {
  const reducedMotion = useReducedMotion();

  return (
    <motion.div
      className="glass-card rounded-2xl p-7 flex flex-col gap-5 cursor-default group hover:border-accent/25 transition-all duration-300"
      whileHover={reducedMotion ? {} : { y: -4 }}
      transition={{ duration: 0.2 }}
    >
      {/* Иконка */}
      <div className="flex items-center justify-between">
        {service.icon_url ? (
          <div className="w-10 h-10 relative">
            <Image src={service.icon_url} alt={service.icon_alt || service.title} fill className="object-contain" />
          </div>
        ) : (
          <div className="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-xl">
            {SERVICE_ICONS[index % SERVICE_ICONS.length]}
          </div>
        )}
        <span className="font-display text-4xl font-bold text-white/5 group-hover:text-white/8 transition-colors">
          {String(index + 1).padStart(2, '0')}
        </span>
      </div>

      <div>
        <h3 className="font-display text-white font-semibold text-lg mb-2 group-hover:text-accent transition-colors">
          {service.title}
        </h3>
        <p className="text-slate-400 text-sm leading-relaxed">{service.description}</p>
      </div>

      <div className="mt-auto">
        <span className="text-accent text-xs font-display font-semibold tracking-widest uppercase opacity-0 group-hover:opacity-100 transition-opacity">
          {lang === 'en' ? 'Learn more →' : 'Детальніше →'}
        </span>
      </div>
    </motion.div>
  );
}
