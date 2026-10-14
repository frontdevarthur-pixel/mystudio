# План реализации: Headless WordPress тема веб-студии

## Обзор

Реализация headless-архитектуры: WordPress (PHP) как бэкенд с REST API и ACF, Next.js 14+ как фронтенд. Задачи расположены инкрементально — каждый шаг интегрируется в предыдущий, от инфраструктуры к UI и тестам.

Стек: WordPress + PHP + ACF / Next.js 14 + React 18 + TypeScript / Axios / Framer Motion / Tailwind CSS / Vitest + fast-check + RTL + MSW.

## Задачи

- [x] 1. Инициализация проекта и структуры директорий
  - Создать директорию WordPress-темы `wp-content/themes/studio-headless/` с файлами `style.css`, `index.php`, `functions.php`
  - Создать директорию Next.js-приложения `nextjs-frontend/` и инициализировать проект командой `npx create-next-app@latest` с флагами: TypeScript, Tailwind CSS, App Router, src-директория отключена
  - Установить зависимости Next.js: `axios`, `framer-motion`, `react-hook-form`, `@hookform/resolvers`, `zod`, `leaflet`, `@types/leaflet`
  - Установить dev-зависимости: `vitest`, `@vitest/coverage-v8`, `fast-check`, `@testing-library/react`, `@testing-library/jest-dom`, `msw`, `jsdom`
  - Создать файлы `nextjs-frontend/.env.example` и `nextjs-frontend/next.config.ts`
  - Создать директории: `app/`, `components/sections/`, `components/ui/`, `components/layout/`, `lib/api/`, `lib/hooks/`, `lib/utils/`, `types/`, `public/fallback/`
  - _Требования: 3.1, 3.10_

- [x] 2. TypeScript-интерфейсы и типы API
  - [x] 2.1 Создать файл `types/api.ts` со всеми интерфейсами API-ответов
    - Реализовать интерфейсы: `SiteSettings`, `SocialLink`, `SeoMeta`, `HeroSection`, `AboutSection`, `AboutStat`, `TeamMember`, `Service`, `Technology`, `TechCategory`, `TechnologiesSection`, `PortfolioProject`, `GeographySection`, `CountryData`, `ContactFormPayload`, `ContactFormResponse`, `ApiError`, `LandingPageData`
    - _Требования: 6.6_

