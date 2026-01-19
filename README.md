# 🎂 Laravel Automated Birthday Message Sender

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)

A comprehensive Laravel-based web application that automatically sends personalized birthday SMS messages with custom digital birthday cards to your contacts. Built for SITC Campus, this system features automated scheduling, contact management, activity logging, and an interactive birthday card experience.

---

## 📋 Table of Contents

- [Features](#-features)
- [Demo](#-demo)
- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Scheduling](#-scheduling)
- [API Reference](#-api-reference)
- [Database Schema](#-database-schema)
- [Project Structure](#-project-structure)
- [Development](#-development)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [Security](#-security)
- [License](#-license)
- [Acknowledgments](#-acknowledgments)

---

## ✨ Features

### Core Functionality

- 🤖 **Automated Birthday Detection** - Automatically identifies and processes birthdays daily via scheduled tasks
- 📱 **SMS Integration** - Sends personalized birthday messages via HTTP SMS gateway API
- 🎨 **Dynamic Birthday Cards** - Generates custom digital birthday cards with recipient names
- 🔗 **Public Card URLs** - Each contact receives a unique, secure link to view their birthday card
- 🎉 **Interactive Card Experience** - Animated confetti effects and downloadable cards

### Contact Management

- ➕ **CRUD Operations** - Full Create, Read, Update, Delete functionality for contacts
- 📊 **CSV Import/Export** - Bulk import contacts from CSV files and export to CSV
- 🔍 **Advanced Search** - Search contacts by name or phone number
- 📑 **Sorting & Pagination** - Sort by multiple columns with persistent query parameters
- ✅ **Bulk Actions** - Select multiple contacts for batch deletion
- 🗑️ **Delete All** - Clear entire contact database with confirmation

### Administrative Features

- 🔐 **Simple Admin Authentication** - Session-based login system with environment credentials
- 📝 **Activity Logging** - Comprehensive logging of all system actions (SMS sent, imports, deletions, etc.)
- 📈 **Log Viewer** - Paginated activity logs with detailed payload inspection
- 🎯 **Manual Triggers** - Force birthday message sending for testing or special occasions
- 📧 **Individual Manual Send** - Send birthday messages to specific contacts on-demand

### User Experience

- 📱 **Responsive Design** - Mobile-first UI built with Tailwind CSS 4.x
- 🎭 **Clean Interface** - Modern, intuitive admin dashboard
- ⚡ **Fast Performance** - Optimized queries and asset compilation with Vite
- 🎁 **Gift Reveal Animation** - Engaging "tap to open" experience for birthday recipients
- 💾 **Card Download** - Recipients can download their personalized birthday cards

### Technical Features

- 🔄 **Duplicate Prevention** - Tracks last message year to prevent duplicate sends
- 🛡️ **Secure Tokens** - Random public tokens for secure, shareable card URLs
- 🖼️ **Dynamic Image Generation** - Server-side image manipulation with Intervention Image
- 📅 **Smart Name Handling** - Automatic name truncation and formatting for long names
- 🎨 **Custom Fonts** - Beautiful typography using custom font files
- 🔔 **SweetAlert2 Integration** - Professional confirmation dialogs and notifications

---

## 🎬 Demo

### Admin Dashboard

The admin panel provides a comprehensive interface for managing contacts:

- **Dashboard**: View, search, sort, and filter all contacts
- **Import**: Bulk upload contacts via CSV files
- **Export**: Download contact database as CSV
- **Manual Triggers**: Send birthday messages on-demand
- **Activity Logs**: Monitor all system activities with detailed payloads

### Birthday Card Experience

Recipients receive an SMS with a unique URL that leads to:

1. **Surprise Screen**: "You have a surprise! 🎁" with a tap-to-open button
2. **Card Reveal**: Animated confetti celebration with personalized birthday card
3. **Download Option**: Button to save the card image to their device

### Example Birthday Message

```
HAPPY BIRTHDAY JOHN DOE!

SITC Campus wishes you a year filled with success, knowledge and new opportunities.

Your Birthday Card: http://yourapp.com/birthday/abc123xyz

Keep learning, growing and shining bright!
```

---

## 🛠️ Tech Stack

### Backend

- **Framework**: Laravel 10.x
- **Language**: PHP 8.1+
- **Database**: SQLite (easily swappable to MySQL/PostgreSQL)
- **Queue System**: Database driver
- **Session Storage**: Database driver
- **Task Scheduler**: Laravel Scheduler (Cron)

### Frontend

- **CSS Framework**: Tailwind CSS 4.x
- **Build Tool**: Vite 7.x
- **JavaScript**: Vanilla JS with modern ES6+ features
- **Confetti Animation**: canvas-confetti library
- **Alerts**: SweetAlert2

### Libraries & Packages

- **Image Processing**: Intervention Image 3.x (GD Driver)
- **HTTP Client**: Guzzle HTTP
- **Date Handling**: Carbon (included with Laravel)
- **Development**: Laravel Sail, Laravel Pint, PHPUnit

### External Services

- **SMS Gateway**: HTTP-based SMS API (configurable)

---

## 🏗️ Architecture

### System Flow

```
┌─────────────────────────────────────────────────────────────┐
│                     Laravel Scheduler                        │
│              (Runs hourly: birthday:send)                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│           SendBirthdayMessages Command                       │
│  - Queries contacts with today's birthday                    │
│  - Filters out already-sent-this-year                        │
│  - Generates unique tokens if missing                        │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    SmsService                                │
│  - Sends HTTP request to SMS Gateway API                     │
│  - Logs success/failure to ActivityLog                       │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Contact Receives SMS                            │
│  - Message includes unique URL                               │
│  - URL: /birthday/{public_token}                            │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│           BirthdayController::show                           │
│  - Displays surprise screen with "Tap to Open"              │
│  - JavaScript reveals card with confetti animation           │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         BirthdayController::card (Image)                     │
│  - Loads base birthday card image                            │
│  - Overlays contact's name using custom font                 │
│  - Returns JPEG image dynamically                            │
└─────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
app/
├── Console/
│   ├── Commands/
│   │   └── SendBirthdayMessages.php    # Birthday SMS sending command
│   └── Kernel.php                      # Task scheduling configuration
├── Http/
│   ├── Controllers/
│   │   ├── ActivityLogController.php   # Log viewing
│   │   ├── BirthdayController.php      # Public birthday card views
│   │   ├── ContactController.php       # Contact CRUD & bulk operations
│   │   └── LoginController.php         # Admin authentication
│   └── Middleware/
│       └── AdminAuthMiddleware.php     # Session-based auth guard
├── Models/
│   ├── ActivityLog.php                 # Activity logging model
│   ├── Contact.php                     # Contact model with DOB handling
│   └── User.php                        # Standard Laravel user model
└── Services/
    ├── LoggerService.php               # Centralized activity logging
    └── SmsService.php                  # SMS gateway integration

database/
├── migrations/
│   ├── 2025_12_15_080530_create_contacts_table.php
│   ├── 2025_12_15_084958_add_public_token_to_contacts_table.php
│   └── 2025_12_18_063949_create_activity_logs_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── css/
│   └── app.css                         # Tailwind CSS imports
├── js/
│   ├── app.js                          # Main JavaScript entry
│   └── bootstrap.js                    # Axios configuration
└── views/
    ├── auth/
    │   └── login.blade.php             # Admin login page
    ├── birthday/
    │   └── show.blade.php              # Public birthday card page
    ├── contacts/
    │   ├── index.blade.php             # Contact listing & management
    │   ├── create.blade.php            # Add new contact form
    │   └── edit.blade.php              # Edit contact form
    ├── logs/
    │   └── index.blade.php             # Activity log viewer
    └── layouts/
        └── app.blade.php               # Base layout template

public/
├── fonts/
│   └── EmilysCandy-Regular.ttf        # Custom font for birthday cards
└── images/
    └── SITC Birthday Card.jpg          # Base birthday card template

routes/
├── web.php                             # Web routes (admin & public)
├── api.php                             # API routes (unused)
└── console.php                         # Console routes/closures
```

---

## 📦 Prerequisites

Before you begin, ensure you have met the following requirements:

### Required Software

- **PHP**: >= 8.1 with extensions:
    - OpenSSL
    - PDO
    - Mbstring
    - Tokenizer
    - XML
    - Ctype
    - JSON
    - BCMath
    - Fileinfo
    - GD (for image manipulation)
- **Composer**: >= 2.0
- **Node.js**: >= 18.x
- **npm**: >= 9.x or **Yarn**: >= 1.22
- **SQLite**: >= 3.8.8 (or MySQL/PostgreSQL for production)

### Optional Tools

- **Git**: For version control
- **PHP 8.1/8.2/8.3**: Any version >= 8.1 is supported
- **Laravel Sail**: For Docker-based development environment

### System Requirements

- **Memory**: Minimum 512MB RAM
- **Disk Space**: Minimum 500MB free space
- **Operating System**: Linux, macOS, or Windows 10+

---

## 🚀 Installation

### Quick Start (Automated Setup)

```bash
# Clone the repository
git clone https://github.com/codezelat/laravel-automated-birthday-message-sender.git
cd laravel-automated-birthday-message-sender

# Run the automated setup script
composer run setup
```

This will:

- Install PHP dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run database migrations
- Install Node.js dependencies
- Build frontend assets

### Manual Installation

If you prefer manual setup or the automated script fails:

#### 1. Clone the Repository

```bash
git clone https://github.com/codezelat/laravel-automated-birthday-message-sender.git
cd laravel-automated-birthday-message-sender
```

#### 2. Install PHP Dependencies

```bash
composer install
```

#### 3. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Database Setup

```bash
# Create SQLite database file (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate
```

#### 5. Install Frontend Dependencies

```bash
npm install
```

#### 6. Build Assets

```bash
# For development
npm run dev

# For production
npm run build
```

#### 7. Create Required Directories

```bash
# Ensure storage and cache directories are writable
chmod -R 775 storage bootstrap/cache
```

---

## ⚙️ Configuration

### Environment Variables

Edit your `.env` file with the following configurations:

#### Application Settings

```env
APP_NAME="Birthday Automator"
APP_ENV=local                    # Change to 'production' for live
APP_KEY=base64:...               # Generated automatically
APP_DEBUG=true                   # Set to false in production
APP_URL=http://localhost:8000    # Your application URL
```

#### Admin Credentials

```env
ADMIN_USERNAME=admin             # Change to secure username
ADMIN_PASSWORD=secure_password   # Change to strong password
```

⚠️ **Important**: Change default admin credentials before deploying to production!

#### Database Configuration

**For SQLite (Default):**

```env
DB_CONNECTION=sqlite
# DB_DATABASE will default to database/database.sqlite
```

**For MySQL:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=birthday_sender
DB_USERNAME=root
DB_PASSWORD=your_password
```

**For PostgreSQL:**

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=birthday_sender
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

#### SMS Gateway Configuration

```env
SMS_USERNAME=your_sms_gateway_username
SMS_PASSWORD=your_sms_gateway_password
SMS_SOURCE=SITC                  # Sender ID (alphanumeric)
SMS_API_URL=https://your-sms-gateway-api-url.com/api/send
```

#### Queue & Session Configuration

```env
QUEUE_CONNECTION=database        # Use 'redis' for better performance
SESSION_DRIVER=database          # Store sessions in database
SESSION_LIFETIME=120             # Session timeout in minutes
```

#### Mail Configuration (Optional)

```env
MAIL_MAILER=log                  # Use 'smtp' for actual email sending
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### SMS Gateway Integration

This application uses a generic HTTP-based SMS gateway. Configure your SMS provider settings in the `SmsService` class if needed.

#### Supported Parameters (Default)

- `username`: API username
- `password`: API password
- `src`: Sender ID/source
- `dst`: Destination phone number
- `msg`: Message content
- `dr`: Delivery report (1 = enabled)

#### Custom SMS Providers

To integrate a different SMS provider, modify [app/Services/SmsService.php](app/Services/SmsService.php):

```php
// Example: Twilio Integration
public function send($phone, $message)
{
    $response = Http::withBasicAuth($this->username, $this->password)
        ->asForm()
        ->post('https://api.twilio.com/2010-04-01/Accounts/YOUR_SID/Messages.json', [
            'From' => $this->source,
            'To' => $phone,
            'Body' => $message,
        ]);
    // ... handle response
}
```

### Customizing Birthday Cards

#### Change Base Image

Replace [public/images/SITC Birthday Card.jpg](public/images/SITC Birthday Card.jpg) with your custom birthday card image.

#### Change Font

Replace [public/fonts/EmilysCandy-Regular.ttf](public/fonts/EmilysCandy-Regular.ttf) with your preferred font file.

#### Modify Card Design

Edit [app/Http/Controllers/BirthdayController.php](app/Http/Controllers/BirthdayController.php) in the `getBirthdayCardImage()` method:

```php
private function getBirthdayCardImage($contact)
{
    $manager = new ImageManager(new Driver());
    $image = $manager->read(public_path('images/SITC Birthday Card.jpg'));

    $name = strtoupper($contact->short_name);

    // Customize text properties
    $image->text($name, $x, $y, function ($font) {
        $font->filename(public_path('fonts/YourFont.ttf'));
        $font->color('#FF0000');     // Change color
        $font->size(60);             // Change size
        $font->align('center');
        $font->valign('middle');
    });

    return $image;
}
```

### Customizing Birthday Message

Edit the message template in [app/Console/Commands/SendBirthdayMessages.php](app/Console/Commands/SendBirthdayMessages.php):

```php
$message = "HAPPY BIRTHDAY {$name}!\n\n"
    . "Your custom message here.\n\n"
    . "Your Birthday Card: {$url}\n\n"
    . "Custom footer text!";
```

---

## 📖 Usage

### Starting the Application

#### Development Server

```bash
# Start Laravel development server
php artisan serve

# In a separate terminal, start Vite dev server (for hot reloading)
npm run dev

# Or use the all-in-one development command
composer run dev
```

The application will be available at `http://localhost:8000`

#### Queue Worker

For processing background jobs (recommended):

```bash
php artisan queue:listen
```

### Admin Login

1. Navigate to `http://localhost:8000`
2. You'll be redirected to the login page
3. Enter your admin credentials (from `.env`):
    - Default username: `admin`
    - Default password: `secure_password` (⚠️ change this!)

### Managing Contacts

#### Adding a Single Contact

1. Click **"Add Contact"** button
2. Fill in the form:
    - **Name**: Full name of the contact
    - **Phone**: Phone number (include country code, e.g., +94771234567)
    - **Date of Birth**: YYYY-MM-DD format
3. Click **"Create"**

#### Importing Contacts from CSV

1. Prepare your CSV file with the following format:

    ```csv
    Name,Phone,DOB
    John Doe,+94771234567,1990-05-15
    Jane Smith,+94777654321,1985-08-22
    ```

2. Click **"Import CSV"** in the import section
3. Select your CSV file
4. Click **"Import"**

#### Searching & Filtering

- Use the search bar to find contacts by name or phone number
- Click column headers to sort (Name, Phone, DOB, Last Message)
- Sorting persists across page refreshes

#### Bulk Operations

- Check the checkbox next to contacts you want to delete
- Click **"Delete Selected"** button
- Confirm the action

#### Delete All Contacts

- Click **"Delete All"** button
- Confirm the dangerous action
- All contacts will be permanently removed

#### Exporting Contacts

- Click **"Export CSV"** button
- Your browser will download `contacts.csv` with all contact data

### Sending Birthday Messages

#### Automatic Sending

Birthday messages are sent automatically based on your scheduler configuration (see [Scheduling](#-scheduling) section).

#### Manual Trigger (Send Today's Messages)

1. Click **"Send Today's Messages"** button
2. Confirm the action
3. The system will immediately check for today's birthdays and send messages
4. Check the **Activity Logs** for results

#### Manual Single Send

1. Find a contact in the list
2. Click **"Send Manual"** next to their name
3. Confirm the action
4. An SMS will be sent immediately (bypasses year check)

### Viewing Activity Logs

1. Click **"View Logs"** button
2. Browse paginated log entries
3. Click **"View"** on any log entry to see detailed information:
    - Timestamp
    - Action type
    - Description
    - Status (success/error/info)
    - Full payload (JSON data)

### Birthday Card Experience (Recipient View)

When a recipient clicks the URL from their SMS:

1. **Landing Page**: Shows "You have a surprise! 🎁" with a button
2. **Tap to Open**: Recipient clicks/taps the button
3. **Card Reveal**: Animated confetti fills the screen, personalized card appears
4. **Download**: Recipient can download the card image by clicking "Download Card"

---

## ⏰ Scheduling

### Setting Up the Scheduler

The application uses Laravel's task scheduler to automatically send birthday messages. The scheduler needs to run continuously via a system cron job or process manager.

#### Linux/macOS Cron Setup

1. Open your crontab:

    ```bash
    crontab -e
    ```

2. Add this line:

    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

3. Replace `/path-to-your-project` with your actual project path

#### Windows Task Scheduler

1. Open Task Scheduler
2. Create a new task:
    - **Trigger**: Every 1 minute
    - **Action**: Start a program
    - **Program**: `C:\path\to\php.exe`
    - **Arguments**: `artisan schedule:run`
    - **Start in**: `C:\path\to\your\project`

### Schedule Configuration

The scheduler is configured in [app/Console/Kernel.php](app/Console/Kernel.php):

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('birthday:send')->hourly();
}
```

**Default**: Runs every hour to check for birthdays and send messages.

#### Customizing Schedule Frequency

You can modify the frequency in `app/Console/Kernel.php`:

```php
// Run every hour (default)
$schedule->command('birthday:send')->hourly();

// Run daily at midnight
$schedule->command('birthday:send')->dailyAt('00:00');

// Run every 30 minutes
$schedule->command('birthday:send')->everyThirtyMinutes();

// Run daily at 8 AM
$schedule->command('birthday:send')->dailyAt('08:00');

// Run on specific days
$schedule->command('birthday:send')
    ->dailyAt('09:00')
    ->days([1, 2, 3, 4, 5]); // Monday to Friday only
```

### Manual Command Execution

You can manually trigger the birthday check command:

```bash
php artisan birthday:send
```

This will:

1. Find all contacts with today's birthday
2. Filter out those who already received a message this year
3. Send SMS to eligible contacts
4. Update `last_message_sent_year` to current year
5. Log all activities

### Duplicate Prevention

The system automatically prevents sending duplicate messages:

- Tracks `last_message_sent_year` for each contact
- Skips contacts who already received a message in the current year
- Manual sends bypass this check (for testing or special occasions)

---

## 🔌 API Reference

### Public Routes

#### Birthday Card View

```
GET /birthday/{token}
```

Display the birthday surprise page with confetti animation.

**Parameters:**

- `token` (string, required): Unique public token for the contact

**Response:** HTML page with interactive birthday card experience

---

#### Birthday Card Image

```
GET /birthday/{token}/card
```

Generate and return the birthday card image with the contact's name.

**Parameters:**

- `token` (string, required): Unique public token for the contact

**Response:** JPEG image (Content-Type: image/jpeg)

---

#### Birthday Card Download

```
GET /birthday/{token}/download
```

Download the birthday card image as a file.

**Parameters:**

- `token` (string, required): Unique public token for the contact

**Response:** JPEG image download (filename: birthday-card.jpg)

---

### Admin Routes (Authentication Required)

#### Login

```
POST /login
```

Authenticate admin user.

**Body:**

```json
{
    "username": "admin",
    "password": "secure_password"
}
```

**Response:** Redirect to contacts page or error message

---

#### Logout

```
POST /logout
```

End admin session.

**Response:** Redirect to login page

---

#### List Contacts

```
GET /contacts
```

Display paginated list of contacts with search and sort.

**Query Parameters:**

- `search` (string, optional): Search term for name/phone
- `sort_by` (string, optional): Column to sort by (name, phone, dob, last_message_sent_year, created_at)
- `sort_direction` (string, optional): Sort direction (asc, desc)
- `page` (integer, optional): Page number

**Response:** HTML page with contact listing

---

#### Create Contact

```
POST /contacts
```

Add a new contact.

**Body:**

```json
{
    "name": "John Doe",
    "phone": "+94771234567",
    "dob": "1990-05-15"
}
```

**Response:** Redirect to contacts list with success message

---

#### Update Contact

```
PUT /contacts/{contact}
```

Update an existing contact.

**Parameters:**

- `contact` (integer, required): Contact ID

**Body:**

```json
{
    "name": "John Doe Updated",
    "phone": "+94771234567",
    "dob": "1990-05-16"
}
```

**Response:** Redirect to contacts list with success message

---

#### Delete Contact

```
DELETE /contacts/{contact}
```

Delete a single contact.

**Parameters:**

- `contact` (integer, required): Contact ID

**Response:** Redirect to contacts list with success message

---

#### Import Contacts

```
POST /contacts/import
```

Bulk import contacts from CSV file.

**Body:** multipart/form-data

- `file` (file, required): CSV file (max 2MB)

**CSV Format:**

```csv
Name,Phone,DOB
John Doe,+94771234567,1990-05-15
```

**Response:** Redirect to contacts list with success message

---

#### Export Contacts

```
GET /contacts/export/csv
```

Export all contacts to CSV file.

**Response:** CSV file download

---

#### Send Today's Messages

```
POST /contacts/send-today
```

Manually trigger birthday message sending for today's birthdays.

**Response:** Redirect to contacts list with success message

---

#### Bulk Delete Contacts

```
POST /contacts/bulk-delete
```

Delete multiple contacts at once.

**Body:**

```json
{
    "ids": [1, 2, 3, 4]
}
```

**Response:** Redirect to contacts list with success message

---

#### Delete All Contacts

```
POST /contacts/delete-all
```

Delete all contacts from database.

**Response:** Redirect to contacts list with success message

---

#### Send Manual Message

```
POST /contacts/{contact}/send-manual
```

Send birthday message to a specific contact immediately.

**Parameters:**

- `contact` (integer, required): Contact ID

**Response:** Redirect back with success/error message

---

#### View Activity Logs

```
GET /logs
```

Display paginated activity logs.

**Response:** HTML page with log entries

---

## 🗄️ Database Schema

### Tables

#### `contacts`

Stores contact information and birthday data.

| Column                   | Type         | Description                          |
| ------------------------ | ------------ | ------------------------------------ |
| `id`                     | BIGINT       | Primary key                          |
| `name`                   | VARCHAR(255) | Full name of contact                 |
| `phone`                  | VARCHAR(20)  | Phone number                         |
| `public_token`           | VARCHAR(64)  | Unique token for public card URL     |
| `dob`                    | DATE         | Date of birth                        |
| `last_message_sent_year` | INTEGER      | Year when last birthday SMS was sent |
| `created_at`             | TIMESTAMP    | Record creation timestamp            |
| `updated_at`             | TIMESTAMP    | Record update timestamp              |

**Indexes:**

- Primary key on `id`
- Unique index on `public_token`

---

#### `activity_logs`

Stores all system activity for auditing and debugging.

| Column        | Type         | Description                                  |
| ------------- | ------------ | -------------------------------------------- |
| `id`          | BIGINT       | Primary key                                  |
| `action`      | VARCHAR(255) | Action type (e.g., "SMS Sent", "Import")     |
| `description` | TEXT         | Detailed description of the action           |
| `status`      | VARCHAR(255) | Status (success, error, info, warning)       |
| `payload`     | JSON         | Additional data (contact details, responses) |
| `created_at`  | TIMESTAMP    | When the activity occurred                   |
| `updated_at`  | TIMESTAMP    | Record update timestamp                      |

**Indexes:**

- Primary key on `id`
- Index on `created_at` for efficient sorting

---

#### `users`

Standard Laravel users table (for future extensions).

| Column              | Type         | Description             |
| ------------------- | ------------ | ----------------------- |
| `id`                | BIGINT       | Primary key             |
| `name`              | VARCHAR(255) | User's full name        |
| `email`             | VARCHAR(255) | Email address (unique)  |
| `email_verified_at` | TIMESTAMP    | Email verification time |
| `password`          | VARCHAR(255) | Hashed password         |
| `remember_token`    | VARCHAR(100) | Remember me token       |
| `created_at`        | TIMESTAMP    | Account creation time   |
| `updated_at`        | TIMESTAMP    | Record update timestamp |

**Indexes:**

- Primary key on `id`
- Unique index on `email`

---

### Relationships

- **Contacts**: No explicit relationships (self-contained)
- **ActivityLogs**: No explicit relationships (self-contained)
- **Users**: Not currently used in authentication (session-based admin login instead)

---

## 📁 Project Structure

```
laravel-automated-birthday-message-sender/
├── app/                          # Application core code
│   ├── Console/
│   │   ├── Commands/             # Artisan commands
│   │   │   └── SendBirthdayMessages.php
│   │   └── Kernel.php            # Scheduler configuration
│   ├── Exceptions/               # Exception handlers
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/          # Request handlers
│   │   │   ├── ActivityLogController.php
│   │   │   ├── BirthdayController.php
│   │   │   ├── ContactController.php
│   │   │   ├── Controller.php
│   │   │   └── LoginController.php
│   │   ├── Middleware/           # HTTP middleware
│   │   │   └── AdminAuthMiddleware.php
│   │   └── Kernel.php            # HTTP kernel
│   ├── Models/                   # Eloquent models
│   │   ├── ActivityLog.php
│   │   ├── Contact.php
│   │   └── User.php
│   ├── Providers/                # Service providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── Services/                 # Business logic services
│       ├── LoggerService.php
│       └── SmsService.php
├── bootstrap/                    # Framework bootstrap files
│   ├── app.php
│   └── cache/
├── config/                       # Configuration files
│   ├── app.php
│   ├── database.php
│   ├── queue.php
│   ├── services.php
│   └── ...
├── database/                     # Database files
│   ├── database.sqlite           # SQLite database (created on install)
│   ├── factories/                # Model factories
│   │   └── UserFactory.php
│   ├── migrations/               # Database migrations
│   │   ├── 2025_12_15_080530_create_contacts_table.php
│   │   ├── 2025_12_15_084958_add_public_token_to_contacts_table.php
│   │   └── 2025_12_18_063949_create_activity_logs_table.php
│   └── seeders/                  # Database seeders
│       └── DatabaseSeeder.php
├── public/                       # Public web root
│   ├── build/                    # Compiled assets (Vite output)
│   ├── fonts/                    # Custom fonts
│   │   └── EmilysCandy-Regular.ttf
│   ├── images/                   # Static images
│   │   └── SITC Birthday Card.jpg
│   ├── index.php                 # Entry point
│   └── robots.txt
├── resources/                    # Raw assets and views
│   ├── css/
│   │   └── app.css               # Tailwind CSS entry
│   ├── js/
│   │   ├── app.js                # JavaScript entry
│   │   └── bootstrap.js          # Axios config
│   └── views/                    # Blade templates
│       ├── auth/
│       │   └── login.blade.php
│       ├── birthday/
│       │   └── show.blade.php
│       ├── contacts/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── logs/
│       │   └── index.blade.php
│       └── layouts/
│           └── app.blade.php
├── routes/                       # Route definitions
│   ├── api.php                   # API routes
│   ├── console.php               # Console routes
│   └── web.php                   # Web routes
├── storage/                      # Storage files
│   ├── app/                      # Application storage
│   ├── framework/                # Framework storage
│   └── logs/                     # Application logs
├── tests/                        # Automated tests
│   ├── Feature/
│   └── Unit/
├── vendor/                       # Composer dependencies
├── .env                          # Environment configuration (create from .env.example)
├── .env.example                  # Example environment file
├── artisan                       # Artisan CLI
├── composer.json                 # PHP dependencies
├── composer.lock                 # PHP dependency lock
├── package.json                  # Node.js dependencies
├── package-lock.json             # Node.js dependency lock
├── phpunit.xml                   # PHPUnit configuration
├── README.md                     # This file
└── vite.config.js                # Vite build configuration
```

---

## 💻 Development

### Running in Development Mode

```bash
# Start all development servers simultaneously
composer run dev
```

This command starts:

- Laravel development server (port 8000)
- Queue worker
- Vite dev server (hot reload)

Or run each separately:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:listen --tries=1

# Terminal 3: Vite dev server
npm run dev
```

### Code Quality Tools

#### Laravel Pint (Code Formatting)

```bash
# Format all code to Laravel standards
./vendor/bin/pint

# Check without fixing
./vendor/bin/pint --test
```

#### Laravel Tinker (REPL)

```bash
# Interactive PHP shell with loaded application
php artisan tinker

# Examples:
>>> App\Models\Contact::count()
>>> App\Models\Contact::whereMonth('dob', now()->month)->get()
>>> \App\Services\SmsService::send('+94771234567', 'Test message')
```

### Clearing Caches

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Or clear everything at once
php artisan optimize:clear
```

### Database Operations

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback and re-run all migrations
php artisan migrate:refresh

# Rollback all and re-seed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Logging

Application logs are stored in `storage/logs/`:

```bash
# View real-time logs
tail -f storage/logs/laravel.log

# Clear log file
echo "" > storage/logs/laravel.log
```

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Or using PHPUnit directly
./vendor/bin/phpunit

# Run specific test file
php artisan test tests/Feature/ContactTest.php

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

### Writing Tests

Create new tests:

```bash
# Feature test (HTTP, database, etc.)
php artisan make:test ContactControllerTest

# Unit test (pure PHP logic)
php artisan make:test ContactServiceTest --unit
```

Example test structure:

```php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_contacts()
    {
        Contact::factory()->count(5)->create();

        $response = $this->get('/contacts');

        $response->assertStatus(200);
        $response->assertViewHas('contacts');
    }
}
```

### Continuous Integration

Example GitHub Actions workflow (`.github/workflows/tests.yml`):

```yaml
name: Tests

on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v3
            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: 8.1
            - name: Install Dependencies
              run: composer install
            - name: Run Tests
              run: php artisan test
```

---

## 🚀 Deployment

### Production Checklist

Before deploying to production:

- [ ] Change `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY` with `php artisan key:generate`
- [ ] Update `APP_URL` to your production domain
- [ ] Change admin username and password
- [ ] Configure SMS gateway credentials
- [ ] Use MySQL/PostgreSQL instead of SQLite
- [ ] Set up Redis for cache and queue (recommended)
- [ ] Configure proper mail driver
- [ ] Enable HTTPS/SSL
- [ ] Set up proper file permissions
- [ ] Configure backup strategy
- [ ] Set up monitoring and error tracking

### Server Requirements

**Web Server Options:**

- Apache 2.4+ with mod_rewrite
- Nginx 1.18+
- Caddy 2.0+

**PHP Extensions Required:**

- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD

### Deployment Steps

#### 1. Server Setup (Ubuntu/Debian Example)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1
sudo apt install php8.1 php8.1-fpm php8.1-cli php8.1-mbstring \
    php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-sqlite3 \
    php8.1-mysql -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

#### 2. Clone and Configure

```bash
# Clone repository
cd /var/www
git clone https://github.com/codezelat/laravel-automated-birthday-message-sender.git
cd laravel-automated-birthday-message-sender

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/laravel-automated-birthday-message-sender
sudo chmod -R 775 storage bootstrap/cache

# Environment configuration
cp .env.example .env
nano .env  # Edit configuration
php artisan key:generate
php artisan migrate --force
```

#### 3. Web Server Configuration

**Nginx Configuration** (`/etc/nginx/sites-available/birthday-sender`):

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/laravel-automated-birthday-message-sender/public;

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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/birthday-sender /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 4. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal is set up automatically
```

#### 5. Process Manager (Supervisor)

For queue workers, create `/etc/supervisor/conf.d/birthday-sender-worker.conf`:

```ini
[program:birthday-sender-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-automated-birthday-message-sender/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/laravel-automated-birthday-message-sender/storage/logs/worker.log
stopwaitsecs=3600
```

Activate:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start birthday-sender-worker:*
```

#### 6. Scheduler Cron

Add to crontab (`sudo crontab -e`):

```bash
* * * * * cd /var/www/laravel-automated-birthday-message-sender && php artisan schedule:run >> /dev/null 2>&1
```

### Docker Deployment

Create `Dockerfile`:

```dockerfile
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 storage bootstrap/cache

CMD php artisan serve --host=0.0.0.0 --port=8000
```

Build and run:

```bash
docker build -t birthday-sender .
docker run -d -p 8000:8000 birthday-sender
```

### Performance Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
```

---

## 🔧 Troubleshooting

### Common Issues

#### Permission Denied Errors

```bash
# Fix storage and cache permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Database Connection Errors

- **SQLite**: Ensure `database/database.sqlite` exists and is writable
- **MySQL**: Verify credentials in `.env` and ensure database exists
- Test connection:
    ```bash
    php artisan migrate:status
    ```

#### Scheduler Not Running

- Verify cron job is active:
    ```bash
    crontab -l
    ```
- Check cron logs:
    ```bash
    grep CRON /var/log/syslog
    ```
- Test scheduler manually:
    ```bash
    php artisan schedule:run
    ```

#### SMS Not Sending

1. Check SMS credentials in `.env`
2. Review logs: `storage/logs/laravel.log`
3. Check Activity Logs in admin panel for detailed error messages
4. Test SMS service manually:
    ```bash
    php artisan tinker
    >>> $sms = new \App\Services\SmsService();
    >>> $sms->send('+94771234567', 'Test message');
    ```

#### Birthday Card Image Not Displaying

- Ensure GD extension is installed:
    ```bash
    php -m | grep -i gd
    ```
- Check image and font files exist:
    ```bash
    ls -lh public/images/
    ls -lh public/fonts/
    ```
- Verify file permissions:
    ```bash
    chmod 644 public/images/* public/fonts/*
    ```

#### "Mix Manifest Not Found" Error

This occurs when assets aren't compiled:

```bash
npm install
npm run build
```

#### Session Expired / Not Authenticated

- Clear sessions:
    ```bash
    php artisan session:flush
    ```
- Check session driver in `.env` (should be `database`)
- Ensure sessions table exists:
    ```bash
    php artisan migrate
    ```

### Debug Mode

Enable debug mode for detailed error messages (development only):

```env
APP_DEBUG=true
```

View detailed error traces in `storage/logs/laravel.log`

### Getting Help

If you encounter issues not covered here:

1. Check application logs: `storage/logs/laravel.log`
2. Check web server logs:
    - Nginx: `/var/log/nginx/error.log`
    - Apache: `/var/log/apache2/error.log`
3. Enable query logging to debug database issues:
    ```php
    // In app/Providers/AppServiceProvider.php boot() method
    \DB::listen(function($query) {
        \Log::info($query->sql, $query->bindings);
    });
    ```
4. Use `php artisan tinker` to test code interactively
5. Check the [Laravel Documentation](https://laravel.com/docs)
6. Open an issue on GitHub (provide error logs and steps to reproduce)

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

### How to Contribute

1. **Fork the Repository**

    ```bash
    git clone https://github.com/codezelat/laravel-automated-birthday-message-sender.git
    cd laravel-automated-birthday-message-sender
    ```

2. **Create a Feature Branch**

    ```bash
    git checkout -b feature/your-feature-name
    ```

3. **Make Your Changes**
    - Write clean, documented code
    - Follow Laravel coding standards (use Laravel Pint)
    - Add tests for new features
    - Update documentation as needed

4. **Run Code Quality Checks**

    ```bash
    ./vendor/bin/pint           # Format code
    php artisan test            # Run tests
    ```

5. **Commit Your Changes**

    ```bash
    git add .
    git commit -m "feat: add your feature description"
    ```

    Use [Conventional Commits](https://www.conventionalcommits.org/):
    - `feat:` - New features
    - `fix:` - Bug fixes
    - `docs:` - Documentation changes
    - `style:` - Code style changes (formatting)
    - `refactor:` - Code refactoring
    - `test:` - Test additions/changes
    - `chore:` - Maintenance tasks

6. **Push to Your Fork**

    ```bash
    git push origin feature/your-feature-name
    ```

7. **Open a Pull Request**
    - Go to the original repository
    - Click "New Pull Request"
    - Select your feature branch
    - Describe your changes clearly

### Contribution Ideas

- 🌍 **Multi-language support** - Internationalization (i18n)
- 📧 **Email integration** - Send emails alongside SMS
- 📊 **Analytics dashboard** - Visualize birthday statistics
- 🔔 **WhatsApp integration** - Send messages via WhatsApp Business API
- 👥 **Multiple admin users** - User management system
- 📱 **Mobile app** - React Native or Flutter companion app
- 🎨 **Template system** - Multiple birthday card designs
- 🔄 **API endpoints** - RESTful API for external integrations
- ⏰ **Custom timing** - Per-contact preferred send time
- 📝 **Custom messages** - Per-contact personalized messages

### Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on the code, not the person
- Help others learn and grow

---

## 🔒 Security

### Reporting Security Vulnerabilities

If you discover a security vulnerability, please **DO NOT** open a public issue. Instead:

1. Email: your-email@example.com
2. Include:
    - Description of the vulnerability
    - Steps to reproduce
    - Potential impact
    - Suggested fix (if any)

We will respond within 48 hours and work with you to resolve the issue.

### Security Best Practices

#### Default Credentials

⚠️ **CRITICAL**: Change default admin credentials immediately after installation!

```env
ADMIN_USERNAME=your_secure_username
ADMIN_PASSWORD=your_very_strong_password_here
```

#### Environment File

- Never commit `.env` file to version control
- Keep `.env` file permissions restrictive: `chmod 600 .env`
- Use strong, unique passwords for all services

#### Database Security

- Use prepared statements (Laravel does this automatically)
- Sanitize user input (Laravel validators handle this)
- Use encrypted connections for remote databases

#### HTTPS/SSL

- Always use HTTPS in production
- Redirect HTTP to HTTPS
- Use HSTS headers

#### Rate Limiting

Add rate limiting to prevent abuse:

```php
// In routes/web.php
Route::middleware(['throttle:60,1'])->group(function () {
    // Your routes
});
```

#### Input Validation

Always validate and sanitize user input (already implemented):

- Contact forms use Laravel validation
- CSRF protection on all forms
- XSS protection via Blade templating

#### File Upload Security

- Validate file types and sizes (CSV import does this)
- Store uploaded files outside public directory
- Scan for malware if handling user uploads at scale

#### Regular Updates

- Keep Laravel and dependencies updated
- Monitor security advisories: `composer audit`
- Update PHP version regularly

---

## 📄 License

This project is licensed under the **MIT License**.

### MIT License

```
Copyright (c) 2025 SITC Campus

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

### Third-Party Licenses

This project uses the following open-source packages:

- **Laravel Framework** - MIT License
- **Intervention Image** - MIT License
- **Tailwind CSS** - MIT License
- **SweetAlert2** - MIT License
- **Canvas Confetti** - ISC License
- **Guzzle HTTP** - MIT License

See individual package licenses in the `vendor/` and `node_modules/` directories.

---

## 🙏 Acknowledgments

### Built With

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- [Intervention Image](https://image.intervention.io) - Image handling and manipulation library
- [Vite](https://vitejs.dev) - Next generation frontend tooling
- [SweetAlert2](https://sweetalert2.github.io) - Beautiful, responsive, customizable alerts
- [Canvas Confetti](https://github.com/catdad/canvas-confetti) - Confetti animation library

### Inspiration

This project was created to automate birthday greetings for SITC Campus, bringing joy to students and staff on their special day.

### Contributors

Special thanks to all contributors who help improve this project!

### Contact Information

- **Project Maintainer**: SITC Campus Development Team
- **Email**: info@sitc.lk
- **Website**: https://sitc.lk

---

## 📊 Project Status

- **Version**: 1.0.0
- **Status**: Active Development
- **Last Updated**: January 2026
- **Supported Laravel Version**: 10.x
- **Supported PHP Version**: 8.1+

---

## 📞 Support

### Getting Help

- **Documentation**: Read this README thoroughly
- **Issue Tracker**: [GitHub Issues](https://github.com/codezelat/laravel-automated-birthday-message-sender/issues)
- **Discussions**: [GitHub Discussions](https://github.com/codezelat/laravel-automated-birthday-message-sender/discussions)
- **Email**: info@sitc.lk

### Frequently Asked Questions

**Q: Can I use this for commercial purposes?**  
A: Yes, the MIT License allows commercial use.

**Q: Does this support multiple languages?**  
A: Not yet, but it's planned for a future release.

**Q: Can I customize the birthday card design?**  
A: Yes, replace the image in `public/images/` and modify the controller.

**Q: How do I change the message template?**  
A: Edit the message in `app/Console/Commands/SendBirthdayMessages.php`.

**Q: Can I use MySQL instead of SQLite?**  
A: Yes, just update your `.env` database configuration.

**Q: Is there a limit to how many contacts I can have?**  
A: No hard limit, but performance depends on your server resources.

---

<div align="center">

**Made with ❤️ by SITC Campus**

⭐ **Star this repository if you found it helpful!** ⭐

[Report Bug](https://github.com/codezelat/laravel-automated-birthday-message-sender/issues) · [Request Feature](https://github.com/codezelat/laravel-automated-birthday-message-sender/issues) · [Contribute](https://github.com/codezelat/laravel-automated-birthday-message-sender/pulls)

</div>
