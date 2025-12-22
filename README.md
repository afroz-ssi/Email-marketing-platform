# Titan Email Marketing Platform - MVP

A complete email marketing platform built with Laravel and Vue.js that enables lead management, web scraping, campaign management, and multi-account email sending through Outlook accounts.

## 🎯 MVP Objectives - ALL COMPLETE ✅

| Requirement | Status | Implementation |
|-------------|--------|----------------|
| **Lead Database** | ✅ | Complete CRUD with name, company, website, email, phone |
| **Web Scraping** | ✅ | Single & bulk scraping with auto-lead creation |
| **Email Campaigns** | ✅ | Full campaign system via Outlook accounts |
| **Account Switching** | ✅ | Automatic rotation based on daily limits |
| **Analytics Dashboard** | ✅ | Interactive charts and real-time metrics |
| **Professional UI** | ✅ | Clean Metronic-based responsive interface |

## 🚀 Quick Installation

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Edit `.env`:
```env
DB_DATABASE=titan_email_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Mail Configuration (Outlook)
Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### 5. Run Migrations & Seed Sample Data
```bash
php artisan migrate
php artisan db:seed
php artisan db:seed --class=SampleDataSeeder
```

### 6. Build Assets & Start
```bash
npm run build
php artisan serve
```

**Default Login:** admin@example.com / password

## 📊 Core Features

### 1. Lead Database Management
- **Complete CRUD** operations for leads
- **Required Fields**: name, company, website, email, phone
- **Search & Filter** functionality
- **Pagination** support
- **Sample Data**: 5 leads with complete information

### 2. Web Scraping System
- **Single Website** scraping for emails and phone numbers
- **Bulk Scraping** up to 10 websites simultaneously
- **Auto-create Leads** from scraped data
- **Company Detection** from website content
- **Error Handling** for invalid URLs

### 3. Email Campaign Management
- **Campaign Creation** with name and message
- **Bulk Email Sending** to all leads with emails
- **Duplicate Prevention** - won't send same campaign twice to same lead
- **Status Tracking** (draft, active, completed, paused)
- **Sample Data**: 3 campaigns ready for testing

### 4. Outlook Account Rotation
- **Multiple Accounts** support with individual daily limits
- **Automatic Switching** when account reaches daily limit
- **Real-time Tracking** of sent_today counters
- **Reset Functionality** for daily counts
- **Sample Data**: 3 accounts with different usage levels

### 5. Analytics Dashboard
- **Key Metrics**: Total emails sent, leads, campaigns, active accounts
- **Interactive Charts**: Email trends, campaign performance, account usage
- **Real-time Updates** with ApexCharts integration
- **Weekly/Monthly** trend analysis

## 🎯 Usage Flow

### Step 1: Setup Outlook Accounts
1. Navigate to **Outlook Accounts**
2. Click **"Add Account"**
3. Enter email and daily limit (e.g., 100)
4. Save account

### Step 2: Add Leads (Two Methods)

**Method A - Manual Entry:**
1. Go to **Leads**
2. Click **"Add New"**
3. Fill contact information
4. Save lead

**Method B - Web Scraping:**
1. Go to **Leads**
2. Click **"Web Scraper"** button (integrated on leads page)
3. Enter website URL (e.g., https://example.com)
4. Check **"Auto-create leads"**
5. Click **"Scrape Website"**
6. Review extracted emails/phones in results
7. Leads created automatically if option selected
8. Use **Bulk Scraper** section for multiple websites

### Step 3: Create & Send Campaign
1. Navigate to **Campaigns**
2. Click **"Create Campaign"**
3. Enter campaign name and message
4. Save campaign
5. Click **"Send Campaign"**
6. System automatically:
   - Finds all leads with valid emails
   - Selects available Outlook account
   - Sends emails in bulk
   - Rotates to next account when limit reached
   - Logs all email activity
   - Updates sent counts

### Step 4: Monitor Analytics
- **Dashboard**: Overview metrics and trend charts
- **Campaign Analytics**: Detailed performance data

## 🔧 Technical Architecture

### Backend (Laravel)
- **MVC Architecture** with clean separation of concerns
- **Eloquent ORM** for database relationships
- **Security**: CSRF protection, SQL injection prevention, input validation
- **Performance**: Optimized queries, pagination, eager loading
- **Error Handling**: Comprehensive logging and try-catch blocks

### Frontend (Vue.js)
- **Component-based** architecture with Inertia.js
- **Reactive Data Binding** for real-time updates
- **ApexCharts Integration** for interactive analytics
- **Responsive Design** with Bootstrap grid system
- **Professional UI** using Metronic admin theme

### Database Schema
```
leads (id, name, company, website, email, phone, timestamps)
campaigns (id, name, message, sent_count, status, timestamps)
email_logs (id, campaign_id, lead_id, outlook_email, status, sent_at, timestamps)
outlook_accounts (id, email, daily_limit, sent_today, status, timestamps)
```

### Key Relationships
- Campaign → EmailLogs (one-to-many)
- Lead → EmailLogs (one-to-many)
- OutlookAccount → EmailLogs (one-to-many)

## 📋 Sample Data Included

**5 Leads:**
- John Doe (john@example.com, Tech Corp)
- Jane Smith (jane@business.com, Business Inc)
- Mike Johnson (mike@startup.io, Startup IO)
- Sarah Wilson (sarah@agency.net, Creative Agency)
- David Brown (david@consulting.org, Brown Consulting)

**3 Outlook Accounts:**
- marketing1@outlook.com (25/100 sent - Active)
- marketing2@outlook.com (100/150 sent - Active)
- marketing3@outlook.com (200/200 sent - Limit Reached)

**3 Campaigns:**
- Welcome Campaign (15 emails sent)
- Product Launch (8 emails sent)
- Newsletter Q4 (0 emails sent - ready to test)

## 🛠️ Troubleshooting

### Emails Not Sending
1. Verify Outlook credentials in `.env`
2. Check account daily limits not exceeded
3. Review email logs for specific errors

### Web Scraping Issues
1. Ensure website URL is accessible
2. Check for CAPTCHA or bot protection
3. Try different websites for testing

### Charts Not Loading
1. Run `npm run build` to compile assets
2. Check browser console for JavaScript errors
3. Verify ApexCharts is properly installed

## 🔐 Security Features

- **CSRF Protection** on all forms
- **SQL Injection Prevention** via Eloquent ORM
- **XSS Protection** with input sanitization
- **Password Hashing** for user authentication
- **Email Validation** for lead data
- **URL Validation** for web scraping

## 📈 Performance Optimizations

- **Database Indexing** on searchable columns
- **Query Optimization** with eager loading
- **Pagination** for large datasets
- **Component Lazy Loading** in Vue.js
- **Asset Minification** for production

## 🎨 Code Quality Standards

- **PSR-12 Compliance** for PHP code
- **Component Architecture** for Vue.js
- **Consistent Naming** conventions
- **Comprehensive Comments** and documentation
- **Error Handling** throughout application
- **Input Validation** on all forms

## 📦 File Structure

```
app/
├── Models/                   # Eloquent models with relationships
├── Http/Controllers/Admin/   # Admin panel controllers
├── Http/Controllers/         # API and web controllers
└── Mail/                     # Email templates

