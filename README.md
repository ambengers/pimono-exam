# Pimono - Laravel Application
### Written By: Marvin Quezon

A small real-time money transfer app developed using Laravel 12 framework with Vue 3 single-page architecture, TypeScript, TailwindCSS, with real-time features using Pusher.

## Features

- **User Authentication & Account Management**
  - User registration and login
  - Password reset via email (with Mailtrap.io integration for local development)
  - Secure session management

- **Money Transfer System**
  - Send money to other users with searchable recipient selection
  - Automatic commission fee calculation
  - Real-time balance updates after transactions
  - Designed to handle high concurrency with database row-level locking
  - Automatic rollback on failed transactions to ensure data integrity

- **Transaction History**
  - View all sent and received transactions
  - Detailed transaction information including balances before/after
  - Paginated transaction list with expandable details
  - Real-time transaction notifications via Pusher

- **Dashboard**
  - View account information and current balance
  - Make new transactions
  - Browse transaction history with pagination
  - Real-time updates when receiving new transactions

## Local Development with Docker

This application is containerized with Docker for easy local development. The setup includes:

- **PHP 8.2 FPM** with required extensions
- **Nginx** web server
- **MySQL 8.0** database
- **Redis** for caching and queues
- **Supervisor** for managing queue workers

### Prerequisites

- Docker and Docker Compose installed
- `.env` file configured (see `.env.example`)

### Quick Start

#### Option 1: Automated Deployment (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd pimono-exam
   ```

2. **Create `.env` file**
   ```bash
   cp .env.example .env
   ```

3. **Configure environment variables**
   
   Edit `.env` and set the following:
   ```env
   APP_NAME=Pimono
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=pimono
   DB_USERNAME=pimono
   DB_PASSWORD=password
   
   QUEUE_CONNECTION=database
   BROADCAST_DRIVER=pusher
   
   PUSHER_APP_ID=your_pusher_app_id
   PUSHER_APP_KEY=your_pusher_key
   PUSHER_APP_SECRET=your_pusher_secret
   PUSHER_APP_CLUSTER=your_pusher_cluster
   
   VITE_PUSHER_APP_KEY=your_pusher_key
   VITE_PUSHER_APP_CLUSTER=your_pusher_cluster
   ```
   
   **Email Configuration (for Forgot Password functionality):**
   
   To enable email sending for password reset in local development, set up Mailtrap.io:
   
   1. **Sign up for Mailtrap.io** (free account available)
      - Visit [https://mailtrap.io](https://mailtrap.io)
      - Create a free account or sign in
   
   2. **Get your SMTP credentials**
      - After logging in, go to **Email Testing** → **Inboxes**
      - Select or create an inbox
      - Click on **SMTP Settings**
      - Select **Laravel** from the integration dropdown
      - Copy the following values:
        - **Host:** `smtp.mailtrap.io`
        - **Port:** `2525` (or `587` for TLS)
        - **Username:** (your Mailtrap username)
        - **Password:** (your Mailtrap password)
   
   3. **Update your `.env` file** with Mailtrap credentials:
      ```env
      MAIL_MAILER=smtp
      MAIL_HOST=smtp.mailtrap.io
      MAIL_PORT=2525
      MAIL_USERNAME=your_mailtrap_username
      MAIL_PASSWORD=your_mailtrap_password
      MAIL_ENCRYPTION=tls
      MAIL_FROM_ADDRESS=noreply@example.com
      MAIL_FROM_NAME="${APP_NAME}"
      ```
   
   **Note:** Without Mailtrap configuration, password reset emails will be logged to `storage/logs/laravel.log` instead of being sent. Mailtrap allows you to test emails safely in development without sending real emails.

4. **Run deployment script**
   ```bash
   ./docker/deploy.sh
   ```

5. **Seed test data (optional)**
   ```bash
   docker-compose exec app php artisan db:seed
   ```
   
   This creates 2 default test users:
   - `sender@example.com` (password: `password`) - Balance: $100,000,000
   - `receiver@example.com` (password: `password`) - Balance: $1,000,000
   
   Plus 100 additional random users and 10,000 sample transactions.

6. **Access the application**
   
   Open your browser and navigate to: `http://localhost:8000`

#### Option 2: Manual Deployment

1. **Clone and configure** (same as Option 1, steps 1-3, including email configuration)

2. **Build and start containers**
   ```bash
   docker-compose up -d --build
   ```

3. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate --force
   ```

4. **Optimize application**
   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

5. **Seed test data (optional)**
   ```bash
   docker-compose exec app php artisan db:seed
   ```
   
   This creates 2 default test users:
   - `sender@example.com` (password: `password`) - Balance: $100,000,000
   - `receiver@example.com` (password: `password`) - Balance: $1,000,000
   
   Plus 100 additional random users and 10,000 sample transactions.

6. **Access the application**
   
   Open your browser and navigate to: `http://localhost:8000`

### Docker Commands

**Start containers:**
```bash
docker-compose up -d
```

**Stop containers:**
```bash
docker-compose down
```

**View logs:**
```bash
docker-compose logs -f app
```

**Execute commands in container:**
```bash
docker-compose exec app php artisan <command>
```

**Run migrations:**
```bash
docker-compose exec app php artisan migrate
```

**Seed test data:**
```bash
docker-compose exec app php artisan db:seed
```

This will create:
- **2 default test users:**
  - `sender@example.com` (Bruce Wayne) - Balance: $100,000,000
  - `receiver@example.com` (Clark Kent) - Balance: $1,000,000
- **100 additional random users** with varying balances
- **10,000 sample transactions** between the default users

**Note:** The default password for test users is `password` (as defined in the UserFactory).

**Clear cache:**
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

**Restart queue workers:**
```bash
docker-compose restart app
```

### Connecting to MySQL with External Clients

The MySQL database is accessible from external MySQL clients like Sequel Pro, TablePlus, MySQL Workbench, etc.

**Connection Details:**
- **Host:** `127.0.0.1` or `localhost`
- **Port:** `3306`
- **Database:** `pimono` (or your `DB_DATABASE` value)
- **Username:** `pimono` (or your `DB_USERNAME` value)
- **Password:** `password` (or your `DB_PASSWORD` value)
- **Root Username:** `root`
- **Root Password:** `rootpassword` (or your `DB_ROOT_PASSWORD` value)

**Note:** Make sure the Docker containers are running before attempting to connect.

### Supervisor Configuration

The application uses Supervisor to manage multiple processes automatically:

- **PHP-FPM** - Handles PHP requests
- **Nginx** - Web server
- **2 Laravel Queue Workers** - Process background jobs with automatic restart

Queue workers are configured with:
- `--sleep=3` - Wait 3 seconds between jobs
- `--tries=3` - Retry failed jobs up to 3 times
- `--max-time=3600` - Restart worker after 1 hour
- Automatic restart on failure
- Logging to `/var/log/supervisor/laravel-queue.log`

All processes are automatically started when the container starts and will restart if they crash.

### Troubleshooting

**Check queue worker status:**
```bash
docker-compose exec app supervisorctl status
```

**View queue worker logs:**
```bash
docker-compose exec app tail -f /var/log/supervisor/laravel-queue.log
```

**Restart all services:**
```bash
docker-compose restart
```

**Rebuild from scratch:**
```bash
docker-compose down -v
docker-compose up -d --build
```

## Testing

```bash
docker-compose exec app php artisan test
```

## Sample Screenshots

**Login Page**

<img src=./login-page.png />

**Dashboard Page**

<img src=./dashboard-page.png />

**Transaction Modal**

<img src=./transaction-modal.png />

**Successful Transaction**

<img src=./successful-transaction.png />