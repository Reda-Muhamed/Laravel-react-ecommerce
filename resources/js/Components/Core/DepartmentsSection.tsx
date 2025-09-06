/* eslint-disable prettier/prettier */
import { Link, usePage } from '@inertiajs/react';
import React, { useEffect, useRef, useState } from 'react';
import { motion } from 'framer-motion';
import { ChevronLeft, ChevronRight } from "lucide-react";
import { Department, PageProps } from '@/types';


function DepartmentsSection() {
  const { sharedDepartments } = usePage<PageProps>().props ;
  const sharedDepartments1: Department[] = sharedDepartments as Department[];
  // console.log(sharedDepartments);

  const scrollRef = useRef<HTMLDivElement | null>(null);
  const [autoScroll, setAutoScroll] = useState(true);

  // Duplicate departments for infinite scroll effect

  useEffect(() => {
    const scrollContainer = scrollRef.current;
    if (!scrollContainer) return;

    // Start at the beginning of the duplicated content
    scrollContainer.scrollLeft = 0;

    const step = () => {
      if (!autoScroll) return;

      const scrollWidth = scrollContainer.scrollWidth;
      const clientWidth = scrollContainer.clientWidth;
      const maxScrollLeft = scrollWidth - clientWidth;

      if (scrollContainer.scrollLeft >= maxScrollLeft / 2) {
        // Reset to the start of the duplicated content for seamless looping
        scrollContainer.scrollLeft = 0;
      } else {
        // Scroll left by a small amount (adjust for smoothness)
        scrollContainer.scrollBy({ left: -2, behavior: "smooth" });
      }
    };

    const interval = setInterval(step, 50); // Faster interval for smoother scroll
    return () => clearInterval(interval);
  }, [autoScroll]);

  // Pause auto-scroll on user interaction
  useEffect(() => {
    const scrollContainer = scrollRef.current;
    if (!scrollContainer) return;

    let timeout: NodeJS.Timeout;

    const handleUserInteraction = () => {
      setAutoScroll(false);
      clearTimeout(timeout);
      timeout = setTimeout(() => setAutoScroll(true), 4000); // Resume after 4s
    };

    scrollContainer.addEventListener("scroll", handleUserInteraction);
    scrollContainer.addEventListener("mouseenter", () => setAutoScroll(false));
    scrollContainer.addEventListener("mouseleave", () => setAutoScroll(true));

    return () => {
      scrollContainer.removeEventListener("scroll", handleUserInteraction);
      scrollContainer.removeEventListener("mouseenter", () => setAutoScroll(false));
      scrollContainer.removeEventListener("mouseleave", () => setAutoScroll(true));
      clearTimeout(timeout);
    };
  }, []);

  // Manual scroll controls
  const scrollByAmount = (amount: number) => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: amount, behavior: "smooth" });
    }
  };
  // console.log(departments);

  return (
    <section className="bg-gradient-to-b from-gray-900 to-gray-950 py-16 px-6 text-white relative">
      <div className="max-w-7xl mx-auto">
        {/* Section Title */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
          className="text-center mb-12"
        >
          <h2 className="text-4xl font-normal md:text-5xl  text-white relative inline-block">
            Shop by Department
            <span className="block mt-3 h-1 w-32 mx-auto bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full"></span>
          </h2>
          <p className="text-gray-400 mt-4 text-base md:text-lg">
            Discover categories tailored for you
          </p>
        </motion.div>

        {/* Scrollable Container */}
        <div className="relative">
          {/* Left Arrow */}
          <button
            onClick={() => scrollByAmount(-300)}
            className="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-gray-800/70 hover:bg-gray-700 p-3 rounded-full shadow-lg"
          >
            <ChevronLeft className="w-6 h-6" />
          </button>

          {/* Departments */}
          <div
            ref={scrollRef}
            className="overflow-x-auto overflow-y-hidden scrollbar-hide pb-4"
            style={{ scrollBehavior: "smooth" }}
          >
            <div className="grid grid-rows-2 grid-flow-col gap-6 auto-cols-max px-12">
              {sharedDepartments1.map((dep:Department, index:number) => (
                <motion.div
                  key={`${dep.id}-${index}`}
                  whileHover={{ scale: 1.08 }}
                  transition={{ type: "spring", stiffness: 200 }}
                  className="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl shadow-md hover:shadow-2xl cursor-pointer border border-gray-700 group w-48 h-60 flex-shrink-0"
                >
                  <Link
                    href={route("products.byDepartment", dep.slug)}
                    className="flex flex-col items-center text-center  pt-3 px-0 "
                  >
                    {/* Department Image */}
                    <div className="h-44 w-44 mb-4 rounded-2xl overflow-hidden bg-gray-100 shadow-md">
                      <img
                        src={dep?.image || "https://via.placeholder.com/150"}
                        alt={dep.name}
                        className="w-full object-cover h-40  group-hover:scale-110 transition-transform"
                      />
                    </div>

                    {/* Department Name */}
                    <span className="text-base font-semibold capitalize text-gray-200 group-hover:text-purple-400">
                      {dep.name}
                    </span>
                  </Link>
                </motion.div>
              ))}
            </div>
          </div>

          {/* Right Arrow */}
          <button
            onClick={() => scrollByAmount(300)}
            className="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-gray-800/70 hover:bg-gray-700 p-3 rounded-full shadow-lg"
          >
            <ChevronRight className="w-6 h-6" />
          </button>
        </div>
      </div>

      {/* Hide scrollbar */}
      <style >{`
        .scrollbar-hide::-webkit-scrollbar {
          display: none;
        }
        .scrollbar-hide {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }
      `}</style>
    </section>
  );
}

export default DepartmentsSection;