- [x] 3. WordPress тема — бэкенд
  - [x] 3.1 Создать `style.css` (заголовок темы) и минимальный `index.php` с редиректом на Next.js
    - `index.php` должен выполнять `wp_redirect` на `NEXTJS_FRONTEND_URL`, если константа определена
    - _Требования: 1.1, 1.4_

  - [x] 3.2 Создать `includes/post-types.php` — регистрация кастомных типов записей (CPT)
    - Зарегистрировать CPT: `portfolio` (Портфолио), `technologies` (Технологии), `team` (Команда), `services` (Услуги), `geography` (География)
    - Каждый CPT: `show_in_rest => true`, `public => true`, поддержка `title`, `thumbnail`, `custom-fields`
    - Подключить через `add_action('init', ...)` в `functions.php`
    - _Требования: 1.2_

  - [x] 3.3 Создать `includes/acf-fields.php` — регистрация ACF-групп полей
    - Группа `Studio Settings` (ACF Options): поля `site_name`, `logo`, `email`, `phone`, `address`, `social_links` (repeater), `seo_title`, `seo_description`, `og_image`, `map_lat`, `map_lng`
    - Группа `Hero Section` (ACF Options): поля `heading`, `subheading`, `cta_text`, `cta_anchor`, `background_type` (select: video/image/gradient), `background_video`, `background_image`, `background_gradient`
    - Группа `About Section` (ACF Options): поля `about_title`, `history` (wysiwyg), `mission`, `values` (repeater), `stats` (repeater: label, value, suffix)
    - Группа `Portfolio CPT`: поля `short_description`, `full_description` (wysiwyg), `gallery` (gallery), `technologies` (repeater), `project_url`, `sort_order`, `is_visible`
    - Группа `Services CPT`: поля `description`, `icon` (image), `sort_order`, `is_visible`
    - Группа `Technologies CPT`: поля `logo` (image), `category` (select), `sort_order`, `is_visible`
    - Группа `Team CPT`: поля `position`, `sort_order`
    - Группа `Geography CPT`: поля `country_code`, `projects_count`, `clients_count`, `tooltip_text`
    - Зарегистрировать ACF Options Page для Studio Settings, Hero, About
    - _Требования: 1.3, 1.6, 5.1, 5.2_

  - [x] 3.4 Создать `includes/cors.php` — CORS-заголовки для REST API
    - Добавить `add_action('rest_api_init', ...)` для установки заголовков `Access-Control-Allow-Origin` (из константы `NEXTJS_FRONTEND_URL` или `wp-config.php`), `Access-Control-Allow-Methods: GET, POST, OPTIONS`, `Access-Control-Allow-Headers`
    - _Требования: 2.11_

  - [x] 3.5 Создать `includes/rest-api.php` — GET-эндпоинты `/wp-json/studio/v1/*`
    - Эндпоинт `GET /settings`: возвращает данные из ACF Options группы `Studio Settings`
    - Эндпоинт `GET /hero`: возвращает данные из ACF Options группы `Hero Section`
    - Эндпоинт `GET /about`: возвращает данные из ACF Options групп `About Section` + список команды из CPT `team` отсортированный по `sort_order`
    - Эндпоинт `GET /services`: возвращает CPT `services`, фильтрует `is_visible=true`, сортирует по `sort_order`
    - Эндпоинт `GET /portfolio`: возвращает CPT `portfolio`, фильтрует `is_visible=true`, сортирует по `sort_order`
    - Эндпоинт `GET /technologies`: возвращает CPT `technologies`, фильтрует `is_visible=true`, сортирует по `sort_order`, группирует по категориям
    - Эндпоинт `GET /team`: возвращает CPT `team`, сортирует по `sort_order`
    - Эндпоинт `GET /geography`: возвращает CPT `geography`
    - Все эндпоинты регистрируются через `register_rest_route('studio/v1', ...)`
    - _Требования: 2.1–2.8_

  - [x] 3.6 Создать `includes/contact-handler.php` — POST-эндпоинт `/wp-json/studio/v1/contact`
    - Регистрировать эндпоинт `POST /contact`
    - Валидировать: `name` (непустой), `email` (валидный формат), `phone` (опционально), `project_description` (непустой)
    - При ошибке валидации: возвращать HTTP 422 с `{ code, message, data: { validation_errors } }`
    - При успехе: сохранять заявку как CPT `contact_request` или в опциях, отправлять `wp_mail` администратору, возвращать `{ success: true, message }`
    - _Требования: 2.9, 2.10_

  - [x] 3.7 Создать `includes/isr-webhook.php` — WordPress-хук для ISR-регенерации
    - Реализовать `add_action('save_post', 'studio_trigger_isr_revalidate', 10, 2)`
    - Для CPT `portfolio` — отправлять пути `['/']` и `['/portfolio/{slug}']`
    - Для остальных CPT и ACF Options (`acf/save_post` при `$post_id === 'options'`) — отправлять путь `['/']`
    - Игнорировать автосохранения и ревизии
    - Использовать `wp_remote_post` с заголовком `x-revalidate-secret` из константы `NEXTJS_REVALIDATE_SECRET`
    - _Требования: 5.6_

  - [x] 3.8 Создать `includes/admin-settings.php` — страница настроек темы в WordPress Admin
    - Добавить страницу `Studio Theme Settings` в меню Admin через `add_menu_page`
    - Отображать ссылки на ACF Options страницы секций для быстрого доступа
    - Добавить поле для ввода `NEXTJS_FRONTEND_URL` и `NEXTJS_REVALIDATE_SECRET` с сохранением в `wp_options`
    - Добавить кнопку «Принудительно регенерировать сайт» для ручного вызова `studio_trigger_isr_revalidate`
    - _Требования: 1.6, 5.1_

  - [x] 3.9 Подключить все include-файлы в `functions.php`
    - `require_once` для: `post-types.php`, `acf-fields.php`, `rest-api.php`, `cors.php`, `isr-webhook.php`, `contact-handler.php`, `admin-settings.php`
    - Добавить `add_theme_support('post-thumbnails')` и `add_theme_support('title-tag')`
    - _Требования: 1.5_

- [ ] 4. Checkpoint — базовый WordPress бэкенд
  - Убедиться, что все эндпоинты `/wp-json/studio/v1/*` регистрируются без ошибок PHP
  - Убедиться, что CPT отображаются в Admin
  - Спросить пользователя, если возникнут вопросы

