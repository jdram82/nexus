## 🔐 USER LOGIN & DOWNLOAD SYSTEM SETUP

**For:** jdsancontrols.com  
**Goal:** Users register → Get license → Download plugin

---

## 📋 PAGES YOU NEED TO CREATE

### 1. **Login Page** (`/login`)
### 2. **Register Page** (`/register`)
### 3. **Dashboard Page** (`/dashboard`)
### 4. **Download Page** (`/download`)
### 5. **Pricing Page** (`/pricing`)

---

## 🚀 QUICK SETUP (30 Minutes)

### **Step 1: Create Login Page**

**In WordPress Admin:**
```
Pages → Add New
Title: Login
URL: /login
```

**Page Content:**
```html
<div class="login-container">
    <h2>Login to Your Account</h2>
    
    <?php
    // WordPress default login form
    if (!is_user_logged_in()) {
        wp_login_form(array(
            'redirect' => home_url('/dashboard'),
            'label_username' => 'Email or Username',
            'label_password' => 'Password',
            'label_remember' => 'Remember Me',
            'label_log_in' => 'Login',
            'remember' => true
        ));
        
        echo '<p>Don\'t have an account? <a href="' . home_url('/register') . '">Register here</a></p>';
        echo '<p><a href="' . wp_lostpassword_url() . '">Forgot Password?</a></p>';
    } else {
        echo '<p>You are already logged in. <a href="' . home_url('/dashboard') . '">Go to Dashboard</a></p>';
    }
    ?>
</div>

<style>
.login-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 40px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.login-container h2 {
    text-align: center;
    margin-bottom: 30px;
}
.login-container form {
    margin-bottom: 20px;
}
.login-container label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.login-container input[type="text"],
.login-container input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.login-container input[type="submit"] {
    width: 100%;
    padding: 12px;
    background: #FF6B35;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}
.login-container input[type="submit"]:hover {
    background: #E55A2B;
}
.login-container p {
    text-align: center;
    margin: 10px 0;
}
</style>
```

---

### **Step 2: Create Registration Page**

**In WordPress Admin:**
```
Pages → Add New
Title: Register
URL: /register
```

**Page Content:**
```html
<div class="register-container">
    <h2>Create Your Account</h2>
    
    <?php
    if (!is_user_logged_in()) {
        // Handle registration
        if (isset($_POST['register_user'])) {
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            
            $user_id = wp_create_user($username, $password, $email);
            
            if (!is_wp_error($user_id)) {
                // Auto-login after registration
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                
                echo '<p style="color: green;">✅ Registration successful! Redirecting...</p>';
                echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/dashboard') . '"; }, 2000);</script>';
            } else {
                echo '<p style="color: red;">❌ Error: ' . $user_id->get_error_message() . '</p>';
            }
        }
    ?>
    
    <form method="post" action="">
        <label>Username *</label>
        <input type="text" name="username" required>
        
        <label>Email *</label>
        <input type="email" name="email" required>
        
        <label>Password *</label>
        <input type="password" name="password" required minlength="8">
        
        <label>Confirm Password *</label>
        <input type="password" name="confirm_password" required minlength="8">
        
        <button type="submit" name="register_user">Create Account</button>
    </form>
    
    <p>Already have an account? <a href="<?php echo home_url('/login'); ?>">Login here</a></p>
    
    <?php
    } else {
        echo '<p>You are already registered. <a href="' . home_url('/dashboard') . '">Go to Dashboard</a></p>';
    }
    ?>
</div>

<style>
.register-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 40px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.register-container h2 {
    text-align: center;
    margin-bottom: 30px;
}
.register-container label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.register-container input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.register-container button {
    width: 100%;
    padding: 12px;
    background: #FF6B35;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}
.register-container button:hover {
    background: #E55A2B;
}
.register-container p {
    text-align: center;
    margin: 10px 0;
}
</style>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="confirm_password"]').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});
</script>
```

---

### **Step 3: Create Dashboard Page**

**In WordPress Admin:**
```
Pages → Add New
Title: Dashboard
URL: /dashboard
```

