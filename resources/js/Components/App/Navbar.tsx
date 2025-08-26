/* eslint-disable prettier/prettier */
// eslint-disable-next-line prettier/prettier
import { Link, useForm, usePage } from '@inertiajs/react';
import React from 'react'
import MiniCartDrodown from './MiniCartDrodown';
import { PageProps } from '@/types';
import { MagnifyingGlassIcon } from '@heroicons/react/24/outline';

function Navbar() {
  const { auth, keyword } = usePage().props;
  const { url } = usePage();
  // console.log('url',url.split('/')[2])
  // console.log('departments',departments)
  const { user } = auth;
  const searchForm = useForm<{
    keyword: string,
  }>({
    keyword: keyword || '',
  })
  const onSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    searchForm.get(url, {
      preserveState: true,
      preserveScroll: true,
    });

  }
  return (

    <div className="navbar max-w-full bg-base-100 shadow-sm bg-gradient-to-r from-gray-950 to-gray-900">
      <div className="flex-1">
        <Link href='/' className="btn btn-ghost text-xl">RedaStore</Link>
      </div>

      <div className="flex gap-4 ">
        <div className='hidden sm:flex'>
          <form onSubmit={onSubmit} className="join flex-1 h-full hidden lg:inline-flex">
            <input
              type="text"
              name="search"
              value={searchForm.data.keyword}
              onChange={(e) => searchForm.setData('keyword', e.target.value)}
              placeholder="Search..."
              className="input input-bordered join-item flex-1  focus:outline-none focus:ring-1 outline-none border-spacing-1 bg-gradient-to-r from-gray-950 to-gray-900"
            />
            <button type="submit" className="btn join-item bg-gradient-to-r hover:from-gray-900 hover:to-gray-950 from-gray-950 to-gray-900">
              <MagnifyingGlassIcon className="size-4" /> Search
            </button>
          </form>
        </div>


        <MiniCartDrodown />
        {user && <div className="dropdown dropdown-end">
          <div tabIndex={0} role="button" className="btn btn-ghost btn-circle avatar">
            <div className="w-10 rounded-full">
              <img
                alt="Tailwind CSS Navbar component"
                src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
            </div>
          </div>
          <ul
            tabIndex={0}
            className="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
            <li>
              <Link href={route('profile.edit')} className="justify-between">
                Profile

              </Link>
            </li>
            <li><Link href={route('logout')} method={'post'} as='button'>Logout</Link></li>

          </ul>
        </div>
        }
        {!user && <>
          <Link href={route('login')} className={"btn"}>Login</Link>
          <Link href={route('register')} className={"btn btn-primary"}>Register</Link>
        </>}
      </div>
    </div>



  )
}

export default Navbar;

