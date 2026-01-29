# Charity Website - Laravel Backend

A fully-featured charity donation platform built with Laravel 10, Sanctum, and MySQL.

## 🚀 Features

- **User Authentication** (Registration/Login with Sanctum)
- **Campaign Management** (CRUD operations)
- **Donation System** with payment integration
- **API Endpoints** for frontend consumption
- **Email Notifications** (For donations and campaign updates, password resets)
- **File Uploads** for campaign images

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM (for asset compilation)

## 🛠️ Installation

1. **Clone the repository**
    ```bash
    git clone https://github.com/yourusername/charity-backend.git
    cd charity-backend
    Install dependencies
    ```

bash
composer install
npm install
Configure environment

bash
cp .env.example .env
php artisan key:generate
Update .env file

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=charity_app
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
Run migrations

bash
php artisan migrate
php artisan db:seed
Generate Sanctum keys

bash
php artisan passport:install # or for Sanctum
Start the server

bash
php artisan serve
🔧 API Documentation
Authentication Endpoints
POST /api/register - Register new user

POST /api/login - Login user

POST /api/logout - Logout user

GET /api/user - Get current user

Campaign Endpoints
GET /api/campaigns - List all campaigns

POST /api/campaigns - Create campaign

GET /api/campaigns/{id} - Get single campaign

PUT /api/campaigns/{id} - Update campaign

DELETE /api/campaigns/{id} - Delete campaign

Donation Endpoints
POST /api/campaigns/{id}/donate - Make donation

GET /api/donations - List user donations

🧪 Testing
bash
php artisan test
📁 Project Structure
text
app/
├── Http/
│ ├── Controllers/
│ │ ├── Api/ # API controllers
│ │ └── Web/ # Web controllers
│ ├── Middleware/ # Custom middleware
│ └── Requests/ # Form requests
├── Models/ # Eloquent models
├── Services/ # Business logic
└── Providers/ # Service providers

database/
├── migrations/ # Database migrations
├── seeders/ # Database seeders
└── factories/ # Model factories

routes/
├── api.php # API routes
├── web.php # Web routes
└── console.php # Artisan commands
🚀 Deployment
Production environment setup

Configure .env for production

Optimize application

bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
Setup queue worker (if using queues)

Configure web server (Nginx/Apache)

📄 License
This project is open-source and available under the MIT License.

👤 Author
Your Name

GitHub: @aghazain10

LinkedIn: https://www.linkedin.com/in/syed-zain-mujtaba/