**Page Content:**
```php
<?php
if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . home_url('/login') . '">login</a> to access your dashboard.</p>';
    return;
}

$current_user = wp_get_current_user();
$supabase = new ULNEC_Supabase();
$supabase_user = $supabase->get_user_by_wordpress_id($current_user->ID);
?>

<div class="dashboard-container">
    <h1>Welcome, <?php echo esc_html($current_user->display_name); ?>!</h1>
    
    <!-- Account Info -->
    <div class="dashboard-card">
        <h2>Account Information</h2>
        <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
        <p><strong>Account Type:</strong> <?php echo esc_html($supabase_user['tier'] ?? 'Free'); ?></p>
        <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($current_user->user_registered)); ?></p>
    </div>
    
    <!-- License Info -->
    <div class="dashboard-card">
        <h2>Your Licenses</h2>
        <?php
        if ($supabase_user) {
            $licenses = $supabase->get_user_licenses($supabase_user['id']);
            
            if (!empty($licenses) && !is_wp_error($licenses)) {
                foreach ($licenses as $license) {
                    ?>
                    <div class="license-card">
                        <h3><?php echo ucfirst($license['tier']); ?> License</h3>
                        <div class="license-key-box">
                            <code><?php echo esc_html($license['license_key']); ?></code>
                            <button onclick="copyLicense('<?php echo esc_js($license['license_key']); ?>')">Copy</button>
                        </div>
                        <p><strong>Status:</strong> <span class="status-<?php echo $license['status']; ?>"><?php echo ucfirst($license['status']); ?></span></p>
                        <?php if ($license['expires_at']): ?>
                            <p><strong>Expires:</strong> <?php echo date('F j, Y', strtotime($license['expires_at'])); ?></p>
                        <?php else: ?>
                            <p><strong>Expires:</strong> Never (Lifetime)</p>
                        <?php endif; ?>
                        <p><strong>Activations:</strong> <?php echo $license['activation_count']; ?> / <?php echo $license['max_activations']; ?></p>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>You don't have any licenses yet.</p>
                <a href="<?php echo home_url('/pricing'); ?>" class="button">View Pricing</a>
                <?php
            }
        }
        ?>
    </div>
    
    <!-- Download Section -->
    <div class="dashboard-card">
        <h2>Download Plugin</h2>
        <?php
        $auth = new ULNEC_Auth($supabase);
        if ($auth->user_has_active_license()) {
            $download = new ULNEC_Download($supabase);
            $download_url = $download->get_download_link();
            ?>
            <p>✅ You have an active license!</p>
            <a href="<?php echo esc_url($download_url); ?>" class="download-button">
                <span class="icon">⬇️</span> Download UL-NEC Plugin (Latest Version)
            </a>
            <p class="download-note">File: UL-NEC-Compliance-Plugin-Latest.msi (~50 MB)</p>
            <?php
        } else {
            ?>
            <p>⚠️ You need an active license to download.</p>
            <a href="<?php echo home_url('/pricing'); ?>" class="button">Get License</a>
            <?php
        }
        ?>
    </div>
    
    <!-- Download History -->
    <div class="dashboard-card">
        <h2>Download History</h2>
        <?php
        $download_manager = new ULNEC_Download($supabase);
        $downloads = $download_manager->get_user_download_history();
        
        if (!empty($downloads)) {
            echo '<table class="download-history">';
            echo '<thead><tr><th>Version</th><th>Date</th></tr></thead>';
            echo '<tbody>';
            foreach ($downloads as $dl) {
                echo '<tr>';
                echo '<td>' . esc_html($dl['version']) . '</td>';
                echo '<td>' . date('F j, Y g:i A', strtotime($dl['downloaded_at'])) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No downloads yet.</p>';
        }
        ?>
    </div>
    
    <!-- Quick Links -->
    <div class="dashboard-card">
        <h2>Quick Links</h2>
        <ul class="quick-links">
            <li><a href="<?php echo home_url('/support'); ?>">📧 Contact Support</a></li>
            <li><a href="<?php echo home_url('/docs'); ?>">📖 Documentation</a></li>
            <li><a href="<?php echo home_url('/bug-report'); ?>">🐛 Report a Bug</a></li>
            <li><a href="<?php echo wp_logout_url(home_url()); ?>">🚪 Logout</a></li>
        </ul>
    </div>
</div>

<style>
.dashboard-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}
.dashboard-card {
    background: white;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.license-card {
    background: #f8f9fa;
    padding: 20px;
    margin: 15px 0;
    border-left: 4px solid #FF6B35;
    border-radius: 4px;
}
.license-key-box {
    background: white;
    padding: 15px;
    margin: 10px 0;
    border: 2px dashed #ddd;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.license-key-box code {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}
.license-key-box button {
    padding: 8px 15px;
    background: #FF6B35;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.status-active { color: green; font-weight: bold; }
.status-expired { color: red; font-weight: bold; }
.download-button {
    display: inline-block;
    padding: 15px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    transition: transform 0.2s;
}
.download-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.download-note {
    font-size: 14px;
    color: #666;
    margin-top: 10px;
}
.download-history {
    width: 100%;
    border-collapse: collapse;
}
.download-history th,
.download-history td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.download-history th {
    background: #f8f9fa;
    font-weight: bold;
}
.quick-links {
    list-style: none;
    padding: 0;
}
.quick-links li {
    margin: 10px 0;
}
.quick-links a {
    color: #667eea;
    text-decoration: none;
    font-size: 16px;
}
.quick-links a:hover {
    text-decoration: underline;
}
</style>

<script>
function copyLicense(key) {
    navigator.clipboard.writeText(key);
    alert('License key copied to clipboard!');
}
</script>
```

