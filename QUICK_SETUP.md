# Titan Email Marketing Platform - Quick Setup

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

### 5. Run Migrations & Seed
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Assets & Start
```bash
npm run build
php artisan serve
```

## 📊 Key Features

### Dashboard Analytics
- **Metrics**: Total emails sent, leads, campaigns, accounts
- **Charts**: Email trends, campaign performance, account usage
- **Real-time**: Live data updates

### Lead Management
- **CRUD**: Create, read, update, delete leads
- **Import**: Excel/CSV import
- **Scraping**: Web scraper integration

### Web Scraping
- **Single**: Scrape one website at a time
- **Bulk**: Scrape up to 10 websites
- **Auto-create**: Automatically create leads from scraped data

### Email Campaigns
- **Create**: Simple campaign creation
- **Send**: Bulk email sending
- **Rotation**: Automatic Outlook account rotation
- **Tracking**: Email logs and analytics

### Outlook Accounts
- **Multiple**: Add multiple Outlook accounts
- **Limits**: Set daily send limits
- **Rotation**: Auto-switch when limits reached
- **Reset**: Reset daily counts

## 🎯 Usage Flow

### 1. Add Outlook Accounts
1. Go to "Outlook Accounts"
2. Click "Add Account"
3. Enter email and daily limit (e.g., 100)
4. Save

### 2. Add Leads
**Option A - Manual:**
1. Go to "Leads"
2. Click "Create Lead"
3. Fill form and save

**Option B - Web Scraping:**
1. Go to "Leads"
2. Use Web Scraper component
3. Enter website URL
4. Check "Auto-create leads"
5. Click "Scrape"

### 3. Create Campaign
1. Go to "Campaigns"
2. Click "Create Campaign"
3. Enter name and message
4. Save

### 4. Send Campaign
1. In campaigns list, click "Send Campaign"
2. System automatically:
   - Finds leads with emails
   - Selects available Outlook account
   - Sends emails
   - Rotates accounts when limits reached
   - Logs all activity

### 5. View Analytics
- **Dashboard**: Overview metrics and charts
- **Campaign Analytics**: Detailed performance data

## 🔧 Configuration

### Daily Limits (Recommended)
- Personal Outlook: 100-300 emails/day
- Business Outlook: 500-1000 emails/day

### Scraping Settings
- Timeout: 30 seconds
- Max bulk websites: 10
- Auto-create leads: Enabled by default

## 📱 Default Login
- **Email**: admin@example.com
- **Password**: password

## 🛠️ Troubleshooting

### Emails Not Sending
1. Check Outlook credentials in `.env`
2. Verify account daily limits
3. Check email logs for errors

### Scraping Fails
1. Verify website URL is accessible
2. Check for bot protection
3. Try different websites

### Charts Not Loading
1. Run `npm run build`
2. Check browser console for errors
3. Verify ApexCharts is installed

## 📋 File Structure

```
app/
├── Models/
│   ├── Lead.php              # Lead model with relationships
│   ├── Campaign.php          # Campaign model
│   ├── EmailLog.php          # Email tracking
│   └── OutlookAccount.php    # Account management
├── Http/Controllers/
│   ├── Admin/
│   │   ├── HomeController.php        # Dashboard
│   │   ├── CampaignController.php    # Campaigns
│   │   └── LeadController.php        # Leads
│   └── ScraperController.php         # Web scraping

resources/js/Pages/Admin/
├── Dashboard.vue             # Analytics dashboard
├── campaigns/
│   ├── List.vue             # Campaign list
│   └── Analytics.vue        # Campaign analytics
└── leads/
    └── List.vue             # Lead management

resources/js/components/
└── WebScraper.vue           # Scraping component
```

## 🎨 Customization

### Add New Metrics
Edit `HomeController@dashboard()` to add new data:
```php
$data['newMetric'] = Model::count();
```

### Modify Charts
Edit `Dashboard.vue` computed properties:
```javascript
const newChart = computed(() => ({
  // Chart configuration
}));
```

### Change Email Template
Modify `CampaignController@send()`:
```php
Mail::raw($campaign->message, function ($mail) use ($lead, $account, $campaign) {
    $mail->from($account->email)
         ->to($lead->email)
         ->subject($campaign->name);
});
```

---

**🎯 MVP Complete**: Lead database ✓ Web scraping ✓ Email campaigns ✓ Account rotation ✓ Analytics ✓