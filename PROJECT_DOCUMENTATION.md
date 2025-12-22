# Titan Email Marketing Platform - MVP

A comprehensive email marketing platform built with Laravel and Vue.js that enables lead management, web scraping, campaign management, and multi-account email sending through Outlook accounts.

## 🎯 Project Objectives

This MVP demonstrates:

- **Lead Database**: Store and manage contact information (name, company, website, email, phone)
- **Web Scraping**: Extract business emails and phone numbers from websites
- **Email Campaigns**: Send bulk emails through multiple Outlook accounts
- **Account Rotation**: Automatically switch between Outlook accounts to respect daily send limits
- **Analytics Dashboard**: Track campaign performance, email sends, and account usage
- **Professional UI**: Clean, modern interface built with Vue.js and Metronic theme

## 🚀 Features

### 1. Lead Management

- Create, edit, and delete leads
- Import leads from Excel/CSV
- Web scraping integration for automatic lead generation
- Track email history per lead
- Filter and search capabilities

### 2. Web Scraping

- Single website scraping for emails and phone numbers
- Bulk website scraping (up to 10 sites at once)
- Automatic lead creation from scraped data
- Company name extraction
- Smart filtering to remove invalid contacts

### 3. Campaign Management

- Create and manage email campaigns
- Rich text message editor
- Send campaigns to all leads with emails
- Track sent count per campaign
- Campaign status tracking (draft, active, completed, paused)
- Prevent duplicate sends to same lead

### 4. Outlook Account Management

- Add multiple Outlook accounts
- Set daily send limits per account
- Automatic account rotation
- Real-time usage tracking
- Reset daily counts
- Account status management (queued/sent)

### 5. Analytics & Reporting

- **Dashboard Analytics**:

  - Total emails sent
  - Total leads in database
  - Active campaigns count
  - Outlook account status
  - Email sending trends (weekly/monthly)
  - Campaign performance charts
  - Outlook account usage visualization
  - Lead source distribution

- **Campaign Analytics**:
  - Daily email trends (last 30 days)
  - Top performing campaigns
  - Outlook account performance
  - Recent email activity log
  - Campaign leaderboard

### 6. Email Sending Logic

- Automatic account selection based on availability
- Respects daily send limits
- Marks accounts as "sent" when limit reached
- Error handling and logging
- Prevents duplicate sends
- Queue support for large campaigns

## 📋 Technical Stack

- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3 with Inertia.js
- **Database**: MySQL
- **Charts**: ApexCharts
- **UI Framework**: Metronic Admin Theme
- **HTTP Client**: Guzzle (for web scraping)
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission

## 🛠️ Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Setup Steps

1. **Clone the repository**

```bash
git clone <repository-url>
cd TItan-email-platform-interview
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install Node dependencies**

```bash
npm install
```

4. **Environment Configuration**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure Database**
   Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=titan_email_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. **Configure Mail Settings**
   Edit `.env` file for Outlook SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-outlook-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-outlook-email@outlook.com
MAIL_FROM_NAME="${APP_NAME}"
```

7. **Run Migrations**

```bash
php artisan migrate
```

8. **Seed Database**

```bash
php artisan db:seed
```

9. **Build Assets**

```bash
npm run build
# or for development
npm run dev
```

10. **Start Development Server**

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📊 Database Schema

### Tables

1. **leads**

   - id, name, company, website, email, phone, timestamps

2. **campaigns**

   - id, name, message, sent_count, status, timestamps

3. **email_logs**

   - id, campaign_id, lead_id, outlook_email, status, sent_at

4. **outlook_accounts**

   - id, email, daily_limit, sent_today, status, timestamps

5. **users**
   - Standard Laravel users table with roles

## 🔑 Key Features Implementation

### Web Scraping

```php
// Single website scraping
POST /admin/scrape
{
    "website": "https://example.com",
    "auto_create_leads": true
}

// Bulk scraping
POST /admin/bulk-scrape
{
    "websites": [
        "https://example1.com",
        "https://example2.com"
    ]
}
```

### Campaign Sending

The system automatically:

1. Fetches all leads with valid emails
2. Checks if lead already received this campaign
3. Selects available Outlook account (status=queued, sent_today < daily_limit)
4. Sends email and logs the activity
5. Increments account's sent_today counter
6. Marks account as "sent" when limit reached
7. Moves to next available account

### Account Rotation Logic

```php
// Get available account
$account = OutlookAccount::where('status', 'queued')
    ->whereColumn('sent_today', '<', 'daily_limit')
    ->first();

// After sending
$account->increment('sent_today');

// Check if limit reached
if ($account->sent_today >= $account->daily_limit) {
    $account->update(['status' => 'sent']);
}
```

