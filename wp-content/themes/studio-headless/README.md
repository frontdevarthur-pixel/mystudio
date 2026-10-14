# Studio Headless — WordPress тема для веб-студии

Headless WordPress тема + Next.js 14 лендинг для агентства веб-разработки.

## Требования

- PHP 8.1+
- WordPress 6.4+
- ACF Pro (Advanced Custom Fields)
- WP-CLI (для импорта демо-данных)
- Node.js 20+

---

## Установка WordPress-бэкенда

### 1. Активация темы

```bash
# Скопируйте папку темы в wp-content/themes/
# Активируйте через WordPress Admin → Внешний вид → Темы
# или через WP-CLI:
wp theme activate studio-headless
```

### 2. Установка ACF Pro

Установите и активируйте плагин [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/).

### 3. Настройка wp-config.php

Добавьте в `wp-config.php` перед строкой `/* That's all, stop editing! */`:

```php
// URL Next.js-фронтенда (без слеша в конце)
define('NEXTJS_FRONTEND_URL', 'https://yourdomain.ru');

// Секрет для ISR-регенерации (сгенерируйте: openssl rand -hex 32)
define('NEXTJS_REVALIDATE_SECRET', 'your-secret-here');
```

Или настройте через **WordPress Admin → Studio Theme → Настройки подключения**.

### 4. Настройка CORS

В `wp-config.php` задайте `NEXTJS_FRONTEND_URL` — это автоматически настроит CORS-заголовки для REST API.

### 5. Импорт демо-данных

```bash
wp eval-file wp-content/themes/studio-headless/demo-data/demo-import.php
```

---

## Запуск Next.js-фронтенда

### 1. Настройка переменных окружения

```bash
cp nextjs-frontend/.env.example nextjs-frontend/.env.local
```

Заполните `.env.local`:

```env
NEXT_PUBLIC_API_URL=https://your-wp-domain.ru/wp-json/studio/v1
NEXT_PUBLIC_SITE_URL=https://yourdomain.ru
REVALIDATE_SECRET=your-secret-here  # Должен совпадать с NEXTJS_REVALIDATE_SECRET
```

### 2. Установка зависимостей и запуск

```bash
npm install
npm run build
npm start
```

Для разработки:
```bash
npm run dev
```

---

## Настройка ISR Webhook

При сохранении любого контента в WordPress Admin автоматически отправляется запрос на `/api/revalidate` Next.js-приложения, что вызывает немедленную перестройку страниц.

**Как работает:**
1. Контент-менеджер сохраняет изменения в WordPress Admin
2. WordPress отправляет POST на `NEXTJS_FRONTEND_URL/api/revalidate` с заголовком `x-revalidate-secret`
3. Next.js проверяет секрет и вызывает `revalidatePath('/')`
4. Страница перестраивается в течение нескольких секунд

**Принудительная регенерация:**  
WordPress Admin → Studio Theme → Принудительно регенерировать сайт

---

## Управление контентом

Весь контент управляется через WordPress Admin:

| Раздел | Описание |
|--------|----------|
| Studio Theme → Studio Settings | Название, логотип, контакты, SEO |
| Studio Theme → Hero Section | Заголовок, подзаголовок, фон |
| Studio Theme → About Section | О нас, миссия, статистика |
| Портфолио | Проекты студии |
| Услуги | Карточки услуг |
| Технологии | Стек технологий |
| Команда | Сотрудники |
| География | Страны присутствия |
| Заявки | Входящие заявки с формы |

---

## API эндпоинты

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/wp-json/studio/v1/settings` | Глобальные настройки |
| GET | `/wp-json/studio/v1/hero` | Hero-секция |
| GET | `/wp-json/studio/v1/about` | О нас + команда |
| GET | `/wp-json/studio/v1/services` | Услуги |
| GET | `/wp-json/studio/v1/portfolio` | Портфолио |
| GET | `/wp-json/studio/v1/technologies` | Технологии |
| GET | `/wp-json/studio/v1/team` | Команда |
| GET | `/wp-json/studio/v1/geography` | География |
| POST | `/wp-json/studio/v1/contact` | Отправка заявки |

---

## Структура проекта

```
├── wp-content/themes/studio-headless/   # WordPress-тема (бэкенд)
│   ├── functions.php                     # Главный файл темы
│   ├── includes/
│   │   ├── post-types.php               # Кастомные типы записей
│   │   ├── acf-fields.php               # ACF-поля
│   │   ├── rest-api.php                 # REST API эндпоинты
│   │   ├── cors.php                     # CORS настройки
│   │   ├── isr-webhook.php              # ISR-регенерация
│   │   ├── contact-handler.php          # Обработчик заявок
│   │   └── admin-settings.php           # Страница настроек Admin
│   └── demo-data/                       # Демо-данные
│
└── nextjs-frontend/                      # Next.js-приложение (фронтенд)
    ├── app/                              # App Router
    ├── components/                       # React-компоненты
    │   ├── layout/                       # Header, Footer
    │   ├── sections/                     # Секции лендинга
    │   └── ui/                           # UI-компоненты
    ├── lib/
    │   ├── api/                          # Axios-клиент + эндпоинты
    │   ├── hooks/                        # React-хуки
    │   └── utils/                        # Утилиты
    └── types/                            # TypeScript типы
```
