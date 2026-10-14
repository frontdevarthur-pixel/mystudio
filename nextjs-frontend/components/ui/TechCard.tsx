import Image from 'next/image';
import type { Technology } from '@/types/api';

interface TechCardProps {
  tech: Technology;
}

export default function TechCard({ tech }: TechCardProps) {
  return (
    <div className="glass-card rounded-xl p-4 flex flex-col items-center gap-3 hover:border-accent/30 hover:bg-accent/5 transition-all duration-200 group cursor-default">
      {tech.logo_url ? (
        <div className="relative w-10 h-10">
          <Image
            src={tech.logo_url}
            alt={tech.logo_alt || tech.name}
            fill
            className="object-contain"
          />
        </div>
      ) : (
        <div className="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center font-display font-bold text-accent text-base group-hover:bg-accent/20 transition-colors">
          {tech.name.charAt(0)}
        </div>
      )}
      <span className="text-xs text-slate-400 text-center font-medium group-hover:text-white transition-colors leading-tight">
        {tech.name}
      </span>
    </div>
  );
}
