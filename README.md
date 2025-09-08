# Laravel-React E-Commerce Platform

Welcome to the **Multi-Vendor E-Commerce Platform**, a robust full-stack application built with **Laravel**, **Inertia.js**, and **React**. This project combines powerful backend functionality with a modern, interactive frontend to create a scalable e-commerce solution. Check out the source code on [GitHub](https://github.com/Reda-Muhamed/Laravel-react-ecommerce).

## Project Overview

This platform enables multiple vendors to manage their products, process orders, and receive automated payouts, while providing admins with comprehensive oversight. It leverages Laravel's backend strength and React's dynamic frontend capabilities via Inertia.js for a seamless user experience.

## Features

- **Vendor Onboarding**: Users can easily apply to become vendors and expand the marketplace.
- **Product Management**: Vendors can list, showcase, and sell products with full control.
- **Vendor Dashboards**: Intuitive interfaces for managing products and tracking orders.
- **Admin Oversight**: Admins can monitor and manage all vendors and orders effectively.
- **Product Variations**: Support for multiple product options to cater to diverse needs.
- **Secure Payouts**: Integration with Stripe for vendors to receive automated payouts.
- **Automated Payments**: Monthly payouts processed via a scheduled command on the first of each month.

## Technologies Used

- **Laravel**: Backend framework for robust API and business logic.
- **Inertia.js**: Bridges Laravel and React for server-driven single-page apps.
- **React**: Frontend library for dynamic user interfaces.
- **Tailwind CSS**: Utility-first CSS framework for responsive design.
- **Stripe**: Payment processing for vendor payouts.
- **MySQL**: Database for storing user, product, and order data.

## Installation

Follow these steps to set up the project locally:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Reda-Muhamed/Laravel-react-ecommerce.git
   cd Laravel-react-ecommerce
   ```

2. **Install Dependencies**
   - Install PHP dependencies:
     ```bash
     composer install
     ```
   - Install Node.js dependencies:
     ```bash
     npm install
     ```

3. **Configure Environment**
   - Copy the `.env.example` file to `.env` and update the following:
     - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for your MySQL database.
     - `APP_URL` with your local URL (e.g., `http://localhost`).
     - Stripe credentials under `STRIPE_KEY` and `STRIPE_SECRET`.
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```

4. **Set Up Database**
   - Create a MySQL database and run migrations:
     ```bash
     php artisan migrate
     ```
   - Seed the database with initial data (optional):
     ```bash
     php artisan db:seed
     ```

5. **Run the Application**
   - Start the Laravel server:
     ```bash
     php artisan serve
     ```
   - Build and start the frontend:
     ```bash
     npm run dev
     ```
   - Access the app at `http://localhost:8000`.

6. **Schedule Commands**
   - Ensure the Laravel scheduler is running for automated payouts:
     ```bash
     php artisan schedule:run
     ```
   - Configure a cron job to run the scheduler every minute (e.g., `* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1`).

## Usage

- **Vendors**: Register, apply to become a vendor, and use the dashboard to manage products and view orders.
- **Admins**: Log in with admin credentials to oversee vendors and orders.
- **Customers**: Browse products, place orders, and enjoy a seamless shopping experience.

## Contributing

Contributions are welcome! To contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes and commit them (`git commit -m "Description of changes"`).
4. Push to the branch (`git push origin feature-branch`).
5. Open a Pull Request with a clear description of your changes.

Please review the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct) before contributing.

## License

This project is open-source software licensed under the [MIT License](LICENSE).

## Contact

- **Developer**: Reda Mohamed
- **Company**: So2 Baladna
- **Phone**: +201069582548
- **Email**: rede.mohamed.reda.201@gmail.com

## Acknowledgments

- Thanks to the Laravel and React communities for their amazing tools and support.
- Special appreciation to Stripe for secure payment integration.

---

**Last updated**: Monday, September 08, 2025, 03:21 PM EEST