resources/js/
├── Pages/Admin/             # Vue.js admin pages
├── components/              # Reusable Vue components
└── helpers/                 # JavaScript utilities

database/
├── migrations/              # Database schema
└── seeders/                 # Sample data seeders
```

## 🚀 Production Deployment

### Requirements
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer
- Web server (Apache/Nginx)

### Deployment Steps
1. Clone repository
2. Install dependencies (`composer install`, `npm install`)
3. Configure environment (`.env`)
4. Run migrations (`php artisan migrate`)
5. Seed data (`php artisan db:seed`)
6. Build assets (`npm run build`)
7. Configure web server
8. Set proper file permissions

## 📝 Submission Checklist

- [x] **GitHub Repository** with complete source code
- [x] **Setup Instructions** in this README
- [x] **.env.example** file with required configurations
- [x] **Sample Data** seeded for immediate testing
- [x] **All MVP Objectives** implemented and tested
- [x] **Professional UI** with responsive design
- [x] **Comprehensive Documentation** in single README

## 🎉 Project Status: PRODUCTION READY

This MVP successfully demonstrates all required features with:
- ✅ Complete lead database with CRUD operations
- ✅ Working web scraper (single & bulk)
- ✅ Full email campaign system
- ✅ Automatic Outlook account rotation
- ✅ Interactive analytics dashboard
- ✅ Professional, responsive UI
- ✅ Clean, maintainable code
- ✅ Comprehensive security measures
- ✅ Performance optimizations
- ✅ Sample data for immediate testing

**Ready for demonstration and production deployment!**

---

**Built with ❤️ using Laravel & Vue.js**