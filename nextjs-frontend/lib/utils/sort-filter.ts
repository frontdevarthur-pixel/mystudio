// Утилиты для сортировки, фильтрации и группировки данных API

/**
 * Сортирует массив элементов по возрастанию поля sort_order
 */
export function sortBySortOrder<T extends { sort_order: number }>(items: T[]): T[] {
  return [...items].sort((a, b) => a.sort_order - b.sort_order);
}

/**
 * Фильтрует массив, оставляя только видимые элементы (is_visible === true)
 */
export function filterVisible<T extends { is_visible: boolean }>(items: T[]): T[] {
  return items.filter(item => item.is_visible === true);
}

/**
 * Группирует массив элементов по полю category
 */
export function groupByCategory<T extends { category: string }>(
  items: T[]
): Record<string, T[]> {
  return items.reduce<Record<string, T[]>>((acc, item) => {
    const key = item.category;
    if (!acc[key]) {
      acc[key] = [];
    }
    acc[key].push(item);
    return acc;
  }, {});
}
