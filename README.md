# Photokrafft Dynamic Form System

A complete dynamic form system with PHP backend, database storage, email notifications, and responsive admin dashboard.

## Features

### Form Features
- ✅ Dynamic form with real-time validation
- ✅ Responsive design with modern UI
- ✅ Form data stored in MySQL database
- ✅ Email notifications to customers
- ✅ CC notifications to admin (parth@photokrafft.com)
- ✅ Success/error handling with user feedback

### Admin Dashboard Features
- ✅ Secure admin login system
- ✅ Responsive black and white theme
- ✅ Real-time statistics (total, today, this week)
- ✅ Search functionality
- ✅ CRUD operations (Create, Read, Update, Delete)
- ✅ Export to CSV/Excel functionality
- ✅ Modal views for detailed information
- ✅ Mobile responsive design

## Setup Instructions

### 1. Database Setup
1. Create a MySQL database named `photokrafft_forms`
2. Update database credentials in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'photokrafft_forms');
   ```

### 2. Email Configuration
1. Install PHPMailer:
   ```bash
   composer install
   ```

2. Update email settings in `config/email.php`:
   ```php
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_PORT', 587);
   define('SMTP_USERNAME', 'your-email@gmail.com');
   define('SMTP_PASSWORD', 'your-app-password');
   define('ADMIN_EMAIL', 'parth@photokrafft.com');
   ```

   **Note:** For Gmail, you need to:
   - Enable 2-factor authentication
   - Generate an App Password
   - Use the App Password instead of your regular password

### 3. Admin Login
- **Username:** admin
- **Password:** photokrafft2024
- **Admin URL:** `your-domain.com/admin/`

### 4. File Structure
```
├── index.html              # Main form page
├── process_form.php        # Form processing script
├── config/
│   ├── database.php        # Database configuration
│   └── email.php          # Email configuration
├── admin/
│   ├── index.php          # Admin dashboard
│   └── api.php            # Admin API for CRUD
├── composer.json           # PHP dependencies
└── README.md              # This file
```

## Usage

### For Customers
1. Visit the main page (`index.html`)
2. Fill out the registration form
3. Submit the form
4. Receive confirmation email
5. Admin receives notification email

### For Admin
1. Login at `/admin/`
2. View all form submissions in table format
3. Search through submissions
4. View, edit, or delete submissions
5. Export data to CSV or Excel
6. View real-time statistics

## Email Notifications

### Customer Email
- Sent to the email address provided in the form
- Contains registration confirmation and details
- Professional HTML template

### Admin Email
- Sent to parth@photokrafft.com
- Contains new registration details
- CC'd to admin email for tracking

## Database Schema

```sql
CREATE TABLE form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    event_name VARCHAR(255),
    workshop_name VARCHAR(255),
    investment VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Security Features

- ✅ SQL injection prevention with prepared statements
- ✅ XSS prevention with htmlspecialchars
- ✅ Email validation
- ✅ Admin session management
- ✅ CSRF protection (basic)

## Browser Compatibility

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers

## Troubleshooting

### Common Issues

1. **Email not sending:**
   - Check SMTP credentials
   - Verify app password for Gmail
   - Check server firewall settings

2. **Database connection error:**
   - Verify database credentials
   - Ensure MySQL is running
   - Check database permissions

3. **Form not submitting:**
   - Check PHP error logs
   - Verify file permissions
   - Ensure all required files exist

### Error Logs
Check your server's error logs for detailed error messages:
- Apache: `/var/log/apache2/error.log`
- Nginx: `/var/log/nginx/error.log`
- PHP: Check php.ini error_log setting

## Customization

### Changing Admin Credentials
Edit the credentials in `admin/index.php`:
```php
$admin_username = 'your_username';
$admin_password = 'your_password';
```

### Modifying Email Templates
Edit the email templates in `config/email.php` functions:
- `sendCustomerConfirmation()`
- `sendAdminNotification()`

### Adding New Form Fields
1. Add field to HTML form
2. Update database schema
3. Modify `process_form.php`
4. Update admin dashboard
5. Update email templates

## Support

For technical support or customization requests, contact the development team.

## License

This project is proprietary software for Photokrafft.