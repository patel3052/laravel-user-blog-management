# Laravel User Blog Management

A Laravel 12 application for managing users and blogs with authentication and AJAX data tables.

## Features
- User Registration & Login (only Active users can log in)
- User Management with blog count (AJAX DataTable)
- Blog CRUD (Title, Description, Images, Tags, Links)
- Role-based blog visibility (Admin sees all, others see their blogs)
- Blog view page with images, tags, author, and links

## Installation
```bash
# Clone repository
git clone https://github.com/patel3052/laravel-user-blog-management.git
cd laravel-user-blog-management

# Update .env with your DB credentials

# Run migrations
php artisan migrate

# Create storage link for uploaded files
php artisan storage:link

# Start development server
php artisan serve
