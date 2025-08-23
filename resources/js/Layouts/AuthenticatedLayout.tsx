/* eslint-disable prettier/prettier */
import ApplicationLogo from '@/Components/App/ApplicationLogo';
import Navbar from '@/Components/App/Navbar';
import Dropdown from '@/Components/Core/Dropdown';
import NavLink from '@/Components/Core/NavLink';
import ResponsiveNavLink from '@/Components/Core/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode, useEffect, useRef, useState } from 'react';

export default function AuthenticatedLayout({
  header,
  children,
}: PropsWithChildren<{ header?: ReactNode }>) {
  const props = usePage().props;
  const user = props.auth.user;
  const [showingNavigationDropdown, setShowingNavigationDropdown] =
    useState(false);

console.log(props.success);

  const [successMessage, setSuccessMessage] = useState<any[]>([]);
  const timeoutRef = useRef<{ [key: number]: ReturnType<typeof setTimeout> }>({});
  useEffect(() => {
    if (props.success?.message) {
      const newMessage = {
        ...props.success,
        id: props.success.time,
      };
      setSuccessMessage((prev) => [newMessage, ...prev]);
      const timeoutId = setTimeout(() => {
        setSuccessMessage((prev) => prev.filter((msg) => msg.id !== newMessage.id))
        delete timeoutRef.current[newMessage.id]
      }, 5000)
      timeoutRef.current[newMessage.id] = timeoutId
    }
  }, [props.success])


  return (
    <div className="min-h-screen bg-gray-100 dark:bg-gray-900">

      <Navbar />

      {props.error && (
        <div className="container mx-auto px-8 mt-8">
          <div className="bg-red-500 text-white px-4 py-2 rounded">
            {props.error}
          </div>
        </div>
      )}
      {
        successMessage.length>0&&(
          <div className="toast toast-top toast-end z-[1000] mt-16">
            {
              successMessage.map((msg)=>(
                <div key={msg.id} className="alert alert-success">
                  <span>{msg.message}</span>
                </div>
              ))
            }
          </div>
        )
      }

      <main>{children}</main>
    </div>
  );
}
