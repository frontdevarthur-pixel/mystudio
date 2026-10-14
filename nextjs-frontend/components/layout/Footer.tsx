import type { SiteSettings } from '@/types/api';

interface FooterProps {
  settings: SiteSettings;
}

export default function Footer({ settings }: FooterProps) {
  return (
    <footer className="bg-[#0a0818] border-t border-white/10 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Название */}
          <div>
            <h3 className="text-xl font-bold text-white mb-2">{settings.site_name}</h3>
            <p className="text-gray-400 text-sm">{settings.site_description}</p>
          </div>

          {/* Контакты */}
          <div>
            <h4 className="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Контакты</h4>
            <ul className="space-y-2 text-sm text-gray-400">
              {settings.phone && <li><a href={`tel:${settings.phone}`} className="hover:text-white transition-colors">{settings.phone}</a></li>}
              {settings.email && <li><a href={`mailto:${settings.email}`} className="hover:text-white transition-colors">{settings.email}</a></li>}
              {settings.address && <li className="whitespace-pre-line">{settings.address}</li>}
            </ul>
          </div>

          {/* Соцсети */}
          {settings.social_links?.length > 0 && (
            <div>
              <h4 className="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Соцсети</h4>
              <ul className="space-y-2">
                {settings.social_links.map((link, i) => (
                  <li key={i}>
                    <a
                      href={link.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-sm text-gray-400 hover:text-white transition-colors"
                    >
                      {link.label}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>

        <div className="mt-8 pt-8 border-t border-white/10 text-center text-xs text-gray-500">
          © {new Date().getFullYear()} {settings.site_name}. Все права защищены.
        </div>
      </div>
    </footer>
  );
}
