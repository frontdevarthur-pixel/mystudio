'use client';

import { useReducedMotion } from '@/lib/hooks/useReducedMotion';

interface MarqueeProps {
  items: string[];
  direction?: 'left' | 'right';
  speed?: number;
}

export default function Marquee({ items, direction = 'left', speed = 40 }: MarqueeProps) {
  const reducedMotion = useReducedMotion();
  const doubled = [...items, ...items];

  return (
    <div className="overflow-hidden py-4 relative">
      {/* Fade edges */}
      <div className="absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-[#080b11] to-transparent z-10 pointer-events-none" />
      <div className="absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-[#080b11] to-transparent z-10 pointer-events-none" />

      <div
        className="flex gap-6 w-max"
        style={
          reducedMotion
            ? {}
            : {
                animation: `marquee-${direction} ${speed}s linear infinite`,
              }
        }
      >
        {doubled.map((item, i) => (
          <div
            key={i}
            className="flex items-center gap-6 flex-shrink-0"
          >
            <span className="font-display font-semibold text-sm uppercase tracking-widest text-slate-500 whitespace-nowrap hover:text-accent transition-colors cursor-default">
              {item}
            </span>
            <span className="text-accent/40 text-xs">✦</span>
          </div>
        ))}
      </div>
    </div>
  );
}
