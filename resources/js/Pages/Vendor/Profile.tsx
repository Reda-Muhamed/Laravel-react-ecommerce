/* eslint-disable prettier/prettier */
import ProductItem from '@/Components/App/ProductItem'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import { PageProps, PaginationProps, Product, Vendor } from '@/types'
import { Head } from '@inertiajs/react'
import React from 'react'

export default function Profile({ vendor, products }: PageProps<{ vendor: Vendor, products: PaginationProps<Product> }>) {
  // console.log('vendor', vendor)
  console.log('products', products
  )
  return (
    <AuthenticatedLayout>
      <Head title={vendor.store_name + ' Profile Page'} />
      <div className="hero min-h-[320px]" style={{
        backgroundImage: "url(https://img.daisyui.com/images/stock/photo-1507358522600-9f71e620c44e.webp)",
      }}>
        <div className="hero-overlay bg-opacity-60">
          <div className="hero-content text-center text-neutral-content">
            <div className="max-w-md">
              <h1 className="mb-5 text-5xl font-bold">{vendor.store_name}</h1>
            </div>
          </div>

        </div>
      </div>
        <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg
            :grid-cols-3 xl:grid-cols-4 p-8">
              {products?.data?.map((product) => (
                <ProductItem  key={product.id} product={product} vendor={vendor} />
              ))}
            </div>
    </AuthenticatedLayout>
  )
}