- [x] 5. Axios HTTP-клиент и API-функции
  - [x] 5.1 Создать `lib/api/axios-client.ts` — настройка Axios-экземпляра
    - Инициализировать `axios.create` с `baseURL: process.env.NEXT_PUBLIC_API_URL`, `timeout: 10_000`, заголовками `Content-Type: application/json`
    - Реализовать интерцептор запросов (добавление заголовков)
    - Реализовать интерцептор ответов с retry-логикой: при HTTP 5xx — до 3 повторных попыток с экспоненциальной задержкой (500ms, 1000ms, 2000ms), `_retryCount` на конфиге запроса
    - Экспортировать `apiClient`
    - _Требования: 6.1, 6.2, 6.3, 6.4, 6.5_

  - [ ]* 5.2 Написать property-тест для Axios-клиента — Content-Type заголовок
    - **Property 9: Axios-клиент добавляет Content-Type заголовок**
    - **Validates: Требование 6.3**
    - Использовать MSW для перехвата запросов, fast-check для генерации произвольных путей и тел запросов, проверить `Content-Type: application/json` в каждом запросе
    - Минимум 100 итераций

  - [ ]* 5.3 Написать property-тест для retry-логики Axios
    - **Property 10: Retry при 5xx ответе не более 3 раз**
    - **Validates: Требования 6.4, 6.5**
    - Использовать MSW для симуляции стабильного 5xx-ответа, fast-check для выбора произвольного 5xx-статуса (500–599), проверить что суммарное число попыток ≤ 4
    - Минимум 100 итераций

  - [x] 5.4 Создать `lib/api/endpoints.ts` — типизированные функции-обёртки
    - Реализовать: `fetchSettings`, `fetchHero`, `fetchAbout`, `fetchTeam`, `fetchServices`, `fetchTechnologies`, `fetchPortfolio`, `fetchGeography`, `submitContact`
    - Реализовать `fetchLandingPageData` с `Promise.all` для параллельной загрузки всех данных лендинга
    - Типизировать возвращаемые значения через интерфейсы из `types/api.ts`
    - _Требования: 3.4, 6.6_

- [ ] 6. Next.js конфигурация и базовая структура приложения
  - [x] 6.1 Настроить `next.config.ts`
    - Добавить `images.remotePatterns` для домена WordPress CMS
    - Включить `experimental.optimizePackageImports` для `framer-motion`
    - _Требования: 3.1, 4.5_

  - [x] 6.2 Создать `app/layout.tsx` — root layout с метаданными
    - Реализовать `generateMetadata()` async: загружать `fetchSettings()`, возвращать `Metadata` с `title.template`, `description`, `openGraph`, `twitter`, `robots`
    - Подключить шрифты через `next/font`, базовые провайдеры
    - _Требования: 4.1_

  - [ ]* 6.3 Написать property-тест для `generateMetadata`
    - **Property 3: generateMetadata содержит все обязательные поля**
    - **Validates: Требование 4.1**
    - Использовать fast-check для генерации произвольных `SiteSettings` с ненулевыми полями, MSW для мока `fetchSettings`, проверить наличие всех обязательных полей в результате
    - Минимум 100 итераций

  - [x] 6.4 Создать `app/api/revalidate/route.ts` — ISR webhook-обработчик
    - Реализовать `POST` handler: проверить `x-revalidate-secret`, вызвать `revalidatePath` для каждого пути из `body.paths` (дефолт `['/']`), вернуть `{ revalidated: true, paths }`
    - При неверном секрете: вернуть HTTP 401
    - _Требования: 3.2, 5.6_

  - [x] 6.5 Создать `app/robots.ts` — генератор `robots.txt`
    - Разрешить все для `User-agent: *`, указать `Sitemap` URL на основе `NEXT_PUBLIC_SITE_URL`
    - _Требования: 4.3_

  - [x] 6.6 Создать `app/sitemap.ts` — генератор `sitemap.xml`
    - Вызвать `fetchPortfolio()`, вернуть массив: главная страница (priority 1.0) + страницы проектов (`/portfolio/${slug}`, priority 0.7)
    - _Требования: 4.2_

  - [ ]* 6.7 Написать property-тест для `sitemap`
    - **Property 4: sitemap содержит запись для каждого проекта**
    - **Validates: Требование 4.2**
    - Использовать fast-check для генерации массивов `PortfolioProject[]` длиной ≥ 1, MSW для мока `fetchPortfolio`, проверить что для каждого проекта есть URL `${SITE_URL}/portfolio/${slug}`
    - Минимум 100 итераций

