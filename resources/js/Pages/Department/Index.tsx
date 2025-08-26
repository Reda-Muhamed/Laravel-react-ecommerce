/* eslint-disable prettier/prettier */

import ProductItem from "@/Components/App/ProductItem";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Department, PageProps, PaginationProps, Product } from "@/types";
import { Head } from "@inertiajs/react";
import { motion } from 'framer-motion';

export default function Index({
  products,
  department,
}: PageProps<{ department: Department, products: PaginationProps<Product> }>) {
  console.log('department', department)
  console.log('products', products.data)
  return (
    <AuthenticatedLayout>
      <Head>
        <title>{department.name}</title>
        <meta name='title' content={department.meta_title }/>
        <meta name="description" content={department.meta_description}/>
        <link rel="canonical" href={route('products.byDepartment',department.slug)} />
        <meta property="og:type" content="website" />
        <meta property="og:url" content={route('products.byDepartment',department.slug)} />
        <meta property="og:title" content={department.name} />
        <meta property="og:description" content={department.meta_description} />
        {/* <meta property="og-site_name" content={appName} /> */}
      </Head>
      {/* <div className="containter mx-auto">
        <div className="hero bg-base-200 min-h-[120px">
          <div className="hero-content text-center">
            <div className="max-w-md">
              <h1 className="text-5xl font-bold">{department.name}</h1>
            </div>
          </div>
        </div>
      </div> */}
      <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
          className="text-center mb-10 mt-10"
        >
          <h2 className="lg:text-5xl text-3xl  md:text-5xl font-extrabold text-white relative inline-block">
            {department.name}
            <span className="block mt-3 h-1 w-32 mx-auto bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full"></span>
          </h2>

        </motion.div>
      {
        products.data.length === 0 && (
          <div className="py-16 px-8 text-center text-gray-500 text-3xl">
            No products found in this department
          </div>
        )
      }
      <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg
      :grid-cols-3 xl:grid-cols-4 p-8">
        {products?.data?.map((product) => (
          <ProductItem key={product.id} product={product} />
        ))}
      </div>

    </AuthenticatedLayout>
  )
}
