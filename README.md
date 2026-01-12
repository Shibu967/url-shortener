# URL Shortener Service

A Laravel-based URL shortener service with multi-company support and role-based access control.

## Features

- Multi-company support
- Role-based access control (SuperAdmin, Admin, Member, Sales, Manager)
- User invitation system
- URL shortening functionality
- Public URL redirection

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL
- Laravel 12

## Setup Instructions

1. Clone the repository:
```bash
git clone <repository-url>
cd url_shortner
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure MySQL database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. Create the MySQL database:
```sql
CREATE DATABASE url_shortener;
```

7. Run migrations:
```bash
php artisan migrate
```

8. Seed SuperAdmin account:
```bash
php artisan db:seed
```

This will create a SuperAdmin account using raw SQL:
- Email: `superadmin@example.com`
- Password: `password`

9. Start the development server:
```bash
php artisan serve
```

10. Visit `http://localhost:8000` in your browser.

## Testing

Run the test suite:
```bash
php artisan test
```

## Usage

1. Login with the SuperAdmin credentials (`superadmin@example.com` / `password`)
2. Create a company (manually via database or through the application)
3. Invite Admins to companies
4. Admins can invite other Admins or Members to their company
5. Admins and Members can create short URLs
6. Short URLs are publicly accessible at `/s/{code}`

## Roles and Permissions

- **SuperAdmin**: Can invite Admins to new companies, view all short URLs, cannot create short URLs
- **Admin**: Can invite Admins and Members to their company, create short URLs, view short URLs from other companies
- **Member**: Can create short URLs, view short URLs created by others

## Database Structure

- `companies`: Stores company information
- `users`: Stores user accounts with role and company_id
- `invitations`: Stores pending user invitations
- `short_urls`: Stores shortened URLs with original URLs and short codes