- [ ] 7. SEO-утилиты и JSON-LD
  - [x] 7.1 Создать `lib/utils/jsonld.ts` — генераторы JSON-LD схем
    - Реализовать `buildOrganizationJsonLd(settings: SiteSettings)`: возвращать объект с `@context: 'https://schema.org'`, `@type: 'Organization'`, `name`, `url`, `logo`, `contactPoint`, `sameAs`
    - Реализовать `buildWebSiteJsonLd(settings: SiteSettings)`
    - _Требования: 4.6_

  - [ ]* 7.2 Написать property-тест для `buildOrganizationJsonLd`
    - **Property 6: buildOrganizationJsonLd возвращает корректный JSON-LD**
    - **Validates: Требование 4.6**
    - Использовать fast-check для генерации произвольных `SiteSettings`, проверить наличие `@context`, `@type`, непустых `name`, `url`, `logo`, `contactPoint`
    - Минимум 100 итераций

  - [x] 7.3 Создать `lib/utils/seo.ts` — утилиты для метаданных страниц
    - Функция `buildPageMetadata(title, description, ogImage)` → `Metadata`
    - _Требования: 4.1_

- [-] 8. Хуки и утилиты
  - [x] 8.1 Создать `lib/hooks/useReducedMotion.ts`
    - Использовать `window.matchMedia('(prefers-reduced-motion: reduce)')`, подписаться на изменения через `addEventListener('change', ...)`
    - Возвращать `boolean`
    - _Требования: 14.4_

  - [x] 8.2 Создать `lib/hooks/useScrollAnimation.ts`
    - Обёртка над Framer Motion `useInView` с настройками `{ once: true, margin: '-80px' }`
    - Принимать `reducedMotion: boolean` и возвращать пустые варианты если `true`
    - _Требования: 14.1, 14.4_

  - [x] 8.3 Создать утилиту `lib/utils/sort-filter.ts`
    - Функция `sortBySortOrder<T extends { sort_order: number }>(items: T[]): T[]` — сортировка по возрастанию
    - Функция `filterVisible<T extends { is_visible: boolean }>(items: T[]): T[]` — фильтрация по `is_visible === true`
    - Функция `groupByCategory<T extends { category: string }>(items: T[]): Record<string, T[]>` — группировка по категории
    - _Требования: 5.4, 5.5, 10.3_

  - [ ]* 8.4 Написать property-тест для сортировки по sort_order
    - **Property 7: Сортировка элементов по sort_order**
    - **Validates: Требование 5.4**
    - Использовать fast-check для генерации массивов с произвольными `sort_order`, проверить что результат отсортирован по возрастанию
    - Минимум 100 итераций

  - [ ]* 8.5 Написать property-тест для фильтрации невидимых элементов
    - **Property 8: Невидимые элементы исключаются из рендера**
    - **Validates: Требование 5.5**
    - Использовать fast-check для генерации массивов с произвольными `is_visible`, проверить что результат не содержит элементов с `is_visible === false`
    - Минимум 100 итераций

  - [ ]* 8.6 Написать property-тест для группировки технологий
    - **Property 12: Группировка технологий по категориям**
    - **Validates: Требование 10.3**
    - Использовать fast-check для генерации массивов `Technology[]` с произвольными категориями, проверить 3 условия: количество групп = количество уникальных категорий, каждый элемент в правильной группе, нет пустых групп
    - Минимум 100 итераций

- [ ] 9. Checkpoint — утилиты и хуки
  - Запустить `vitest --run` для проверки всех написанных тестов
  - Убедиться, что типы компилируются без ошибок
  - Спросить пользователя, если возникнут вопросы

- [ ] 10. Компоненты Layout — Header и Footer
  - [ ] 10.1 Создать `components/layout/Header.tsx`
    - Отображать `logo_url` (через `next/image`) и `site_name` из `SiteSettings`
    - Навигация: якорные ссылки на секции лендинга
    - Адаптивное меню (бургер на мобильном)
    - _Требования: 3.9_

  - [ ] 10.2 Создать `components/layout/Footer.tsx`
    - Отображать `address`, `phone`, `email`, ссылки `social_links` из `SiteSettings`
    - _Требования: 13.7_

