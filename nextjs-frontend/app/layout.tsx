import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';
import { ContactModalProvider } from '@/components/ui/ContactModal';

const inter = Inter({ subsets: ['latin', 'cyrillic'], variable: '--font-inter' });

export const metadata: Metadata = {
  title: {
    default: 'WebStudio',
    template: '%s | WebStudio',
  },
  description: 'Разработка сайтов и веб-приложений для малого и среднего бизнеса',
  robots: { index: true, follow: true },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ru" className={inter.variable}>
      <body className="bg-[#080b11] text-slate-200 antialiased">
        <ContactModalProvider>{children}</ContactModalProvider>
      </body>
    </html>
  );
}