## 📈 Analytics Features

### Dashboard Metrics

- Total emails sent (all time)
- Total leads in database
- Active campaigns count
- Outlook accounts (active/total)
- Email trends (weekly/monthly charts)
- Campaign performance (donut chart)
- Outlook usage (bar chart)
- Lead sources (pie chart)

### Campaign Analytics

- Daily email trends (30-day line chart)
- Top campaigns (donut chart)
- Outlook performance (multi-bar chart)
- Recent activity table
- Campaign leaderboard

## 🔐 Security Features

- CSRF protection on all forms
- SQL injection prevention (Eloquent ORM)
- XSS protection
- Role-based access control
- Password hashing
- Email validation
- URL validation for scraping

## 🎨 UI Components

### Dashboard

- Metric cards with icons
- Interactive charts (ApexCharts)
- Responsive grid layout
- Real-time data updates

### Lead Management

- Data table with pagination
- Search and filter
- Bulk import
- Web scraper integration

### Campaign Management

- Campaign list with status badges
- Send campaign button
- Reset all accounts button
- Analytics link

### Outlook Accounts

- Account list with usage indicators
- Daily limit tracking
- Reset individual/all accounts
- Status badges

## 🚦 API Endpoints

### Admin Routes

```
GET  /admin/dashboard              - Dashboard with analytics
GET  /admin/leads                  - Lead list
POST /admin/create-lead            - Create lead
GET  /admin/campaigns              - Campaign list
GET  /admin/campaigns/analytics    - Campaign analytics
POST /admin/campaigns/{id}/send    - Send campaign
GET  /admin/outlook-accounts       - Outlook account list
POST /admin/scrape                 - Scrape single website
POST /admin/bulk-scrape            - Scrape multiple websites
```

## 📝 Usage Examples

### Adding an Outlook Account

1. Navigate to "Outlook Accounts"
2. Click "Add Outlook Account"
3. Enter email and daily limit (e.g., 100)
4. Save

### Creating a Campaign

1. Navigate to "Campaigns"
2. Click "Create Campaign"
3. Enter campaign name and message
4. Save
5. Click "Send Campaign" to start sending

### Web Scraping

1. Navigate to "Leads"
2. Use the Web Scraper component
3. Enter website URL
4. Check "Auto-create leads"
5. Click "Scrape Website"
6. Review results and create leads

### Viewing Analytics

1. Navigate to "Dashboard" for overview
2. Navigate to "Campaigns > Analytics" for detailed campaign metrics
3. Toggle between weekly/monthly views
4. Export data as needed

## 🔧 Configuration

### Email Configuration

Configure multiple Outlook accounts in the database, not in `.env`. The `.env` mail settings are used as defaults.

### Daily Limits

Set appropriate daily limits per Outlook account:

- Personal Outlook: 100-300 emails/day
- Business Outlook: 500-1000 emails/day

### Scraping Settings

- Timeout: 30 seconds
- Max bulk websites: 10
- User-Agent: Mozilla/5.0

## 🐛 Troubleshooting

### Emails Not Sending

1. Check Outlook account credentials
2. Verify daily limits not exceeded
3. Check email logs for errors
4. Ensure SMTP settings are correct

### Scraping Fails

1. Verify website URL is accessible
2. Check for CAPTCHA or bot protection
3. Review error logs
4. Try different User-Agent

### Charts Not Loading

1. Ensure ApexCharts is installed
2. Check browser console for errors
3. Verify data is being passed to component

## 📦 Dependencies

### PHP Packages

- laravel/framework: ^11.0
- inertiajs/inertia-laravel: ^1.0
- guzzlehttp/guzzle: ^7.0
- spatie/laravel-permission: ^6.0
- maatwebsite/excel: ^3.1

### NPM Packages

- vue: ^3.4
- @inertiajs/vue3: ^1.0
- apexcharts: ^3.45
- vue3-apexcharts: ^1.5

## 🎯 Future Enhancements

- Email templates with variables
- A/B testing for campaigns
- Email open tracking
- Click tracking
- Unsubscribe management
- Scheduled campaigns
- Email bounce handling
- Advanced lead segmentation
- CRM integration
- API for external integrations

## 👥 Default Users

After seeding:

- **Super Admin**
  - Email: admin@example.com
  - Password: password

## 📄 License

This project is proprietary software developed for Titan Email Platform.

## 🤝 Support

For support, email support@titanemailplatform.com or create an issue in the repository.

---

**Built with ❤️ using Laravel & Vue.js**
