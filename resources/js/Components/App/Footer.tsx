/* eslint-disable prettier/prettier */
import React from 'react';

const Footer = () => {
  return (
    <footer className="bg-gradient-to-br from-gray-900  to-gray-950 text-white py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Company Info */}
          <div className="mb-6 md:mb-0">
            <h3 className="text-2xl font-bold text-indigo-400 mb-4">So2 Baladna</h3>
            <p className="text-gray-400 text-sm">
              Empowering your business with innovative solutions and exceptional service.
            </p>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="text-lg font-semibold text-white mb-4">Contact Us</h4>
            <p className="text-gray-300">Name: Reda Mohamed</p>
            <p className="text-gray-300 mt-2">Phone: +201069582548</p>
            <p className="text-gray-300 mt-2">Email: rede.mohamed.reda.201@gmail.com</p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-lg font-semibold text-white mb-4">Quick Links</h4>
            <ul className="space-y-2">
              <li><a href="/about" className="text-gray-400 hover:text-indigo-400 transition">About Us</a></li>
              <li><a href="/products" className="text-gray-400 hover:text-indigo-400 transition">Products</a></li>
              <li><a href="/contact" className="text-gray-400 hover:text-indigo-400 transition">Contact</a></li>
            </ul>
          </div>
        </div>

        <div className="mt-8 pt-6 border-t border-gray-800 text-center">
          <p className="text-gray-500 text-sm">
            © {new Date().getFullYear()} So2 Baladna. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
