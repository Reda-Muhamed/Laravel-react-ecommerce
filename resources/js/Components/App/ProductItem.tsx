/* eslint-disable prettier/prettier */
import { Product, Vendor } from '@/types';
import { Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import CurrencyFormatter from '../Core/CurrencyFormatter';

export default function ProductItem({ product , vendor }: { product: Product ,vendor?:Vendor}) {
  const form = useForm<{
    option_ids: Record<string, number>;
    quantity: number;

  }>({
    option_ids: {},
    quantity: 1,
  })
  const addToCart = () => {
    form.post(route('cart.store', product.id), {
      preserveScroll: true,
      preserveState: true,
      onError: (err) => {
        console.log(err);
      }
    })
  }
  // console.log('product',product.store_name);
  // console.log('product',product);
  return (
    <motion.div
      className="bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl border border-gray-700"
      initial={{ opacity: 0, y: 40 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, ease: 'easeOut' }}
      whileHover={{
        scale: 1.02,
        transition: { type: 'spring', stiffness: 200, damping: 15 }
      }}
    >
      <Link href={route('product.show', product.slug)}>
        <motion.figure className="overflow-hidden">
          <motion.img
            src={product.image}
            alt={product.name}
            className="aspect-square object-cover w-full transition-transform duration-700 ease-out"
            whileHover={{ scale: 1.08 }}
          />
        </motion.figure>
      </Link>

      <div className="p-4">
        <h2 className="text-lg font-semibold text-white line-clamp-2">{product.name}</h2>
        <p className="text-sm text-gray-400 mt-1">
          by{' '}
          <Link href={route('vendor.profile',product?.user?.store_name || vendor?.store_name )} className="hover:underline text-gray-300">
            {product?.user?.name}
          </Link>{' '}
          in{' '}
          <Link href="/" className="hover:underline text-gray-300">
            {product?.department?.name}
          </Link>
        </p>

        <div className="flex items-center justify-between mt-4">
          <motion.button
            onClick={addToCart}
            className="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-500 transition-colors"
            whileTap={{ scale: 0.95 }}
          >
            Add to Cart
          </motion.button>
          <span className="text-xl font-bold text-white">
            <CurrencyFormatter amount={product.price} />
          </span>
        </div>
      </div>
    </motion.div>
  );
}
