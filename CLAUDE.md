# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application using the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) with Filament for the admin panel. It serves as a starter template for building content-driven applications with a robust admin interface.

## Development Commands

### Backend (PHP/Laravel)
- `php artisan serve` - Start development server
- `php artisan migrate:fresh --seed` - Reset database with sample data
- `php artisan key:generate` - Generate application key
- `vendor/bin/pint` - Run Laravel Pint code formatter
- `vendor/bin/pint --test` - Check code formatting without making changes

### Frontend (Node.js/Vite)
- `npm install` - Install dependencies
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build assets for production

### Database
- Uses SQLite by default (`database/database.sqlite`)
- Run migrations: `php artisan migrate`
- Seed database: `php artisan db:seed`

## Architecture Overview

### Core Structure
- **Models**: `app/Models/` - Contains Post and User models with relationships
- **Filament Resources**: `app/Filament/Resources/` - Admin panel CRUD interfaces for Post, User, and Activity
- **Livewire Components**: `app/Livewire/` - Frontend interactive components (Home, Post views)
- **Routes**: `routes/web.php` - Public routes, admin routes handled by Filament

### Frontend Architecture
- **CSS**: Split between main app (`resources/css/app.css`) and admin (`resources/css/admin.css`)
- **JavaScript**: `resources/js/app.js` - Main frontend bundle with Alpine.js and Livewire
- **Views**: `resources/views/` - Blade templates for public-facing pages
- **Assets**: Built with Vite, configured for both app and admin assets

### Key Features
- **Filament Admin Panel**: Located at `/admin` with full CRUD for Posts and Users
- **Media Management**: Uses Filament Curator for file uploads and media library
- **User Authentication**: Filament Breezy for user profiles and 2FA
- **SEO**: Laravel SEO package for meta management
- **Job Monitoring**: Filament Jobs Monitor for background job tracking
- **Activity Logging**: Filament Logger for tracking model changes

### Filament Plugins Used
- **Curator**: Media library and file management
- **Gravatar**: User avatars
- **Exceptions**: Exception monitoring and debugging
- **Jobs Monitor**: Background job status tracking
- **Breezy**: User authentication and profile management
- **Peek**: Frontend preview of resources
- **Logger**: Activity and change logging

## Development Guidelines

### Code Organization
- Follow the established pattern of separating frontend CSS/JS per page as mentioned in user preferences
- Filament resources should include proper form fields, table columns, and relationships
- Livewire components handle frontend interactivity while maintaining clean separation from admin
- Use existing models and relationships when adding new features

### Database Conventions
- Models use standard Laravel conventions with proper relationships
- Posts have slug-based routing and support for published/draft states
- Users integrate with Filament's authentication system

### Asset Management
- Vite handles both app and admin CSS compilation
- TailwindCSS configured for both frontend and Filament admin
- Alpine.js and Livewire provide frontend interactivity
- Assets auto-refresh during development

### Testing and Code Quality
- Laravel Pint enforces code formatting standards
- GitHub Actions runs Pint checks on PR/push to main
- Database uses SQLite for simplicity in development

## Admin Panel Access
- URL: `/admin`
- Default credentials: `admin` / `admin` (created by seeder)
- Features full CRUD for Posts, Users, and system monitoring
- sempre que fizer um push faca um curl para http://24.152.38.77:3000/api/deploy/8d6d4940a47282ae2effcb92feefff6fc4fb35ad8bfe9129