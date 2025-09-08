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
          <Pagination meta={products.meta} data={[]} links={{
            first: null,
            last: null,
            prev: null,
            next: null
          }}/>
        </div>
      </div>

    </AuthenticatedLayout>
  );
}
