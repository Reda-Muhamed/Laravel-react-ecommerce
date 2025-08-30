/* eslint-disable prettier/prettier */

import ProductItem from "@/Components/App/ProductItem";
import FilterSidebar from "@/Components/Core/FilterSidebar";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { IndexProps } from "@/types";
import { Head } from "@inertiajs/react";
import { motion } from 'framer-motion';



export default function Index({
  products,
  department,
  departments,
  categories,
  filters,
}: IndexProps) {
  // console.log("=================================")
  // console.log('department', department)
  // console.log('categories', categories)
  // console.log('products', products)
  // console.log('filters', filters)
  // console.log('departments', departments)

  return (

    <AuthenticatedLayout>
       <Head>
         <title>{department.name}</title>
         <meta name='title' content={department.meta_title} />
         <meta name="description" content={department.meta_description} />
         <link rel="canonical" href={route('products.byDepartment', department.slug)} />
         <meta property="og:type" content="website" />
         <meta property="og:url" content={route('products.byDepartment', department.slug)} />
         <meta property="og:title" content={department.name} />
         <meta property="og:description" content={department.meta_description} />
         {/* <meta property="og-site_name" content={appName} /> */}
       </Head>

      <div className=" mx-auto flex gap-6">
        {/* Sidebar */}
        <FilterSidebar departments={departments} categories={categories} showCategories={true} showDepartments={false} filters={filters}  />

        {/* Main Content */}
        <div className="flex-1">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-10 mt-0 pt-10"
          >
            <h2 className="lg:text-5xl text-3xl md:text-5xl font-extrabold text-white relative inline-block">
              {department.name}
              <span className="block mt-3 h-1 w-32 mx-auto bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full"></span>
            </h2>
          </motion.div>

          {products.data.length === 0 && (
            <div className="py-16 px-8 text-center text-gray-500 text-3xl">
              No products found in this department
            </div>
          )}

          <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-8">
            {products.data.map(product => (
              <ProductItem key={product.id} product={product} />
            ))}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  )
}
