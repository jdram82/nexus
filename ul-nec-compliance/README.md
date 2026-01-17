# UL-NEC Compliance AutoCAD Plugin Manager

**Version:** 1.0.0-beta  
**WordPress Plugin** for managing UL-NEC Compliance AutoCAD Plugin  
**Backend:** Supabase  
**Payments:** PayPal + Razorpay  

---

## 📦 WHAT THIS PLUGIN DOES

Complete SaaS management system for your AutoCAD plugin:

✅ **User Management**
- WordPress user registration & login
- Auto-sync with Supabase database
- User tiers: Free, Beta, Pro, Enterprise
- Profile management

✅ **License Management**
- Generate unique license keys
- Validate activations
- Track machine IDs
- Manage expiration
- Multiple activation support

✅ **Secure Downloads**
- License-validated downloads
- Temporary signed URLs (5 min expiry)
- Download tracking & analytics
- Version management
- .msi file delivery

✅ **Payment Processing**
- PayPal integration
- Razorpay integration
- Subscription management
- Transaction logging
- Automated license delivery

✅ **Support System**
- Bug report submission
- Feature request voting
- User feedback collection
- Admin management interface

✅ **Analytics**
- Download tracking
- Usage metrics
- Revenue reports
- User behavior

---

## 🚀 QUICK INSTALL

### Requirements:
- WordPress 5.8+
- PHP 7.4+
- Supabase account (PRO plan)
- PayPal or Razorpay account

### Installation:

1. **Download & Upload:**
   ```bash
   # Zip this folder
   zip -r ul-nec-compliance.zip ul-nec-compliance/
   
   # Upload via WordPress:
   # Plugins → Add New → Upload Plugin
   ```

2. **Activate Plugin:**
   - Go to Plugins
   - Find "UL-NEC Compliance"
   - Click "Activate"

3. **Configure Supabase:**
   
   Add to `wp-config.php`:
   ```php
   define('ULNEC_SUPABASE_URL', 'https://xxx.supabase.co');
   define('ULNEC_SUPABASE_ANON_KEY', 'your-anon-key');
   define('ULNEC_SUPABASE_SERVICE_KEY', 'your-service-key');
   ```

4. **Setup Database:**
   - Run `BETA_DATABASE_SCHEMA.sql` in Supabase
   - Create storage buckets: `ulnec-downloads`, `ulnec-screenshots`
   - Upload your .msi file

5. **Test Connection:**
   - Go to UL-NEC → Dashboard
   - See "✅ Supabase Connected"

---

## 📁 FILE STRUCTURE

```
ul-nec-compliance/
├── ul-nec-compliance.php        # Main plugin file
│
├── includes/                    # Core classes
│   ├── class-ulnec-supabase.php # Supabase integration
│   ├── class-ulnec-auth.php     # Authentication
│   ├── class-ulnec-license.php  # License management
│   ├── class-ulnec-download.php # Download handling
│   ├── class-ulnec-payment.php  # Payment processing
│   ├── class-ulnec-admin.php    # Admin dashboard
│   ├── class-ulnec-frontend.php # Frontend pages
│   ├── class-ulnec-shortcodes.php # Shortcodes
│   └── class-ulnec-ajax.php     # AJAX handlers
│
├── templates/                   # Page templates
│   ├── landing.php              # Homepage
│   ├── pricing.php              # Pricing page
│   ├── dashboard.php            # User dashboard
│   ├── download.php             # Download page
│   ├── profile.php              # Profile page
│   ├── bug-report.php           # Bug reports
│   ├── feature-request.php      # Feature requests
│   ├── founders.php             # Founders program
│   └── beta-application.php     # Beta signup
│
├── assets/                      # Frontend assets
│   ├── css/
│   │   ├── frontend.css         # User-facing styles
│   │   └── admin.css            # Admin styles
│   ├── js/
│   │   ├── frontend.js          # User-facing scripts
│   │   └── admin.js             # Admin scripts
│   └── images/                  # Images
│
├── admin/                       # Admin templates
│   ├── dashboard.php            # Admin dashboard
│   ├── users.php                # User management
│   ├── licenses.php             # License management
│   ├── downloads.php            # Download logs
│   ├── bugs.php                 # Bug reports
│   ├── features.php             # Feature requests
│   ├── payments.php             # Payment history
│   └── settings.php             # Plugin settings
│
├── languages/                   # Translations
│   └── ulnec.pot                # Translation template
│
└── readme.txt                   # WordPress readme
```

---

## 🔌 CORE CLASSES

### ULNEC_Supabase
Handles all Supabase communication:
- REST API requests
- User CRUD operations
- License management
- Download tracking
- Storage URL generation

### ULNEC_Auth
User authentication & sync:
- WordPress → Supabase sync
- Login/registration hooks
- Tier management
- License validation

### ULNEC_License
License generation & validation:
- Unique key generation (ULNEC-XXXX-XXXX-XXXX-XXXX)
- Activation tracking
- Machine ID validation
- Expiration handling
- Multi-activation support

### ULNEC_Download
Secure file downloads:
- License validation
- Signed URL generation
- Download logging
- Version management
- Access control

