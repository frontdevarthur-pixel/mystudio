import Image from 'next/image';
import type { TeamMember } from '@/types/api';

interface TeamCardProps {
  member: TeamMember;
  large?: boolean;
}

export default function TeamCard({ member, large }: TeamCardProps) {
  if (large) {
    return (
      <div className="glass-card rounded-2xl overflow-hidden group hover:border-accent/25 transition-all duration-300">
        {/* Большое фото */}
        <div className="relative w-full aspect-[3/4] bg-accent/5">
          {member.photo_url ? (
            <Image
              src={member.photo_url}
              alt={member.photo_alt || member.name}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-500"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center">
              <span className="font-display text-6xl font-black text-accent/30">
                {member.name.charAt(0)}
              </span>
            </div>
          )}
          {/* Градиент снизу */}
          <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-[#0d1117] to-transparent" />
        </div>
        {/* Инфо */}
        <div className="px-5 py-4">
          <h3 className="font-display text-white font-bold text-base">{member.name}</h3>
          <p className="text-slate-500 text-sm mt-0.5">{member.position}</p>
        </div>
      </div>
    );
  }

  return (
    <div className="glass-card rounded-2xl p-6 flex flex-col items-center text-center group hover:border-accent/25 transition-all duration-300">
      <div className="relative w-16 h-16 rounded-full overflow-hidden mb-4 ring-2 ring-white/10 group-hover:ring-accent/40 transition-all">
        {member.photo_url ? (
          <Image
            src={member.photo_url}
            alt={member.photo_alt || member.name}
            fill
            className="object-cover"
          />
        ) : (
          <div className="w-full h-full bg-accent/15 flex items-center justify-center font-display text-xl font-bold text-accent">
            {member.name.charAt(0)}
          </div>
        )}
      </div>
      <h3 className="font-display text-white font-semibold text-sm">{member.name}</h3>
      <p className="text-slate-500 text-xs mt-1">{member.position}</p>
    </div>
  );
}
