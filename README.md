# Webco SSE Technical Test

This is my implementation of the Webco Senior Software Engineer technical test. The project demonstrates my approach to building a Laravel-based product management system using modern practices and tools.

## Overview

I've built a product management system that showcases:
- Clean, maintainable code following SOLID principles
- Efficient use of Laravel and Filament Admin
- Practical implementation of background jobs and API integration
- Attention to UX/UI details
- Proper error handling and validation

## Tech Stack

- Laravel 10
- PHP 8.2
- Filament Admin Panel 3.x
- SQLite (for simplicity in test environment)
- Node.js & NPM for asset compilation

## Getting Started

1. Clone and setup:
```bash
git clone https://github.com/kisalnelaka/Webco_SSE_test.git
cd Webco_SSE_test

# Install dependencies
composer install
npm install

# Build assets
npm run build
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate

# Create SQLite database
touch database/database.sqlite
```

3. Set up the database:
```bash
php artisan migrate
```

4. Start the services:
```bash
# Terminal 1: Start the development server
php artisan serve

# Terminal 2: Start the queue worker for background jobs
php artisan queue:work
```

5. Access the admin panel:
```
URL: http://localhost:8000/webco-admin
Email: admin@example.com
Password: password
```

## Security Features

- CSRF protection on all forms
- Rate limiting on API endpoints
- Secure session handling
- Input validation and sanitization
- Protected admin routes
- Address validation with error handling

## Implementation Details

### Key Features
- Product management with color and category organization
- Address validation with status tracking
- Background job processing for long-running tasks
- Real-time dashboard metrics
- Responsive admin interface with custom theme

### Code Organization
```
app/
├── Filament/           # Admin panel components
│   └── WebcoAdmin/     # Custom resources and widgets
├── Jobs/               # Background job handlers
├── Models/             # Eloquent models with relationships
├── Traits/            # Reusable traits (including AddressValidation)
└── Providers/          # Service providers and configuration
```

### Design Decisions

1. **SQLite Database**: Chose SQLite for easy setup and testing
2. **Background Jobs**: Queue-based processing for time-consuming tasks
3. **Address Validation**: Added retry mechanism and proper error handling
4. **Admin Interface**: Customized Filament theme for better UX

## Testing

### Test Structure
We've organized our tests into three main categories:

1. **Product Management Tests**
   - API endpoint validation
   - CRUD operation verification
   - Input validation checks

2. **Address Validation Tests**
   - Format validation
   - API integration testing
   - Error handling verification

3. **Admin Panel Tests**
   - Authentication checks
   - Permission validation
   - Resource access control

### Running Tests
```bash
php artisan test
```

## Notes

- API rate limits: 60 requests per minute
- Queue retry attempts: 3
- Cache duration: 5 minutes for dashboard metrics
- File upload max size: 5MB

## License

This is a technical test implementation. Code structure and patterns can be reused, but the implementation is specific to the test requirements.

---
Built by Kisal Nelaka for Webco Senior PHP developer Interview using Laravel & Filament