---

### **Step 4: Update Your Navigation Menu**

**Add these links to your main menu:**
```
- Login (/login)
- Register (/register)
- Dashboard (/dashboard) [show only when logged in]
- Logout [show only when logged in]
```

**In WordPress:**
```
Appearance → Menus
Add Custom Links:
- Login: /login
- Register: /register
- Dashboard: /dashboard
```

---

### **Step 5: Add "Download" CTA to Homepage**

Update your homepage "Explore Tools" button to point to `/register` or `/dashboard`

---

## 🎯 USER FLOW

### **New User Journey:**
```
1. Visits jdsancontrols.com
2. Clicks "Register" or "Get Started"
3. Creates account on /register
4. Auto-redirected to /dashboard
5. Sees "You need a license" message
6. Clicks "Get License" → /pricing
7. Purchases license (PayPal/Razorpay)
8. Returns to /dashboard
9. Sees "Download" button
10. Downloads .msi file
11. Installs plugin
12. Activates with license key from dashboard
```

### **Returning User Journey:**
```
1. Visits jdsancontrols.com
2. Clicks "Login"
3. Enters credentials
4. Redirected to /dashboard
5. Sees license key and download button
6. Downloads latest version
```

---

## 🔒 SECURITY FEATURES

**Already Built-In:**
- ✅ WordPress authentication
- ✅ Supabase Row Level Security
- ✅ Download URLs expire in 5 minutes
- ✅ License validation on every download
- ✅ IP tracking for downloads
- ✅ User-agent logging

---

## 📧 AUTO-EMAILS (Coming)

Users will receive:
1. **Welcome Email** (on registration)
2. **License Key Email** (after purchase)
3. **Download Instructions** (with license)
4. **Expiration Reminder** (30 days before)

---

## 🎨 NEXT: CUSTOMIZE STYLING

Match your brand colors:
- Purple gradient: `#667eea → #764ba2`
- Orange CTA: `#FF6B35`
- Update CSS to match jdsancontrols.com theme

---

## ✅ IMMEDIATE ACTIONS

**Do this now:**

1. ✅ Create `/login` page (copy content above)
2. ✅ Create `/register` page (copy content above)
3. ✅ Create `/dashboard` page (copy content above)
4. ✅ Update navigation menu
5. ✅ Test: Register → Login → See Dashboard

**Expected Result:**
- User can register
- User auto-syncs to Supabase
- User sees dashboard
- User can't download yet (no license)

---

**Ready to create these pages?** Let me know when they're live and I'll help you test the flow! 🚀