- [ ] 11. Hero секция
  - [ ] 11.1 Создать `components/sections/HeroSection.tsx`
    - Props: `data: HeroSection`
    - Отображать `heading`, `subheading`, CTA-кнопку с текстом `cta_text`, при клике плавный скролл к `cta_anchor` (`window.scrollTo` или `element.scrollIntoView`)
    - Фоновый элемент: условный рендер по `background_type` (video/image/gradient)
    - Анимация появления: Framer Motion `motion.div` с `initial={{ opacity: 0, y: 30 }}`, `animate={{ opacity: 1, y: 0 }}`, `transition={{ duration: 0.5, delay: 0 }}`, при `useReducedMotion` — без анимации
    - Адаптивность: корректный вид на 320px, 768px, 1024px+
    - _Требования: 7.1, 7.2, 7.3, 7.4, 7.5_

  - [ ]* 11.2 Написать property-тест для HeroSection
    - **Property 11 (частично): HeroSection рендерит обязательные поля**
    - **Validates: Требования 7.1, 11 (heading, subheading, cta_text)**
    - Использовать fast-check для генерации `HeroSection` с непустыми `heading`, `subheading`, `cta_text`, RTL для рендера, проверить наличие в DOM
    - Минимум 100 итераций

- [ ] 12. Секция «О нас» и AnimatedCounter
  - [ ] 12.1 Создать `components/ui/AnimatedCounter.tsx`
    - Props: `value: number`, `suffix?: string`
    - Анимация count-up при входе в viewport через Framer Motion `useMotionValue` + `animate`, при `useReducedMotion` — отображать итоговое значение без анимации
    - _Требования: 8.4_

  - [ ] 12.2 Создать `components/ui/TeamCard.tsx`
    - Props: `member: TeamMember`
    - Отображать `next/image` с `src=photo_url`, `alt=photo_alt`, имя `name`, должность `position`
    - _Требования: 8.2, 4.5_

  - [ ] 12.3 Создать `components/sections/AboutSection.tsx`
    - Props: `data: AboutSection`, `team: TeamMember[]`
    - Отображать `title`, `history` (dangerouslySetInnerHTML для HTML), `mission`, список `values`
    - Отображать карточки `TeamCard` в адаптивной сетке
    - Stagger-анимация карточек: Framer Motion `variants` с `transition.delay: index * 0.1`, `whileInView`, `viewport={{ once: true }}`
    - Счётчики `AboutStat` через `AnimatedCounter`, триггер — viewport
    - _Требования: 8.1, 8.2, 8.3, 8.4_

  - [ ]* 12.4 Написать property-тест для AboutSection
    - **Property 11 (частично): AboutSection рендерит обязательные поля**
    - **Validates: Требования 8.1, 8.2 (title, mission, name, position)**
    - fast-check генерирует `AboutSection` и `TeamMember[]`, RTL проверяет DOM
    - Минимум 100 итераций

- [ ] 13. Секция «Что мы делаем» (Услуги)
  - [ ] 13.1 Создать `components/ui/ServiceCard.tsx`
    - Props: `service: Service`
    - Отображать `next/image` с `src=icon_url`, `alt=icon_alt`, `title`, `description`
    - Hover-анимация: Framer Motion `whileHover={{ scale: 1.05 }}`, при `useReducedMotion` — отключить
    - _Требования: 9.1, 9.2, 4.5_

  - [ ] 13.2 Создать `components/sections/ServicesSection.tsx`
    - Props: `services: Service[]`
    - Применить `filterVisible` и `sortBySortOrder` из `lib/utils/sort-filter.ts`
    - Адаптивная сетка: 3 колонки (lg), 2 колонки (md), 1 колонка (sm)
    - Stagger scroll-triggered анимация карточек через Framer Motion `whileInView`
    - _Требования: 9.1, 9.3_

  - [ ]* 13.3 Написать property-тест для ServicesSection
    - **Property 11 (частично): ServicesSection рендерит обязательные поля**
    - **Validates: Требования 9.1 (title, description)**
    - fast-check генерирует массив `Service[]` с `is_visible: true`, RTL проверяет DOM
    - Минимум 100 итераций

- [ ] 14. Секция «Технологии»
  - [ ] 14.1 Создать `components/ui/TechCard.tsx`
    - Props: `tech: Technology`
    - Отображать `next/image` с `src=logo_url`, `alt=logo_alt`, `name`
    - _Требования: 10.1, 4.5_

  - [ ] 14.2 Создать `components/sections/TechnologiesSection.tsx`
    - Props: `data: TechnologiesSection`
    - Применить `filterVisible` и `sortBySortOrder`, сгруппировать через `groupByCategory`
    - Отображать вкладки/секции по категориям, в каждой — карточки `TechCard`
    - Fade-in анимация логотипов через Framer Motion `whileInView`
    - _Требования: 10.1, 10.2, 10.3_

