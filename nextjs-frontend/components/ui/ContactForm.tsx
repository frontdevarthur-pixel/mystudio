'use client';

import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import axios from 'axios';
import { motion } from 'framer-motion';
import { useReducedMotion } from '@/lib/hooks/useReducedMotion';
import { submitContact } from '@/lib/api/endpoints';
import type { ContactFormPayload, ApiError } from '@/types/api';

const schema = z.object({
  name: z.string().min(1, 'Введите ваше имя'),
  email: z.string().email('Введите корректный email'),
  phone: z
    .string()
    .regex(/^\+[1-9]\d{1,14}$/, 'Формат: +79001234567')
    .or(z.literal(''))
    .optional(),
  project_description: z.string().min(10, 'Опишите проект (минимум 10 символов)'),
});

type FormValues = z.infer<typeof schema>;

export default function ContactForm({ onSuccess }: { onSuccess?: () => void }) {
  const reducedMotion = useReducedMotion();
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');

  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors },
  } = useForm<FormValues>({ resolver: zodResolver(schema) });

  const onSubmit = async (values: FormValues) => {
    setStatus('loading');
    try {
      await submitContact(values as ContactFormPayload);
      setStatus('success');
      reset();
      if (onSuccess) setTimeout(onSuccess, 1500);
    } catch (err) {
      if (axios.isAxiosError(err) && err.response?.status === 422) {
        const apiError = err.response.data as ApiError;
        const validationErrors = apiError.data?.validation_errors ?? {};
        Object.entries(validationErrors).forEach(([field, messages]) => {
          setError(field as keyof FormValues, { message: messages[0] });
        });
        setStatus('idle');
      } else {
        setStatus('error');
      }
    }
  };

  const inputClass = 'w-full px-4 py-3.5 rounded-xl bg-[#0d1117] border border-white/8 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-accent/60 focus:ring-1 focus:ring-accent/30 transition-colors text-sm font-body';
  const errorClass = 'text-red-400 text-xs mt-1';

  return (
    <motion.div
      initial={reducedMotion ? false : { opacity: 0, x: -20 }}
      whileInView={{ opacity: 1, x: 0 }}
      viewport={{ once: true, margin: '-80px' }}
      transition={{ duration: 0.5 }}
    >
      {status === 'success' ? (
        <div className="p-6 rounded-2xl bg-green-500/10 border border-green-500/30 text-green-300 text-center">
          <div className="text-3xl mb-3">✓</div>
          <p className="font-semibold text-lg mb-1">Заявка отправлена!</p>
          <p className="text-sm text-green-400">Мы свяжемся с вами в ближайшее время.</p>
        </div>
      ) : (
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4" noValidate>
          <div>
            <input {...register('name')} placeholder="Ваше имя *" className={inputClass} />
            {errors.name && <p className={errorClass}>{errors.name.message}</p>}
          </div>
          <div>
            <input {...register('email')} type="email" placeholder="Email *" className={inputClass} />
            {errors.email && <p className={errorClass}>{errors.email.message}</p>}
          </div>
          <div>
            <input {...register('phone')} type="tel" placeholder="Телефон (необязательно)" className={inputClass} />
            {errors.phone && <p className={errorClass}>{errors.phone.message}</p>}
          </div>
          <div>
            <textarea
              {...register('project_description')}
              rows={4}
              placeholder="Опишите ваш проект *"
              className={`${inputClass} resize-none`}
            />
            {errors.project_description && <p className={errorClass}>{errors.project_description.message}</p>}
          </div>

          {status === 'error' && (
            <p className="text-red-400 text-sm">Произошла ошибка. Попробуйте позже или напишите нам напрямую.</p>
          )}

          <button
            type="submit"
            disabled={status === 'loading'}
            className="w-full py-4 rounded-xl btn-accent disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            {status === 'loading' ? 'Отправляем...' : 'Отправить заявку →'}
          </button>
        </form>
      )}
    </motion.div>
  );
}
