'use client';

import dynamic from 'next/dynamic';

interface MapEmbedProps {
  lat: number;
  lng: number;
  markerLabel: string;
}

// Внутренний компонент с картой (загружается только на клиенте)
function LeafletMap({ lat, lng, markerLabel }: MapEmbedProps) {
  // Используем iframe Google Maps как простой fallback если нет ключа,
  // или можно заменить на полноценный Leaflet
  const mapsUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;

  return (
    <div className="w-full h-64 rounded-xl overflow-hidden border border-white/10">
      <iframe
        src={mapsUrl}
        width="100%"
        height="100%"
        title={markerLabel}
        loading="lazy"
        referrerPolicy="no-referrer-when-downgrade"
        className="border-0"
      />
    </div>
  );
}

// Динамический импорт без SSR: плейсхолдер нейтрален к языку (без текста)
const MapEmbedClient = dynamic(() => Promise.resolve(LeafletMap), {
  ssr: false,
  loading: () => (
    <div className="w-full h-64 rounded-xl bg-white/5 border border-white/10 animate-pulse" />
  ),
});

export default function MapEmbed(props: MapEmbedProps) {
  if (!props.lat || !props.lng) return null;
  return <MapEmbedClient {...props} />;
}
