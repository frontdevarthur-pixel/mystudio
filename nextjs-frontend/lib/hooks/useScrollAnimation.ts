'use client';

import { useInView } from 'framer-motion';
import { useRef } from 'react';

/**
 * Хук для scroll-triggered анимаций с поддержкой prefers-reduced-motion.
 * При reducedMotion === true возвращает пустые варианты анимации.
 */
export function useScrollAnimation(reducedMotion: boolean) {
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-80px' });

  if (reducedMotion) {
    return { ref, isInView: true, variants: {} };
  }

  return { ref, isInView, variants: null };
}
