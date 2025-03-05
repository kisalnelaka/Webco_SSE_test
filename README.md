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
cd webco-sse-test

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

The admin panel should now be accessible at `http://localhost:8000/webco-admin`

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
└── Providers/          # Service providers and configuration
```

### Design Decisions

1. **SQLite Database**: Chose SQLite for easy setup and testing. In production, this would typically use MySQL/PostgreSQL.

2. **Background Jobs**: Implemented queue-based processing to handle time-consuming tasks without blocking the UI.

3. **Address Validation**: Added retry mechanism and proper error handling for API failures.

4. **Admin Interface**: Customized Filament theme for better UX while maintaining consistency.

## Performance Considerations

- Implemented caching for dashboard metrics
- Optimized database queries using eager loading
- Added proper indexing on frequently queried columns
- Used queue workers for background processing

## Bug Fixes and Known Issues

### Recent Bug Fixes
- Fixed missing `typeables` table for product type relationships
- Resolved duplicate column issue in `address_status` migration
- Fixed polymorphic relationship configuration in Product model
- Addressed Livewire component rendering issues
- Optimized page load times for product listing

### Known Issues
- High load times (~15s) on individual product views - under investigation
- Intermittent Livewire update delays (~2-3s) - performance optimization in progress
- CSS asset loading optimization needed (currently ~500ms in some cases)

## Testing

Run the test suite:
```bash
php artisan test
```

## Areas for Improvement

Given more time, I would:
- Add more comprehensive test coverage
- Implement real-time updates using Laravel Echo
- Add batch processing for bulk operations
- Enhance dashboard with more detailed metrics
- Optimize asset loading and caching strategies
- Implement proper error logging and monitoring

## Notes

- The project uses database queue driver for simplicity. In production, I'd recommend Redis/Horizon
- API credentials should be properly configured in `.env` for address validation
- Cache can be cleared with `php artisan cache:clear` if needed
- For development, ensure proper file permissions on storage and bootstrap/cache directories

## Repository

- GitHub: [https://github.com/kisalnelaka/Webco_SSE_test](https://github.com/kisalnelaka/Webco_SSE_test)
- Language Stats: PHP (53.1%), Blade (46.0%), Other (0.9%)

## License

This is a technical test implementation. Code structure and patterns can be reused, but the implementation is specific to the test requirements.

---
Built by [Your Name] using Laravel & Filament