- [ ] 15. Портфолио — карусель
  - [ ] 15.1 Создать `components/ui/Modal.tsx`
    - Props: `isOpen: boolean`, `onClose: () => void`, `children: React.ReactNode`
    - Закрытие по ESC и клику вне содержимого, focus trap
    - Анимация через Framer Motion `AnimatePresence`
    - _Требования: 11.5_

  - [ ] 15.2 Создать `components/ui/Carousel.tsx`
    - Props: `projects: PortfolioProject[]`
    - Навигация кнопками prev/next со slide-анимацией (Framer Motion `AnimatePresence` + `motion.div`)
    - Touch/swipe через Framer Motion `drag="x"` и `dragConstraints`
    - Автопрокрутка 5 секунд, останавливается при `onMouseEnter`/`onFocus`, возобновляется при `onMouseLeave`/`onBlur`
    - Отображать: `next/image` (screenshot), `title`, `short_description`, список `technologies`, `project_url`
    - При клике на проект — открывать `Modal` с деталями
    - _Требования: 11.1, 11.2, 11.3, 11.4, 11.5, 11.6_

  - [ ] 15.3 Создать `components/sections/PortfolioSection.tsx`
    - Props: `projects: PortfolioProject[]`
    - Применить `filterVisible` и `sortBySortOrder`
    - Обернуть `Carousel` в Framer Motion scroll-trigger
    - _Требования: 11.1_

  - [ ]* 15.4 Написать property-тест для PortfolioSection
    - **Property 11 (частично): PortfolioSection рендерит обязательные поля**
    - **Validates: Требования 11.4 (title, short_description)**
    - fast-check генерирует массив `PortfolioProject[]`, RTL проверяет DOM первого видимого слайда
    - Минимум 100 итераций

- [ ] 16. Секция «География»
  - [ ] 16.1 Создать `components/ui/MapEmbed.tsx`
    - Динамический импорт Leaflet через `next/dynamic` (SSR: false) для карты офиса
    - Props: `lat: number`, `lng: number`, `markerLabel: string`
    - _Требования: 13.5_

  - [ ] 16.2 Создать `components/sections/GeographySection.tsx`
    - Props: `data: GeographySection`
    - Отображать `title`, `description`, список стран `countries` с `country_name`, `projects_count`, `clients_count`
    - Tooltip при hover/click на страну (Framer Motion `AnimatePresence` для появления)
    - Fade-in анимация при входе в viewport
    - _Требования: 12.1, 12.2, 12.3_

  - [ ]* 16.3 Написать property-тест для GeographySection
    - **Property 11 (частично): GeographySection рендерит обязательные поля**
    - **Validates: Требования 12.2 (country_name, projects_count)**
    - fast-check генерирует `GeographySection`, RTL проверяет DOM
    - Минимум 100 итераций

