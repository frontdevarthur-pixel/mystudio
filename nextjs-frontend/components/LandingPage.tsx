import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import HeroSection from '@/components/sections/HeroSection';
import StatsSection from '@/components/sections/StatsSection';
import AboutSection from '@/components/sections/AboutSection';
import ServicesSection from '@/components/sections/ServicesSection';
import TechnologiesSection from '@/components/sections/TechnologiesSection';
import PortfolioSection from '@/components/sections/PortfolioSection';
import GeographySection from '@/components/sections/GeographySection';
import ContactSection from '@/components/sections/ContactSection';
import Marquee from '@/components/ui/Marquee';
import FloatingContacts from '@/components/ui/FloatingContacts';
import { buildOrganizationJsonLd, buildWebSiteJsonLd } from '@/lib/utils/jsonld';
import type { LandingPageData } from '@/types/api';
import type { Lang } from '@/lib/api/endpoints';

const MARQUEE_WORDS = [
  'White-label', 'NDA', 'React', 'Next.js', 'WordPress',
  'Outstaffing', 'Agency Partner', 'TypeScript', 'Headless CMS',
  'On-time Delivery', 'Full Stack', 'Confidential',
];

interface LandingPageProps {
  data: LandingPageData;
  lang: Lang;
}

export default function LandingPage({ data, lang }: LandingPageProps) {
  const orgJsonLd = buildOrganizationJsonLd(data.settings);
  const siteJsonLd = buildWebSiteJsonLd(data.settings);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(orgJsonLd) }} />
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(siteJsonLd) }} />

      <Header settings={data.settings} lang={lang} />

      <main className="pt-16">
        <HeroSection data={data.hero} settings={data.settings} />

        <div className="py-2 border-y border-white/5 bg-[#0d1117]">
          <Marquee items={MARQUEE_WORDS} direction="left" speed={50} />
        </div>

        <StatsSection stats={data.about.stats} siteName={data.settings.site_name} />
        <AboutSection data={data.about} team={data.team} settings={data.settings} />
        <ServicesSection services={data.services} settings={data.settings} />

        <div className="py-2 border-y border-white/5 bg-[#080b11]">
          <Marquee items={MARQUEE_WORDS} direction="right" speed={55} />
        </div>

        <TechnologiesSection data={data.technologies} settings={data.settings} />
        <PortfolioSection projects={data.portfolio} settings={data.settings} />
        <GeographySection data={data.geography} />
        <ContactSection settings={data.settings} />
      </main>

      <Footer settings={data.settings} />
      <FloatingContacts settings={data.settings} />
    </>
  );
}
