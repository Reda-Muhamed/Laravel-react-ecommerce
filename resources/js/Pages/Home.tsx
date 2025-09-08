/* eslint-disable prettier/prettier */
import DepartmentsSection from '@/Components/Core/DepartmentsSection';
import ProductItem from '@/Components/App/ProductItem';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { IndexProps } from '@/types';
import { Head } from '@inertiajs/react';
import FilterSidebar from '@/Components/Core/FilterSidebar';
import { motion } from 'framer-motion';
import HeroSection from '@/Components/App/HeroSection';
import Pagination from '@/Components/Core/Pagination';
import NProgress from "nprogress";
import { router } from "@inertiajs/react";
import "nprogress/nprogress.css";
import "../../css//nprogress-custom.css";

// Start Loader
router.on("start", () => {
  if (!document.getElementById("custom-loader")) {
    const overlay = document.createElement("div");
    overlay.id = "custom-loader";
    overlay.style.cssText = `
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    `;
    overlay.innerHTML = `
      <div style="
        width: 60px;
        height: 60px;
        border: 6px solid #e5e7eb;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
      "></div>
      <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
      </style>
    `;
    document.body.appendChild(overlay);
  }

  NProgress.start();
});

// Stop Loader
router.on("finish", () => {
  NProgress.done();
  const overlay = document.getElementById("custom-loader");
  if (overlay) overlay.remove();
});

export default function Home({ products,
  departments,
  categories,
  filters,
}: IndexProps) {
  // console.log("=================================");
  // console.log('filters', filters);
  // console.log('departments', departments);
  // console.log('categories', categories);
  console.log('productssssssss', products);
  // console.log("=================================");
  return (
    <AuthenticatedLayout>
      <Head title="Home" />
      <HeroSection />

      <DepartmentsSection />

      <div className="flex flex-col lg:flex-row bg-gray-950">
        {/* Sidebar - hidden on small screens */}
        <div className="hidden lg:block w-1/4 p-4 ">
          <FilterSidebar
            filters={filters}
            categories={categories}
            departments={departments}
            showCategories={true}
            showDepartments={true}
          />
        </div>
        {/* Products Grid */}
        <div>
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-4xl font-normal md:text-5xl  text-white relative inline-block">
              Products
              <span className="block mt-3 h-1 w-32 mx-auto bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full"></span>
            </h2>
          </motion.div>
          <div className="flex-1 grid grid-cols-1 gap-6 p-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 bg-[#030712]">
            {products?.data?.map((product) => (
              <ProductItem key={product.id} product={product} />
            ))}
          </div>
          <Pagination  meta={products.meta} data={[]} links={{
            first: null,
            last: null,
            prev: null,
            next: null
          }} />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
