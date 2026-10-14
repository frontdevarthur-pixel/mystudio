// Типы API-ответов для headless WordPress темы веб-студии
// Все интерфейсы соответствуют структуре эндпоинтов /wp-json/studio/v1/*

// ─── Глобальные настройки ────────────────────────────────────────────────────

export interface SiteSettings {
  site_name: string;
  site_description: string;
  logo_url: string;
  favicon_url: string;
  email: string;
  phone: string;
  address: string;
  social_links: SocialLink[];
  seo: SeoMeta;
  map_coordinates: { lat: number; lng: number };
  office_marker_label: string;
}

export interface SocialLink {
  platform: 'telegram' | 'instagram' | 'linkedin' | 'github' | 'behance' | string;
  url: string;
  label: string;
}

export interface SeoMeta {
  title: string;
  description: string;
  og_image_url: string;
  og_type: string;
  twitter_card: 'summary' | 'summary_large_image';
}

// ─── Hero-секция ─────────────────────────────────────────────────────────────

export interface HeroSection {
  heading: string;
  subheading: string;
  cta_text: string;
  cta_anchor: string;
  background_type: 'video' | 'image' | 'gradient';
  background_video_url?: string;
  background_image_url?: string;
  background_gradient?: string;
}

// ─── Секция «О нас» ──────────────────────────────────────────────────────────

export interface AboutSection {
  title: string;
  history: string;
  mission: string;
  values: string[];
  stats: AboutStat[];
}

export interface AboutStat {
  label: string;
  value: number;
  suffix?: string;
}

// ─── Команда ─────────────────────────────────────────────────────────────────

export interface TeamMember {
  id: number;
  name: string;
  position: string;
  photo_url: string;
  photo_alt: string;
  sort_order: number;
}

// ─── Услуги ──────────────────────────────────────────────────────────────────

export interface Service {
  id: number;
  title: string;
  description: string;
  icon_url: string;
  icon_alt: string;
  sort_order: number;
  is_visible: boolean;
}

// ─── Технологии ──────────────────────────────────────────────────────────────

export type TechCategory = 'Frontend' | 'Backend' | 'CMS' | 'DevOps' | 'Mobile' | string;

export interface Technology {
  id: number;
  name: string;
  logo_url: string;
  logo_alt: string;
  category: TechCategory;
  sort_order: number;
  is_visible: boolean;
}

export interface TechnologiesSection {
  categories: TechCategory[];
  items: Technology[];
}

// ─── Портфолио ───────────────────────────────────────────────────────────────

export interface PortfolioProject {
  id: number;
  slug: string;
  title: string;
  short_description: string;
  full_description: string;
  screenshot_url: string;
  screenshot_alt: string;
  gallery: { url: string; alt: string }[];
  technologies: string[];
  project_url?: string;
  sort_order: number;
  is_visible: boolean;
  published_at: string;
}

// ─── География ───────────────────────────────────────────────────────────────

export interface GeographySection {
  title: string;
  description: string;
  countries: CountryData[];
}

export interface CountryData {
  id: number;
  country_code: string;
  country_name: string;
  projects_count: number;
  clients_count: number;
  tooltip_text: string;
}

// ─── Контакты (входящая заявка) ───────────────────────────────────────────────

export interface ContactFormPayload {
  name: string;
  email: string;
  phone: string;
  project_description: string;
}

export interface ContactFormResponse {
  success: boolean;
  message: string;
}

// ─── Общие API-обёртки ───────────────────────────────────────────────────────

export interface ApiError {
  code: string;
  message: string;
  data?: {
    status: number;
    validation_errors?: Record<string, string[]>;
  };
}

// ─── Агрегированный ответ для главной страницы ───────────────────────────────

export interface LandingPageData {
  settings: SiteSettings;
  hero: HeroSection;
  about: AboutSection;
  team: TeamMember[];
  services: Service[];
  technologies: TechnologiesSection;
  portfolio: PortfolioProject[];
  geography: GeographySection;
}