- [ ] 17. Секция контактов
  - [ ] 17.1 Создать `components/ui/ContactForm.tsx`
    - Поля: `name`, `email`, `phone`, `project_description`
    - Валидация через `react-hook-form` + `zod`-схема: `email` по RFC 5322 (`z.string().email()`), `phone` по E.164 (`z.string().regex(/^\+[1-9]\d{1,14}$/)`), `name` и `project_description` — непустые
    - При отправке: вызвать `submitContact(payload)` через `apiClient`
    - При успехе (HTTP 2xx): показать сообщение, очистить форму через `reset()`
    - При HTTP 422: парсить `validation_errors` из `ApiError`, устанавливать ошибки через `setError` у соответствующих полей
    - При других ошибках: показать общее сообщение об ошибке
    - Slide-in анимация формы через Framer Motion `whileInView`
    - _Требования: 13.1, 13.2, 13.3, 13.4, 13.6_

  - [ ]* 17.2 Написать property-тест для валидации email
    - **Property 14: Клиентская валидация email отклоняет невалидные адреса**
    - **Validates: Требование 13.6**
    - Использовать fast-check для генерации строк, не соответствующих RFC 5322 (пустые, без `@`, с пробелами, спецсимволами), проверить что zod-схема возвращает ошибку
    - Минимум 100 итераций

  - [ ]* 17.3 Написать property-тест для отображения ошибок валидации API
    - **Property 13: Ошибки валидации API отображаются у соответствующих полей**
    - **Validates: Требование 13.4**
    - fast-check генерирует объект `validation_errors` с произвольными именами полей формы и массивами сообщений, MSW симулирует HTTP 422-ответ, RTL проверяет что первое сообщение из каждого массива отображается рядом с соответствующим полем
    - Минимум 100 итераций

  - [ ]* 17.4 Написать property-тест для ContactForm при успешной отправке
    - **Property 1: Успешная отправка контактной формы**
    - **Validates: Требование 2.9**
    - fast-check генерирует валидные `ContactFormPayload`, MSW симулирует успешный ответ `{ success: true }`, RTL проверяет отображение сообщения об успехе и очистку формы
    - Минимум 100 итераций

  - [ ]* 17.5 Написать property-тест для ContactForm при HTTP 422
    - **Property 2: Невалидные данные формы возвращают HTTP 422**
    - **Validates: Требование 2.10**
    - fast-check генерирует невалидные `ContactFormPayload` (пустой name или невалидный email), MSW симулирует HTTP 422-ответ с `validation_errors`, проверить что статус 422 обрабатывается корректно
    - Минимум 100 итераций

  - [ ] 17.6 Создать `components/sections/ContactSection.tsx`
    - Props: `settings: SiteSettings`
    - Отображать `address`, `phone`, `email`, `social_links` из settings
    - Встроить `ContactForm` и `MapEmbed` (с координатами из `settings.map_coordinates`)
    - _Требования: 13.1, 13.7_

  - [ ]* 17.7 Написать property-тест для ContactSection
    - **Property 11 (частично): ContactSection рендерит контактные данные**
    - **Validates: Требование 13.7 (phone, email, address)**
    - fast-check генерирует `SiteSettings`, RTL проверяет DOM
    - Минимум 100 итераций

- [ ] 18. Кастомный курсор
  - [ ] 18.1 Создать `components/ui/CustomCursor.tsx`
    - Отслеживать `mousemove` события, отображать кастомный курсор только на устройствах с указателем (`@media (pointer: fine)`)
    - Анимация через Framer Motion `useSpring` для плавного следования за мышью
    - При `useReducedMotion === true` — скрыть кастомный курсор
    - _Требования: 14.6_

- [ ] 19. Главная страница лендинга (SSG + ISR)
  - [x] 19.1 Создать `app/page.tsx` — главная страница
    - `export const revalidate = 3600`
    - `try { data = await fetchLandingPageData() } catch { return <FallbackPage /> }`
    - Отрендерить последовательно: `Header`, `HeroSection`, `AboutSection`, `ServicesSection`, `TechnologiesSection`, `PortfolioSection`, `GeographySection`, `ContactSection`, `Footer`
    - Встроить JSON-LD скрипты: `buildOrganizationJsonLd` и `buildWebSiteJsonLd` через `<script type="application/ld+json">`
    - _Требования: 3.2, 3.7, 4.4, 4.6_

  - [x] 19.2 Создать `public/fallback/` — статичный fallback-контент
    - Файл `public/fallback/landing.json` с минимальными статичными данными для отображения при недоступности API
    - Компонент `FallbackPage` в `components/ui/FallbackPage.tsx` с сообщением об ошибке и статичными данными
    - _Требования: 3.8_

  - [ ]* 19.3 Написать property-тест для alt-атрибутов изображений
    - **Property 5: alt-атрибуты изображений соответствуют данным API**
    - **Validates: Требование 4.5**
    - fast-check генерирует произвольные `photo_alt`, `logo_alt`, `screenshot_alt` значения для TeamMember, Technology, PortfolioProject, RTL проверяет что атрибуты `alt` в DOM совпадают с переданными данными
    - Минимум 100 итераций

  - [ ]* 19.4 Написать комплексный property-тест для рендера всех секций
    - **Property 11: Компоненты секций рендерят все обязательные поля из API**
    - **Validates: Требования 7.1, 8.1, 8.2, 9.1, 10.1, 11.4, 12.2, 13.7**
    - fast-check генерирует полный объект `LandingPageData`, MSW мокирует все эндпоинты, RTL рендерит главную страницу, проверить наличие всех обязательных текстовых полей в DOM
    - Минимум 100 итераций

