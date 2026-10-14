// Настройка единственного Axios-экземпляра для всех API-запросов
// Включает retry-логику для 5xx ошибок с экспоненциальной задержкой

import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios';

const RETRY_LIMIT = 3;
const RETRY_DELAY_BASE_MS = 500;

// Тип конфига с дополнительным счётчиком повторов
type RetryConfig = InternalAxiosRequestConfig & { _retryCount?: number };

// Вспомогательная функция: ждать ms миллисекунд
const sleep = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

// Создаём единственный экземпляр клиента
export const apiClient: AxiosInstance = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  timeout: 10_000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

// Интерцептор запросов: можно добавлять заголовки авторизации здесь при необходимости
apiClient.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    // Для будущего расширения: добавление токенов авторизации
    return config;
  },
  (error) => Promise.reject(error)
);

// Интерцептор ответов: централизованная обработка ошибок + retry для 5xx
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const config = error.config as RetryConfig | undefined;

    if (!config || !error.response) {
      return Promise.reject(error);
    }

    // Retry только для 5xx ошибок (серверные сбои)
    if (error.response.status >= 500 && error.response.status < 600) {
      config._retryCount = config._retryCount ?? 0;

      if (config._retryCount < RETRY_LIMIT) {
        config._retryCount += 1;
        // Экспоненциальная задержка: 500ms, 1000ms, 2000ms
        const delay = RETRY_DELAY_BASE_MS * Math.pow(2, config._retryCount - 1);
        await sleep(delay);
        return apiClient(config);
      }
    }

    return Promise.reject(error);
  }
);