### ULNEC_Payment
Payment processing:
- PayPal integration
- Razorpay integration
- Webhook handling
- License auto-generation
- Transaction logging

---

## 🎨 SHORTCODES

Use these in pages/posts:

```php
[ulnec_login]           // Login form
[ulnec_register]        // Registration form
[ulnec_pricing]         // Pricing table
[ulnec_dashboard]       // User dashboard
[ulnec_download]        // Download button
[ulnec_licenses]        // User's licenses
[ulnec_bug_report]      // Bug report form
[ulnec_feature_request] // Feature request form
[ulnec_founders]        // Founders program
```

---

## ⚙️ CONFIGURATION

### Required Settings:

**Supabase:**
- URL: Your Supabase project URL
- Anon Key: Public API key
- Service Key: Private API key (server-side only)

**Payment Gateways:**

**PayPal:**
- Client ID
- Client Secret
- Mode: Sandbox / Live

**Razorpay:**
- Key ID
- Key Secret
- Mode: Test / Live

### Optional Settings:

- Email notifications
- Download expiry (default: 5 min)
- License duration (default: 365 days)
- Max activations per tier
- Welcome email template
- License delivery email

---

## 🔐 SECURITY FEATURES

✅ **Data Protection:**
- Row Level Security in Supabase
- Encrypted API keys
- Secure credential storage (wp-config.php)

✅ **Download Security:**
- License validation required
- Temporary signed URLs (expire in 5 min)
- IP tracking
- Rate limiting

✅ **Payment Security:**
- PCI compliance (via gateways)
- Webhook signature verification
- Transaction logging
- Fraud prevention

✅ **Code Security:**
- SQL injection prevention (using APIs)
- XSS protection (esc_html, esc_attr)
- CSRF tokens on forms
- Nonce verification
- Input sanitization

---

## 📊 ADMIN DASHBOARD

Access: `yoursite.com/wp-admin` → UL-NEC menu

**Dashboard:**
- Connection status
- User count
- Active licenses
- Revenue overview
- Recent downloads

**Users:**
- All registered users
- Tier management
- Subscription status
- Activity logs

**Licenses:**
- Generate new licenses
- View all licenses
- Activation history
- Expire/revoke licenses

**Downloads:**
- Download logs
- Popular versions
- User analytics
- Download success rate

**Bugs:**
- All bug reports
- Status management
- Severity filtering
- Admin notes

**Features:**
- Feature requests
- Vote counts
- Status updates
- Priority setting

**Payments:**
- Transaction history
- Revenue reports
- Refund management
- Gateway logs

**Settings:**
- Supabase configuration
- Payment gateway setup
- Email settings
- License defaults

---

## 🧪 TESTING

### Test Flow:

1. **User Registration:**
   - Register new account
   - Check WordPress users table
   - Check Supabase ulnec_users table
   - Verify email received

2. **License Generation:**
   - Admin generates license
   - Check ulnec_licenses table
   - Verify license key format
   - Test validation

3. **Download:**
   - Login as user
   - Click download button
   - Verify license checked
   - Confirm file downloads
   - Check download logged

4. **Payment (Sandbox):**
   - Add item to cart
   - Complete PayPal/Razorpay checkout
   - Verify license auto-generated
   - Check transaction recorded
   - Confirm email sent

---

## 🐛 TROUBLESHOOTING

### Common Issues:

**"Supabase Connection Failed"**
- Verify credentials in wp-config.php
- Check Supabase project is running
- Test API keys in Supabase dashboard
- Ensure no trailing slash in URL

**"Download Returns 403"**
- User needs active license
- Check license not expired
- Verify file exists in storage
- Check bucket permissions

**"License Validation Fails"**
- Verify license exists in database
- Check license status is 'active'
- Confirm not expired
- Test machine ID matching

**"Payment Not Processing"**
- Check using correct API keys (live/test)
- Verify webhook URL configured
- Review payment gateway logs
- Test with small amount first

---

## 📧 SUPPORT

**Documentation:**
- [BETA_IMPLEMENTATION_GUIDE.md](../BETA_IMPLEMENTATION_GUIDE.md)
- [BETA_LAUNCH_PLAN.md](../BETA_LAUNCH_PLAN.md)
- [START_HERE_BETA.md](../START_HERE_BETA.md)

**External Resources:**
- Supabase Docs: https://supabase.com/docs
- PayPal Developer: https://developer.paypal.com
- Razorpay Docs: https://razorpay.com/docs

---

## 🔄 VERSION HISTORY

**1.0.0-beta (January 16, 2026)**
- Initial beta release
- Core functionality
- Payment integration
- Download system
- Support features

---

## 📝 LICENSE

GPL v2 or later

---

## 🤝 CREDITS

**Developed for:** JDS & N Controls  
**Website:** https://jdsancontrols.com  
**Backend:** Supabase  
**Payments:** PayPal, Razorpay  

---

## 🚀 NEXT STEPS

After installing:

1. ✅ Read [START_HERE_BETA.md](../START_HERE_BETA.md)
2. ✅ Run database schema
3. ✅ Configure Supabase
4. ✅ Test connection
5. ✅ Configure payments
6. ✅ Customize templates
7. ✅ Launch beta!

**Good luck with your launch!** 🎉
