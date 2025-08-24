/* eslint-disable prettier/prettier */
import InputError from '@/Components/Core/InputError';
import InputLabel from '@/Components/Core/InputLabel';
import Modal from '@/Components/Core/Modal';
import PrimaryButton from '@/Components/Core/PrimaryButton';
import SecondaryButton from '@/Components/Core/SecondaryButton';
import TextInput from '@/Components/Core/TextInput';
import { useForm, usePage } from '@inertiajs/react';
import React, { FormEventHandler, useState } from 'react'

export default function VendorDetails({ className }: { className?: string }) {
  const [showBecomeVendor, setShowBecomeVendor] = useState(false)
  const [suceessMessage, setSuccessMessage] = useState('');
  const user = usePage().props.auth.user;
  const token = usePage().props.csrf_token;
  const { data, setData, errors, post, processing, recentlySuccessful } = useForm({
    store_name: user?.vendor?.store_name || user.name.toLowerCase().replace(/\s+/g, '-'),
    store_address: user?.vendor?.store_address || '',
  });
  const onStoreNameChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setData('store_name', e.target.value.toLowerCase().replace(/\s+/g, '-'));
  }
  const becomeVendor: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('vendor.store'), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        closeModal();
        setSuccessMessage('You have successfully applied to become a vendor. We will review your application and get back to you soon.');
      },
      onError: (err) => {
        console.log(err);
      }
    });
  }
  const updateVendor: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('vendor.store'), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        closeModal();

        setSuccessMessage('You have successfully updated your vendor details.');
      },
      onError: (err) => {
        console.log(err);
      }
    });
  }
  const closeModal = () => {
    setShowBecomeVendor(false);
  }
  return (
    <section className={className}>
      {
        recentlySuccessful && (
          <div className="toast toast-top toast-end">
            <div className="alert alert-success">
              <div>
                <span>{suceessMessage}</span>
              </div>
            </div>

          </div>)
      }
      <header>
        <h2 className="flex justify-between mb-8 text-lg font-medium text-gray-900 dark:text-gray-100">
          Vendor Details
          {user?.vendor?.status === 'pending' && (<span className="badge badge-warning">{user.vendor.status_label}l</span>)}
          {user?.vendor?.status === 'rejected' && (<span className="badge badge-error">{user.vendor.status_label}</span>)}
          {user?.vendor?.status === 'approved' && (<span className="badge badge-success">{user.vendor.status_label}</span>)}


        </h2>

      </header>
      <div className="">
        {!user?.vendor && (
          <button
            type="button"
            className="btn btn-sm btn-primary"
            onClick={() => setShowBecomeVendor(true)}
            disabled={processing}
          >
            Become a Vendor
          </button>
        )}
        {user?.vendor && (
          <>
            <form action="post" onSubmit={updateVendor}>
              <div className="mb-4">
                <InputLabel htmlFor='name' value="Store Name" />
                <TextInput id='name' value={data.store_name} className="mt-1 block w-full" onChange={onStoreNameChange} required isFocused autoComplete='name' />
                {errors.store_name && <InputError message={errors.store_name} className="mt-2" />}
              </div>
              <div className="mb-4">
                <InputLabel htmlFor='address' value='Store Address' />
                <textarea id='address' value={data.store_address} className="textarea textarea-bordered w-full mt-1" onChange={e => setData('store_address', e.target.value)} required />
                {errors.store_address && <InputError message={errors.store_address} className="mt-2" />}
              </div>
              <div className="flex items-center gap-4">
                <button type='submit' className='btn btn-primary btn-sm' disabled={processing}>Update</button>
              </div>
            </form>


            <form method='post' action={route('stripe.connect')} className='mt-4'>
              <input type="hidden" name="_token" value={token} />
              {!user.stripe_account_active && (<PrimaryButton   disabled={processing}>Connect to Stripe</PrimaryButton>)}
              {user.stripe_account_active && (<PrimaryButton  disabled>Stripe Connected</PrimaryButton>)}
            </form>
          </>)}
          <Modal show={showBecomeVendor} onClose={closeModal} >
            <form action="post" onSubmit={becomeVendor} className='p-8 flex justify-center items-center'>
              <h2 className="mb-8 text-lg font-medium text-gray-900 dark:text-gray-100">Become a Vendor ?</h2>
              <div className="mt-6 flex justify-end ">
                <PrimaryButton  className='btn btn-primary ms-3' disabled={processing}>Submit</PrimaryButton>
                <SecondaryButton  onClick={closeModal}>Cancel</SecondaryButton>
              </div>


            </form>
            </Modal>
      </div>
    </section>
  )
}

