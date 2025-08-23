/* eslint-disable prettier/prettier */

import CurrencyFormatter from "@/Components/Core/CurrencyFormatter";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Order, PageProps } from "@/types";
import { CheckCircleIcon } from "@heroicons/react/24/outline";
import { Head, Link } from "@inertiajs/react";

export default function Success({ orders }: PageProps<{ orders: Order[] }>) {
  // console.log(orders[0].data.id);
  console.log(orders[1]);
  // console.log(orders[0].total_price);
  // console.log(orders[0].vendorUser);
  return (
    <AuthenticatedLayout>
      <Head title="Payment Was Completed" />
      <div className="w-[480px] mx-auto py-8 px-4" >
        <div className="flex flex-col gap-2 items-center">
          <div className="text-6xl text-emerald-600">
            <CheckCircleIcon className="size-24" />
          </div>
          <div className="text-3xl">
            Payment was comleted
          </div>
        </div>

        <div className="my-6 text-lg">
          Thanks for your purshase. Your payment was completed successfully.
        </div>

        {
          orders.map(order => (<div key={order.data.id} className="mb-10">

            <div key={order.data.id} className="bg-white dark:bg-gray-800 rounded-lg p-6 mb-4">
              <h3 className="text-3xl mb-3">Order Summary</h3>
              <div className="flex justify-between mb-2 font-bold">
                <div className="text-gray-400">Seller</div>
              </div>
              <div className="">
                <Link href={""} className="hover:underline">
                  {order?.data.vendorUser?.store_name}
                </Link>
              </div>
            </div>
            <div className="flex justify-between mb-2">
              <div className="text-gray-400">
                Order Number
              </div>
              <div className="">
                <Link href={''} className="hover:underline">#{order.data.id}</Link>

              </div>
            </div>
            <div className="flex justify-between mb-3">
              <div className="text-gray-400">
                Items
              </div>
              <div className="">
                {order.data.orderItems?.reduce((total, cur) => total + cur.quantity, 0)}
              </div>
            </div>
            <div className="flex justify-between mb-3">
              <div className="text-gray-400">
                Total
              </div>
              <div>
                <CurrencyFormatter amount={order.data.total_price} />

              </div>
            </div>
            <div className="flex justify-between mt-4">
              <Link href={""} className="btn btn-primary">
                View Order Details
              </Link>
              <Link href={route('dashboard')} className="btn ">
                Back to Home
              </Link>

            </div>

          </div>
          ))}

      </div>

    </AuthenticatedLayout>
  )
}
