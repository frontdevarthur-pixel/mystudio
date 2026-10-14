'use client';

import { useEffect, useRef, useState } from 'react';
import { useInView } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';

interface AnimatedCounterProps {
  value: number;
  suffix?: string;
  duration?: number;
}

export default function AnimatedCounter({ value, suffix = '', duration = 2000 }: AnimatedCounterProps) {
  const reducedMotion = useReducedMotion();
  const [count, setCount] = useState(reducedMotion ? value : 0);
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-40px' });

  useEffect(() => {
    if (!isInView || reducedMotion) return;

    let start = 0;
    const step = Math.ceil(value / (duration / 16));
    const timer = setInterval(() => {
      start += step;
      if (start >= value) {
        setCount(value);
        clearInterval(timer);
      } else {
        setCount(start);
      }
    }, 16);

    return () => clearInterval(timer);
  }, [isInView, value, duration, reducedMotion]);

  return (
    <span ref={ref} className="tabular-nums">
      {count}{suffix}
    </span>
  );
}
