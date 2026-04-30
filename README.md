# WP-CS-CRM - WordPress Creator Sales CRM
A comprehensive CRM system built with Laravel 11 for managing creators, orders, and sales analytics synchronized with WordPress/WooCommerce via webhooks and REST API.

## 📚 Table of Contents

- [Features](#-features)
- [Architecture](#-architecture)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Structure](#-database-structure)
- [API Integration](#-api-integration)
- [Usage](#-usage)
- [Development](#-development)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🚀 Features

### Core Functionality
- ✅ **Creator Management** - Manage content creators with brand associations
- ✅ **Order Synchronization** - Real-time sync with WooCommerce orders
- ✅ **Product Tracking** - Track products by brand and creator
- ✅ **Sales Analytics** - Comprehensive reporting and statistics
- ✅ **Commission Calculation** - Automatic commission tracking per creator
- ✅ **Multi-User System** - Role-based access control (Admin, Manager, Viewer)

### Integration Features
- 🔗 **WordPress/WooCommerce API** - Bidirectional synchronization
- 🔗 **Webhook Support** - Real-time event notifications
- 🔗 **RESTful API** - External integrations and mobile apps
- 🔗 **Batch Processing** - Queue-based order synchronization

### Reporting & Analytics
- 📊 **Sales by Product** - Detailed product performance analysis
- 📊 **Sales by Creator** - Individual creator revenue tracking
- 📊 **Brand Analytics** - Brand-level performance metrics
- 📊 **Time-based Reports** - Daily, weekly, monthly, yearly views
- 📊 **Export Capabilities** - CSV export for all reports

### Advanced Features
- 🔐 **Secure Authentication** - Token-based API authentication
- 🔐 **Webhook Verification** - SHA-256 signature validation
- 🔄 **Automatic Retry** - Failed sync retry mechanism
- 📝 **Comprehensive Logging** - Detailed sync and error logs
- ⚡ **Performance Optimized** - Caching, eager loading, query optimization
- 🎨 **Modern UI** - Tailwind CSS with responsive design

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    WordPress/WooCommerce                 │
│                    (MP Creator Plugin)                   │
└────────────────────┬────────────────────────────────────┘
                     │
                     │ Webhooks & REST API
                     │
┌────────────────────▼────────────────────────────────────┐
│                  Laravel CRM System                      │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐       │
│  │  Webhooks  │  │  REST API  │  │   Queue    │       │
│  │  Handler   │  │  Consumer  │  │   Jobs     │       │
│  └─────┬──────┘  └─────┬──────┘  └─────┬──────┘       │
│        │                │                │              │
│  ┌─────▼────────────────▼────────────────▼──────┐     │
│  │          Application Services                  │     │
│  │  • OrderService  • CreatorService             │     │
│  │  • ProductService • WordPressService          │     │
│  └─────┬──────────────────────────────────┬──────┘     │
│        │                                   │            │
│  ┌─────▼───────────┐            ┌─────────▼────────┐  │
│  │    Database     │            │   File Storage   │  │
│  │  • Orders       │            │   • Logs         │  │
│  │  • Creators     │            │   • Exports      │  │
│  │  • Products     │            │                  │  │
│  └─────────────────┘            └──────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## 📋 Requirements

### Server Requirements
- **PHP:** 8.2 or higher
- **Composer:** 2.x
- **Node.js:** 18.x or higher (for asset compilation)
- **Database:** MySQL 8.0+ or MariaDB 10.5+

### PHP Extensions
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

### External Services
- WordPress/WooCommerce with MP Creator Notifier Pro plugin
- Redis (optional, for caching and queues)

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/smartdev1/baana-baana-crm.git
cd wp-cs-crm
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment

Edit `.env` file with your settings:

```env
# Application
APP_NAME="WP-CS-CRM"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wp_cs_crm
DB_USERNAME=root
DB_PASSWORD=

# WordPress Integration
WORDPRESS_URL=https://your-wordpress-site.com
WORDPRESS_API_TOKEN=your_mp_creator_api_token
WORDPRESS_WEBHOOK_SECRET=your_webhook_secret_token

# WooCommerce API Keys
WC_CONSUMER_KEY=ck_your_consumer_key_here
WC_CONSUMER_SECRET=cs_your_consumer_secret_here

# Queue (optional)
QUEUE_CONNECTION=database
# QUEUE_CONNECTION=redis  # For production

# Cache (optional)
CACHE_DRIVER=file
# CACHE_DRIVER=redis  # For production

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### 5. Database Migration

```bash
# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 6. Compile Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## ⚙️ Configuration

### WordPress Integration Setup

#### 1. Install MP Creator Notifier Pro Plugin

Install and activate the plugin on your WordPress site.

#### 2. Generate MP Creator API Token

1. Go to **MP Creators → Settings** in WordPress admin
2. Navigate to **API Token Management** section
3. Click **Generate New Token**
4. **⚠️ IMPORTANT:** Copy the token immediately (you'll only see it once!)
5. Add to Laravel `.env`:
   ```env
   WORDPRESS_API_TOKEN=your_generated_token_here
   ```

#### 3. Generate WooCommerce API Keys

WooCommerce API keys are required for synchronizing orders, products, and other WooCommerce data.

**Step-by-step guide:**

1. **Login to WordPress Admin**
   - Navigate to your WordPress dashboard

2. **Access WooCommerce Settings**
   - Go to **WooCommerce** → **Settings**
   - Click on the **Advanced** tab
   - Click on **REST API** sub-tab

3. **Add New API Key**
   - Click **Add key** button
   - Fill in the details:
     - **Description:** `Laravel CRM Sync` (or any descriptive name)
     - **User:** Select an administrator user
     - **Permissions:** Select **Read/Write**
   - Click **Generate API key**

4. **Copy Your Credentials**
   - **Consumer key:** `ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
   - **Consumer secret:** `cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
   - **⚠️ CRITICAL:** Copy both keys immediately! They won't be shown again.

5. **Add to Laravel `.env`**
   ```env
   WC_CONSUMER_KEY=ck_your_consumer_key_here
   WC_CONSUMER_SECRET=cs_your_consumer_secret_here
   ```

6. **Verify Configuration**
   ```bash
   # Clear config cache
   php artisan config:clear
   
   # Test WooCommerce connection
   php artisan mpcrm:test-woocommerce
   ```

**Troubleshooting WooCommerce API:**
- If you get a 401 error, verify your keys are correct
- Ensure the WordPress user has `manage_woocommerce` permissions
- Make sure REST API is enabled in WooCommerce settings
- For local development, you may need to disable SSL verification

#### 4. Configure Laravel Webhook URL in WordPress

The webhook allows WordPress to notify Laravel in real-time when creators are added or modified.

**Configuration steps:**

1. **In WordPress Admin**
   - Go to **MP Creators → Settings**
   - Navigate to **Laravel CRM Integration** section

2. **Set Laravel Webhook URL**
   - In the **Laravel Webhook URL** field, enter:
     ```
     https://your-laravel-domain.com/api/webhooks/creator-created
     ```
   - Replace `your-laravel-domain.com` with your actual Laravel application URL
   - For local development, use:
     ```
     http://localhost:8000/api/webhooks/creator-created
     ```
   - **Note:** For local testing, you may need to use a tunneling service like **ngrok** or **expose**

3. **Generate Webhook Secret**
   - Still in the **Laravel CRM Integration** section
   - Click **Generate New Secret** button
   - Copy the generated secret token
   - Add to Laravel `.env`:
     ```env
     WORDPRESS_WEBHOOK_SECRET=your_webhook_secret_here
     ```

4. **Save Settings**
   - Click **Save Changes** in WordPress

5. **Test Webhook Connection**
   ```bash
   # In Laravel
   php artisan config:clear
   
   # Create a test creator in WordPress
   # Check Laravel logs to verify webhook was received
   tail -f storage/logs/laravel.log
   ```

**Using ngrok for Local Development:**
```bash
# Install ngrok (https://ngrok.com)
ngrok http 8000

# Use the https URL provided by ngrok
# Example: https://abc123.ngrok.io/api/webhooks/creator-created
```

#### 5. Configure Laravel Sync Webhook in WordPress

This webhook allows WordPress to trigger Laravel synchronization when orders are created or updated.

**Configuration steps:**

1. **In WordPress Admin**
   - Go to **MP Creators → Settings**
   - Navigate to **Laravel Sync Configuration** section

2. **Set Laravel Sync Webhook URL**
   - In the **Laravel Sync Webhook URL** field, enter:
     ```
     https://your-laravel-domain.com/api/webhooks/wordpress/sync-orders
     ```
   - This endpoint will be triggered when:
     - A new order is created
     - An order status changes
     - An order is updated (if enabled)

3. **Configure Sync Options**
   - **Sync on Order Update:** Enable if you want to sync on every order modification
   - **Notify on Status:** Select which order statuses should trigger notifications

4. **Use Same Webhook Secret**
   - The sync webhook uses the same secret as the creator webhook
   - Ensure `WORDPRESS_WEBHOOK_SECRET` is set in your Laravel `.env`

5. **Save and Test**
   - Click **Save Changes**
   - Create a test order in WooCommerce
   - Verify in Laravel:
     ```bash
     tail -f storage/logs/laravel.log | grep "sync"
     ```

#### 6. Test All Connections

```bash
# Test MP Creator API
php artisan wordpress:test-connection

# Test WooCommerce API
php artisan mpcrm:test-woocommerce

# Test complete integration
php artisan mpcrm:diagnose-orders
```

### Initial Synchronization

After configuration, perform an initial sync to import existing data:

```bash
# Sync all creators
php artisan mpcrm:sync creators

# Sync all products
php artisan mpcrm:sync products

# Sync all orders
php artisan mpcrm:sync orders

# Or sync everything at once
php artisan mpcrm:sync all

# Process queue (if using queue)
php artisan queue:work --queue=sync --once
```

**Sync Options:**
```bash
# Force full synchronization (ignore last sync date)
php artisan mpcrm:sync orders --force

# Sync from specific date
php artisan mpcrm:sync orders --from=2024-01-01

# Use queue for large datasets
php artisan mpcrm:sync all --use-queue
```

---

## 🗄️ Database Structure

### Core Tables

#### `users`
User accounts with role-based access
- `id`, `name`, `email`, `password`, `role`

#### `creators`
Content creators with brand associations
- `id`, `wp_creator_id`, `name`, `email`, `brand_slug`, `commission_rate`

#### `orders`
Synchronized orders from WooCommerce
- `id`, `wp_order_id`, `order_number`, `customer_name`, `status`, `total`, `payment_status`

#### `order_items`
Individual items within orders
- `id`, `order_id`, `wp_product_id`, `product_name`, `quantity`, `unit_price`, `total`

#### `products`
Product catalog synchronized from WooCommerce
- `id`, `wp_product_id`, `name`, `sku`, `brand_slug`, `price`, `stock_quantity`

#### `creator_order` (Pivot)
Many-to-many relationship between creators and orders
- `creator_id`, `order_id`, `creator_total`, `metadata`

#### `sync_logs`
Synchronization history and error tracking
- `id`, `sync_type`, `status`, `records_processed`, `error_message`

### Relationships

```
creators (1) ----< (N) creator_order (N) >---- (1) orders
                                                       |
                                                       | (1)
                                                       |
                                                       v
                                                      (N) order_items
                                                       
products (1) ----< (N) order_items
```

---

## 🔌 API Integration

### WordPress REST API Endpoints

All endpoints use base URL: `https://your-wordpress-site.com/wp-json/mp/v2/`

#### Authentication

Add header to all requests:
```http
X-MP-Token: your_api_token
```

#### Available Endpoints

**Creators**
```http
GET    /creators                    # List creators
GET    /creators/{id}               # Get creator details
POST   /creators                    # Create creator
PUT    /creators/{id}               # Update creator
DELETE /creators/{id}               # Delete creator
GET    /creators/{id}/orders        # Get creator orders
GET    /creators/{id}/stats         # Get creator stats
```

**Orders**
```http
GET    /orders                      # List orders
GET    /orders/{id}                 # Get order details
```

**Products**
```http
POST   /products/brands-bulk        # Get brands for products
POST   /products/creators           # Get creators for products
```

**Statistics**
```http
GET    /stats                       # Global statistics
GET    /brands/{slug}/stats         # Brand statistics
```

### Laravel API Endpoints

Base URL: `https://your-laravel-app.com/api/`

**Authentication**
```http
Authorization: Bearer {token}
```

**Endpoints**
```http
GET    /creators                    # List creators
GET    /orders                      # List orders
GET    /products/sales              # Product sales report
GET    /dashboard/stats             # Dashboard statistics
```

### Webhook Events

**Creator Events**
```json
{
  "event": "creator.created",
  "creator": {
    "wp_creator_id": 123,
    "name": "John Doe",
    "email": "john@example.com",
    "brand_slug": "brand-name"
  },
  "timestamp": "2024-01-15 10:30:00",
  "site_url": "https://your-wordpress-site.com"
}
```

**Order Sync Events**
```json
{
  "event": "order_created",
  "order_id": 456,
  "timestamp": "2024-01-15 10:30:00",
  "site_url": "https://your-wordpress-site.com"
}
```

---

## 💻 Usage

### Admin Dashboard

Access: `http://your-app.com/admin`

**Features:**
- Real-time statistics overview
- Recent orders list
- Creator performance metrics
- Quick sync actions

### Creator Management

1. **Add Creator**
   - Navigate to **Creators → Add New**
   - Fill in: Name, Email, Brand, Commission Rate
   - System syncs with WordPress automatically via webhook

2. **View Creator Details**
   - Click creator name
   - View: Orders, Products, Statistics

3. **Edit Creator**
   - Click **Edit** button
   - Update information
   - Changes sync to WordPress

### Order Management

1. **View Orders**
   - Navigate to **Orders**
   - Filter by: Status, Creator, Date Range
   - Search by: Order Number, Customer

2. **Order Details**
   - Click order number
   - View: Items, Customer Info, Creators, Payment Status

3. **Manual Sync**
   - Click **Sync Now** button in admin interface
   - Or run: `php artisan mpcrm:sync orders`

### Product Sales Reports

1. **Access Report**
   - Navigate to **Reports → Product Sales**

2. **Apply Filters**
   - Period: Today, Week, Month, Quarter, Year, Custom
   - Brand: Select specific brand
   - Stock Status: All, In Stock, Low Stock, Out of Stock
   - Sort: Sales, Quantity, Name

3. **Export Data**
   - Click **Export CSV**
   - Download filtered results

### Statistics & Analytics

**Available Reports:**
- Sales by Product
- Sales by Creator
- Sales by Brand
- Order Status Distribution
- Revenue Over Time

**Metrics:**
- Total Sales Amount
- Order Count
- Average Order Value
- Conversion Rate
- Top Performing Products
- Top Performing Creators

---

## 🛠️ Development

### Project Structure

```
wp-cs-crm/
├── app/
│   ├── Console/
│   │   └── Commands/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/       # Controllers
│   │   ├── Middleware/        # Middleware
│   │   └── Requests/          # Form requests
│   ├── Jobs/                  # Queue jobs
│   ├── Models/                # Eloquent models
│   └── Services/              # Business logic
├── config/                    # Configuration files
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/                 # Blade templates
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── console.php           # Console routes
├── storage/                   # Application storage
├── tests/                     # Tests
└── public/                    # Public assets
```

### Key Services

#### WordPressService
Handles all WordPress API communications.

```php
use App\Services\WordPressService;

$service = new WordPressService();
$creators = $service->getCreators();
$orders = $service->getOrders(['status' => 'completed']);
```

#### SyncOrdersJob
Queue job for order synchronization.

```php
use App\Jobs\SyncOrdersJob;

SyncOrdersJob::dispatch('incremental', false);
```

### Artisan Commands

```bash
# Sync commands
php artisan mpcrm:sync orders [--full] [--from=DATE]
php artisan mpcrm:sync products
php artisan mpcrm:sync creators
php artisan mpcrm:sync all

# Test commands
php artisan mpcrm:test-woocommerce
php artisan mpcrm:diagnose-orders
php artisan wordpress:test-connection
```

### Queue Workers

Start queue worker:
```bash
# Standard queue
php artisan queue:work

# Sync queue specifically
php artisan queue:work --queue=sync

# Process one job
php artisan queue:work --queue=sync --once
```

Process failed jobs:
```bash
# List failed jobs
php artisan queue:failed

# Retry specific job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all
```

### Cache Management

```bash
# Clear all cache
php artisan cache:clear

# Clear view cache
php artisan view:clear

# Clear config cache
php artisan config:clear
```

---

## 🧪 Testing

### Setup Test Environment

```bash
# Copy test environment
cp .env.testing.example .env.testing

# Create test database
php artisan migrate --env=testing
```

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/OrderSyncTest.php

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/
│   ├── OrderSyncTest.php
│   ├── CreatorManagementTest.php
│   └── WebhookTest.php
└── Unit/
    ├── Models/
    ├── Services/
    └── Jobs/
```

### Example Test

```php
public function test_order_synchronization()
{
    $response = $this->artisan('mpcrm:sync orders', ['--from' => '2024-01-01']);
    
    $response->assertExitCode(0);
    $this->assertDatabaseHas('orders', [
        'wp_order_id' => 123
    ]);
}
```

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up Redis for cache and queues
- [ ] Configure SSL certificate
- [ ] Set up queue workers with Supervisor
- [ ] Configure backup strategy
- [ ] Set up monitoring (Sentry, NewRelic)
- [ ] Enable log rotation
- [ ] Configure firewall rules
- [ ] Generate WooCommerce API keys
- [ ] Configure WordPress webhooks
- [ ] Test all connections

### Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Supervisor Configuration

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/wp-cs-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔧 Troubleshooting

### Common Issues

#### "401 Unauthorized" Error During Sync

**Problem:** WooCommerce API authentication failed

**Solution:**
1. Verify WooCommerce keys in `.env`:
   ```env
   WC_CONSUMER_KEY=ck_...
   WC_CONSUMER_SECRET=cs_...
   ```
2. Regenerate WooCommerce API keys if needed
3. Test connection: `php artisan mpcrm:test-woocommerce`
4. Ensure WordPress user has `manage_woocommerce` permission

#### MP Creator API Token Invalid

**Solution:**
1. Verify `WORDPRESS_API_TOKEN` in `.env`
2. Regenerate token in WordPress: **MP Creators → Settings → API Token Management**
3. Test connection: `php artisan wordpress:test-connection`

#### Webhook Not Receiving Events

**Solution:**
1. Check `WORDPRESS_WEBHOOK_SECRET` matches WordPress
2. Verify webhook URL is publicly accessible
3. For local development, use ngrok or expose
4. Check Laravel logs: `tail -f storage/logs/laravel.log`
5. Test webhook manually with Postman:
   ```bash
   curl -X POST https://your-app.com/api/webhooks/creator-created \
     -H "Content-Type: application/json" \
     -H "X-Webhook-Token: your_secret" \
     -d '{"event":"creator.created","creator":{"name":"Test"}}'
   ```

#### Database Connection Error

**Solution:**
```bash
# Check database credentials
php artisan config:clear

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

#### Queue Jobs Not Processing

**Solution:**
```bash
# Check queue worker is running
ps aux | grep queue

# Restart queue worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed

# Manually process queue
php artisan queue:work --once
```

#### Missing Orders After Sync

**Solution:**
1. Check WooCommerce API credentials are correct
2. Verify order status filter in sync command
3. Run full sync: `php artisan mpcrm:sync orders --full`
4. Check sync logs: `php artisan tinker` → `SyncLog::latest()->first()`
5. Enable debug mode and check logs

#### No Products Syncing

**Solution:**
1. Ensure products have `brand_slug` meta field in WordPress
2. Run: `php artisan mpcrm:sync products`
3. Check if products are published in WooCommerce
4. Verify WooCommerce API permissions

### Debug Mode

Enable detailed logging:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

View logs:
```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Filter for sync-related logs
tail -f storage/logs/laravel.log | grep sync

# Filter for errors
tail -f storage/logs/laravel.log | grep ERROR
```

### Configuration Verification

```bash
# Verify all configuration is loaded
php artisan tinker

>>> config('services.wordpress')
>>> config('services.wordpress.wc_key')
>>> config('services.wordpress.wc_secret')
```

---

## 📊 Performance Optimization

### Database Optimization

```sql
-- Add indexes for common queries
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_payment ON orders(payment_status);
CREATE INDEX idx_order_items_product ON order_items(wp_product_id);
CREATE INDEX idx_products_brand ON products(brand_slug);
CREATE INDEX idx_sync_logs_type ON sync_logs(sync_type);
CREATE INDEX idx_sync_logs_status ON sync_logs(status);
```

### Caching Strategy

```php
// Cache statistics for 1 hour
Cache::remember('dashboard_stats', 3600, function() {
    return [
        'total_sales' => Order::sum('total'),
        'order_count' => Order::count(),
        // ...
    ];
});
```

### Query Optimization

```php
// Eager load relationships
$orders = Order::with(['creators', 'items'])->get();

// Use select to limit columns
$products = Product::select('id', 'name', 'price')->get();

// Use chunk for large datasets
Order::chunk(100, function($orders) {
    foreach ($orders as $order) {
        // Process order
    }
});
```

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Use meaningful commit messages

### Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tests pass locally
- [ ] New tests added
- [ ] Manual testing performed

## Checklist
- [ ] Code follows project standards
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No new warnings
```

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Team

**BaanaBaana Boutique**
- Website: https://baanabaana.com
- Email: support@baanabaana.com
- GitHub: [@baanabaana](https://github.com/smartdev1/baana-baana-crm)

---

## 🙏 Acknowledgments

- Laravel Framework Team
- WooCommerce & WordPress Communities
- All Contributors

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [WooCommerce REST API](https://woocommerce.github.io/woocommerce-rest-api-docs/)

---

**Built with ❤️ by BaanaBaana Boutique**