- [ ] 20. Страница проекта портфолио (ISR)
  - [x] 20.1 Создать `app/portfolio/[slug]/page.tsx`
    - `export const revalidate = 1800`
    - `generateStaticParams`: вызвать `fetchPortfolio()`, вернуть массив `{ slug: p.slug }`
    - `generateMetadata`: метаданные проекта (title, description, og_image из screenshot_url)
    - Отображать: галерею изображений, `full_description`, список технологий, ссылку `project_url`
    - _Требования: 3.2, 3.3, 4.1_

- [ ] 21. Анимации — prefers-reduced-motion
  - [ ] 21.1 Убедиться что все компоненты с анимациями используют `useReducedMotion`
    - Проверить все `motion.*` компоненты: при `reducedMotion === true` передавать пустые `variants: {}` или `transition: { duration: 0 }`
    - Применить к: HeroSection, AboutSection, ServicesSection, TechnologiesSection, PortfolioSection, GeographySection, ContactSection, Carousel, CustomCursor
    - _Требования: 14.4_

  - [ ]* 21.2 Написать property-тест для prefers-reduced-motion
    - **Property 15: prefers-reduced-motion отключает анимации**
    - **Validates: Требование 14.4**
    - Мокировать `useReducedMotion` возвращающий `true`, fast-check для выбора произвольного компонента секции, RTL рендерит компонент, проверить что передаваемые `variants` пусты или `transition.duration === 0`
    - Минимум 100 итераций

- [ ] 22. Checkpoint — все компоненты готовы
  - Запустить `vitest --run` и убедиться что все тесты проходят
  - Запустить `tsc --noEmit` для проверки типов
  - Спросить пользователя, если возникнут вопросы

- [ ] 23. Демо-данные и импорт
  - [x] 23.1 Создать `wp-content/themes/studio-headless/demo-data/demo-content.json`
    - Заполнить JSON с демо-данными всех секций из схемы в design.md: settings, hero, about, services (6 карточек), technologies (6 позиций), portfolio (2 проекта), geography (3 страны)
    - _Требования: 15.1_

  - [x] 23.2 Создать `wp-content/themes/studio-headless/demo-data/demo-import.php`
    - WP-CLI команда `wp eval-file demo-import.php` для импорта данных из `demo-content.json`
    - Создание записей CPT через `wp_insert_post` и `update_post_meta` / `update_field` (ACF)
    - Установка ACF Options через `update_field` на странице `'options'`
    - Идемпотентность: проверять существование записи по slug перед созданием
    - _Требования: 15.1_

- [ ] 24. Документация
  - [x] 24.1 Создать `wp-content/themes/studio-headless/README.md`
    - Раздел «Требования»: PHP 8.1+, WordPress 6.4+, ACF Pro, WP-CLI
    - Раздел «Установка WordPress-бэкенда»: шаги активации темы, установки ACF, настройки `wp-config.php` (константы `NEXTJS_FRONTEND_URL`, `NEXTJS_REVALIDATE_SECRET`), настройки CORS
    - Раздел «Импорт демо-данных»: команда `wp eval-file demo-data/demo-import.php`
    - Раздел «Запуск Next.js-фронтенда»: копирование `.env.example`, заполнение переменных, `npm install`, `npm run build`, `npm start`
    - Раздел «Настройка ISR webhook»: объяснение механизма, переменные окружения
    - Раздел «API эндпоинты»: таблица всех эндпоинтов
    - _Требования: 15.2, 15.3, 15.5_

  - [x] 24.2 Создать `nextjs-frontend/.env.example`
    - Переменные: `NEXT_PUBLIC_API_URL`, `NEXT_PUBLIC_SITE_URL`, `REVALIDATE_SECRET`, `NEXT_PUBLIC_GOOGLE_MAPS_API_KEY`
    - Комментарии на русском для каждой переменной
    - _Требования: 15.4_

- [ ] 25. Финальный checkpoint
  - Запустить `vitest --run --coverage` и убедиться что все 15 property-тестов проходят
  - Запустить `tsc --noEmit` для финальной проверки типов
  - Проверить что все задачи выполнены и интегрированы
  - Спросить пользователя, если возникнут вопросы

## Примечания

- Задачи с `*` — опциональные (тесты), могут быть пропущены для ускоренного MVP
- Каждая задача ссылается на конкретные требования для трассируемости
- Property-тесты используют fast-check с минимум 100 итерациями
- Все комментарии в PHP-коде — на русском языке (Требование 15.6)
- Для карты используется Leaflet.js (через `next/dynamic` без SSR), Google Maps как опция
- Axios — единственный HTTP-клиент, `fetch` не используется нигде в фронтенде
