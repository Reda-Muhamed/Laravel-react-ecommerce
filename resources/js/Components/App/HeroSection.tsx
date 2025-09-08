/* eslint-disable prettier/prettier */
import { Department, PageProps } from '@/types';
import { usePage } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import PrimaryButton from '../Core/PrimaryButton';

export default function HeroSection() {
  const { sharedDepartments } = usePage<PageProps>().props;
  const sharedDepartments1: Department[] = sharedDepartments as Department[];
  const [current, setCurrent] = useState(0);

  // Auto-slide every 10 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrent((prev) => (prev + 1) % sharedDepartments1.length);
    }, 10000);

    return () => clearInterval(interval);
  }, [sharedDepartments1.length]);

  const nextSlide = () =>
    setCurrent((prev) => (prev + 1) % sharedDepartments1.length);
  const prevSlide = () =>
    setCurrent((prev) =>
      prev === 0 ? sharedDepartments1.length - 1 : prev - 1
    );

  return (
    <section className="relative w-full h-[80vh] overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-950">
      <AnimatePresence mode="wait">
        {sharedDepartments1.map(
          (dep, index) =>
            index === current && (
              <motion.div
                key={dep.id}
                initial={{ opacity: 0, scale: 1.1, filter: 'blur(5px)' }}
                animate={{ opacity: 1, scale: 1, filter: 'blur(0px)' }}
                exit={{ opacity: 0, scale: 0.9, filter: 'blur(5px)' }}
                transition={{ duration: 1.2, ease: 'easeInOut' }}
                className="absolute inset-0"
              >
                <img
                  src={dep.image}
                  alt={dep.name}
                  className="w-full h-full object-cover brightness-50"
                  loading="lazy"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-center items-center text-center p-6">
                  <motion.h1
                    initial={{ y: 50, opacity: 0, rotateX: 90 }}
                    animate={{ y: 0, opacity: 1, rotateX: 0 }}
                    transition={{ delay: 0.3, duration: 1, ease: 'backOut' }}
                    className="text-4xl md:text-7xl font-extrabold text-white drop-shadow-2xl tracking-wider bg-gradient-to-r from-indigo-300 to-purple-300 bg-clip-text text-transparent"
                  >
                    {dep.name}
                  </motion.h1>
                  <motion.p
                    initial={{ y: 50, opacity: 0 }}
                    animate={{ y: 0, opacity: 1 }}
                    transition={{ delay: 0.5, duration: 0.9 }}
                    className="mt-6 text-lg md:text-2xl text-gray-100 max-w-3xl leading-relaxed"
                  >
                    Dive into our curated collection of {dep.name.toLowerCase()} products.
                  </motion.p>
                  <motion.p
                    initial={{ y: 50, opacity: 0 }}
                    animate={{ y: 0, opacity: 1 }}
                    transition={{ delay: 0.6, duration: 0.9 }}
                    className="mt-4 text-md md:text-lg text-gray-300 max-w-2xl"
                  >
                    Discover top-quality gear with exclusive offers—shop now and elevate your experience!
                  </motion.p>
                  <motion.div
                    initial={{ y: 50, opacity: 0 }}
                    animate={{ y: 0, opacity: 1 }}
                    transition={{ delay: 0.7, duration: 0.9 }}
                  >
                    <PrimaryButton
                      className="mt-10 px-10 py-5 rounded-full bg-gradient-to-r from-indigo-600 to-purple-700 text-white text-xl font-bold hover:from-indigo-700 hover:to-purple-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                      <a href={route('products.byDepartment', dep.slug)}>
                        Shop Now
                      </a>
                    </PrimaryButton>
                  </motion.div>
                  <motion.p
                    initial={{ y: 50, opacity: 0 }}
                    animate={{ y: 0, opacity: 1 }}
                    transition={{ delay: 0.8, duration: 0.9 }}
                    className="mt-6 text-sm md:text-base text-gray-400 italic"
                  >
                    Limited stock available—don’t miss out on the best deals of the season!
                  </motion.p>
                </div>
              </motion.div>
            )
        )}
      </AnimatePresence>

      {/* Controls */}
      <button
        onClick={prevSlide}
        className="absolute left-6 top-1/2 -translate-y-1/2 bg-gray-800/70 text-white p-5 rounded-full hover:bg-gray-700/90 transition-all duration-300 hover:shadow-md"
        aria-label="Previous slide"
      >
        <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <button
        onClick={nextSlide}
        className="absolute right-6 top-1/2 -translate-y-1/2 bg-gray-800/70 text-white p-5 rounded-full hover:bg-gray-700/90 transition-all duration-300 hover:shadow-md"
        aria-label="Next slide"
      >
        <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
        </svg>
      </button>

      {/* Dots indicator */}
      <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex space-x-3">
        {sharedDepartments1.map((_, index) => (
          <button
            key={index}
            onClick={() => setCurrent(index)}
            className={`w-4 h-4 rounded-full transition-all duration-300 ${
              index === current ? 'bg-indigo-500 scale-150' : 'bg-gray-500/50 hover:bg-gray-400/70'
            }`}
            aria-label={`Go to slide ${index + 1}`}
          />
        ))}
      </div>
    </section>
  );
}
