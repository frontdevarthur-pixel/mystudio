// Типизированные функции-обёртки для всех API-эндпоинтов
// Каждая функция принимает lang ('uk' | 'en') и передаёт его как query параметр

import { apiClient } from '@/lib/api/axios-client';
import type {
  SiteSettings,
  HeroSection,
  AboutSection,
  TeamMember,
  Service,
  TechnologiesSection,
  PortfolioProject,
  GeographySection,
  ContactFormPayload,
  ContactFormResponse,
  LandingPageData,
  SectionLabels,
} from '@/types/api';

export type Lang = 'uk' | 'en';

const langParam = (lang: Lang) => ({ params: { lang } });

export const fetchSettings = (lang: Lang = 'uk'): Promise<SiteSettings> =>
  apiClient.get<SiteSettings>('/settings', langParam(lang)).then(r => r.data);

export const fetchHero = (lang: Lang = 'uk'): Promise<HeroSection> =>
  apiClient.get<HeroSection>('/hero', langParam(lang)).then(r => r.data);

export const fetchAbout = (lang: Lang = 'uk'): Promise<AboutSection> =>
  apiClient.get<AboutSection>('/about', langParam(lang)).then(r => r.data);

export const fetchTeam = (lang: Lang = 'uk'): Promise<TeamMember[]> =>
  apiClient.get<TeamMember[]>('/team', langParam(lang)).then(r => r.data);

export const fetchServices = (lang: Lang = 'uk'): Promise<Service[]> =>
  apiClient.get<Service[]>('/services', langParam(lang)).then(r => r.data);

export const fetchTechnologies = (lang: Lang = 'uk'): Promise<TechnologiesSection> =>
  apiClient.get<TechnologiesSection>('/technologies', langParam(lang)).then(r => r.data);

export const fetchPortfolio = (lang: Lang = 'uk'): Promise<PortfolioProject[]> =>
  apiClient.get<PortfolioProject[]>('/portfolio', langParam(lang)).then(r => r.data);

export const fetchGeography = (lang: Lang = 'uk'): Promise<GeographySection> =>
  apiClient.get<GeographySection>('/geography', langParam(lang)).then(r => r.data);

export const fetchLabels = (lang: Lang = 'uk'): Promise<SectionLabels> =>
  apiClient.get<SectionLabels>('/labels', langParam(lang)).then(r => r.data);

export const submitContact = (payload: ContactFormPayload): Promise<ContactFormResponse> =>
  apiClient.post<ContactFormResponse>('/contact', payload).then(r => r.data);

export const fetchLandingPageData = async (lang: Lang = 'uk'): Promise<LandingPageData> => {
  const [settings, hero, about, team, services, technologies, portfolio, geography, labels] =
    await Promise.all([
      fetchSettings(lang),
      fetchHero(lang),
      fetchAbout(lang),
      fetchTeam(lang),
      fetchServices(lang),
      fetchTechnologies(lang),
      fetchPortfolio(lang),
      fetchGeography(lang),
      fetchLabels(lang),
    ]);

  return { settings, hero, about, team, services, technologies, portfolio, geography, labels };
};
