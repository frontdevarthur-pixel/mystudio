/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    // Разрешённые домены для next/image — добавить домен WordPress CMS
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'getwatched.evacuator-dnepr.dp.ua',
        port: '',
        pathname: '/wp-content/uploads/**',
      },
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '',
        pathname: '/wp-content/uploads/**',
      },
    ],
  },
  experimental: {
    // Оптимизация импортов framer-motion для уменьшения bundle size
    optimizePackageImports: ['framer-motion'],
  },
};

export default nextConfig;
