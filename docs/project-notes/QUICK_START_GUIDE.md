# 🚀 Quick Start Guide - Get Started TODAY
## UL-NEC Compliance Plugin Integration

**Time to First Result:** 2-3 hours  
**Goal:** Have Supabase connected and first page working

---

## ⚡ PHASE 0: RIGHT NOW (30 minutes)

### Step 1: Create Supabase Account (5 min)
1. Go to [https://supabase.com](https://supabase.com)
2. Click "Start your project"
3. Sign up with GitHub (easiest)
4. Verify email

### Step 2: Create Project (10 min)
1. Click "New Project"
2. Choose organization (create new if first time)
3. Project settings:
   - **Name:** `nexus-compliance-checker`
   - **Database Password:** (save this somewhere secure!)
   - **Region:** `US East` (or closest to you)
   - **Pricing:** Free (sufficient for testing)
4. Click "Create new project"
5. Wait 2-3 minutes for provisioning

### Step 3: Save Credentials (5 min)
1. Click "Settings" → "API"
2. Copy these three values:

```bash
Project URL: https://xxxxxxxxxxxxx.supabase.co
anon public key: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3M...
service_role key: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3M...
```

3. Save in a secure note/file (you'll need these)

### Step 4: Create Database Tables (10 min)
1. Click "SQL Editor" in left sidebar
2. Click "New query"
3. Copy-paste this schema:

```sql
-- Users table
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    wp_user_id BIGINT UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    tier VARCHAR(50) DEFAULT 'trial',
    license_key VARCHAR(100) UNIQUE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Test data
INSERT INTO users (wp_user_id, email, tier) 
VALUES (1, 'test@example.com', 'founders');

-- Verify
SELECT * FROM users;
```

4. Click "Run" (or F5)
5. Should see: "Success. 1 row returned."

✅ **Checkpoint:** You now have a working Supabase database!

---

## ⚡ PHASE 1: FIRST HOUR (Set Up Plugin)

### Step 1: Create Plugin Folder (5 min)

```bash
cd /workspaces/codespaces-blank/nexus-theme
cd ..  # Go to wp-content
mkdir -p plugins/nexus-compliance-manager
cd plugins/nexus-compliance-manager
```

### Step 2: Create Main Plugin File (10 min)

Create: `nexus-compliance-manager.php`

```php
<?php
/**
 * Plugin Name: Nexus Compliance Manager
 * Plugin URI: https://jdsancontrols.com
 * Description: License and user management for AutoCAD compliance plugins
 * Version: 1.0.0
 * Author: JDS Controls
 * License: GPL-2.0+
 * Text Domain: nexus-compliance-manager
 */

if (!defined('ABSPATH')) exit;

// Constants
define('NCM_VERSION', '1.0.0');
define('NCM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NCM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Supabase credentials
define('NCM_SUPABASE_URL', 'https://xxxxx.supabase.co'); // REPLACE WITH YOUR URL
define('NCM_SUPABASE_ANON_KEY', 'your-anon-key-here');   // REPLACE
define('NCM_SUPABASE_SERVICE_KEY', 'your-service-key');  // REPLACE

// Auto-load classes
spl_autoload_register(function($class) {
    if (strpos($class, 'NCM_') === 0) {
        $file = NCM_PLUGIN_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Initialize
function ncm_init() {
    if (class_exists('NCM_Core')) {
        NCM_Core::instance();
    }
}
add_action('plugins_loaded', 'ncm_init');

// Activation
register_activation_hook(__FILE__, function() {
    // Future: Create custom tables if needed
    flush_rewrite_rules();
});

// Deactivation
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
```

### Step 3: Create Supabase Integration Class (15 min)

Create: `includes/class-ncm-supabase.php`

```php
<?php
/**
 * Supabase Integration
 */

class NCM_Supabase {
    private static $instance = null;
    private $supabase_url;
    private $anon_key;
    private $service_key;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->supabase_url = NCM_SUPABASE_URL;
        $this->anon_key = NCM_SUPABASE_ANON_KEY;
        $this->service_key = NCM_SUPABASE_SERVICE_KEY;
    }
    
    /**
     * Make request to Supabase
     */
    public function request($endpoint, $method = 'GET', $data = null, $use_service = false) {
        $url = $this->supabase_url . '/rest/v1/' . $endpoint;
        
        $headers = array(
            'apikey' => $use_service ? $this->service_key : $this->anon_key,
            'Authorization' => 'Bearer ' . ($use_service ? $this->service_key : $this->anon_key),
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation'
        );
        
        $args = array(
            'method' => $method,
            'headers' => $headers,
            'timeout' => 15
        );
        
        if ($data && in_array($method, array('POST', 'PATCH'))) {
            $args['body'] = json_encode($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            error_log('Supabase Error: ' . $response->get_error_message());
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        return $decoded;
    }
    
    /**
     * Test connection
     */
    public function test_connection() {
        $result = $this->request('users?limit=1', 'GET', null, true);
        return !empty($result) || is_array($result);
    }
    
    /**
     * Get founders count
     */
    public function get_founders_count() {
        $result = $this->request('users?tier=eq.founders&select=id', 'GET', null, true);
        return is_array($result) ? count($result) : 0;
    }
}
```

### Step 4: Create Core Class (10 min)

Create: `includes/class-ncm-core.php`

```php
<?php
/**
 * Core Plugin Class
 */

class NCM_Core {
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_classes();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'register_shortcodes'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    private function load_classes() {
        NCM_Supabase::instance();
        NCM_Shortcodes::instance();
    }
    
    public function register_shortcodes() {
        // Will be implemented by NCM_Shortcodes class
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Compliance Manager',
            'Compliance',
            'manage_options',
            'ncm-dashboard',
            array($this, 'render_admin_dashboard'),
            'dashicons-shield',
            30
        );
    }
    
    public function render_admin_dashboard() {
        $supabase = NCM_Supabase::instance();
        $test = $supabase->test_connection();
        $founders_count = $supabase->get_founders_count();
        ?>
        <div class="wrap">
            <h1>Nexus Compliance Manager</h1>
            
            <div class="notice notice-<?php echo $test ? 'success' : 'error'; ?>">
                <p>
                    <strong>Supabase Status:</strong> 
                    <?php echo $test ? '✅ Connected' : '❌ Not Connected'; ?>
                </p>
            </div>
            
            <?php if ($test): ?>
            <div class="card">
                <h2>Quick Stats</h2>
                <p><strong>Founders Users:</strong> <?php echo $founders_count; ?> / 25</p>
                <p><strong>Spots Remaining:</strong> <?php echo 25 - $founders_count; ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
```

### Step 5: Create Shortcodes Class (10 min)

Create: `includes/class-ncm-shortcodes.php`

```php
<?php
/**
 * Shortcodes Handler
 */

class NCM_Shortcodes {
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('ncm_test', array($this, 'test_shortcode'));
        add_shortcode('ncm_founders_counter', array($this, 'founders_counter'));
    }
    
    /**
     * Test shortcode
     */
    public function test_shortcode($atts) {
        $supabase = NCM_Supabase::instance();
        $test = $supabase->test_connection();
        
        ob_start();
        ?>
        <div style="padding: 20px; background: <?php echo $test ? '#d4edda' : '#f8d7da'; ?>; border-radius: 5px;">
            <h3>Nexus Compliance Manager Test</h3>
            <p><strong>Plugin Status:</strong> ✅ Active</p>
            <p><strong>Supabase Status:</strong> <?php echo $test ? '✅ Connected' : '❌ Not Connected'; ?></p>
            <?php if ($test): ?>
                <p style="color: green;">🎉 Everything is working!</p>
            <?php else: ?>
                <p style="color: red;">⚠️ Check Supabase credentials</p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Founders counter shortcode
     */
    public function founders_counter($atts) {
        $atts = shortcode_atts(array(
            'total' => 25,
            'style' => 'default'
        ), $atts);
        
        $supabase = NCM_Supabase::instance();
        $count = $supabase->get_founders_count();
        $remaining = $atts['total'] - $count;
        
        ob_start();
        ?>
        <div class="ncm-founders-counter" style="padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; text-align: center;">
            <h2 style="margin: 0 0 10px 0; font-size: 3em; font-weight: bold;">
                <?php echo $remaining; ?>
            </h2>
            <p style="margin: 0; font-size: 1.2em;">
                Founders Spots Remaining
            </p>
            <p style="margin: 10px 0 0 0; opacity: 0.8; font-size: 0.9em;">
                (<?php echo $count; ?> of <?php echo $atts['total']; ?> claimed)
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
}
```

### Step 6: Activate Plugin (2 min)

```bash
# In WordPress admin:
# 1. Go to Plugins → Installed Plugins
# 2. Find "Nexus Compliance Manager"
# 3. Click "Activate"
# 4. Look for "Compliance" in sidebar menu
```

✅ **Checkpoint:** Plugin is active and connected to Supabase!

---

## ⚡ PHASE 2: SECOND HOUR (First Page Live)

### Step 1: Create Test Page (5 min)

In WordPress admin:
1. Pages → Add New
2. Title: "Plugin Test"
3. Content: `[ncm_test]`
4. Publish
5. View page

**Expected Result:** Green box saying "Everything is working!"

### Step 2: Add Founders Counter (5 min)

1. Create another page: "Founders"
2. Content: `[ncm_founders_counter]`
3. Publish
4. View page

**Expected Result:** Beautiful purple counter showing spots remaining!

### Step 3: Test Admin Dashboard (5 min)

1. Click "Compliance" in WordPress sidebar
2. Should see:
   - ✅ Supabase Connected
   - Founders count
   - Spots remaining

✅ **Checkpoint:** You have a working plugin with real-time data!

---

## ⚡ PHASE 3: THIRD HOUR (Convert First HTML Page)

### Step 1: Create Template Folder (2 min)

```bash
cd /workspaces/codespaces-blank/nexus-theme/../plugins/nexus-compliance-manager
mkdir templates
```

### Step 2: Copy Landing Page HTML (5 min)

```bash
cp /workspaces/codespaces-blank/nexus-theme/Claude_Beta\ Launch/compliance_landing.html templates/landing-page.php
```

### Step 3: Modify Template (10 min)

Edit `templates/landing-page.php`:

```php
<?php
/**
 * Landing Page Template
 */

// Get dynamic data
$supabase = NCM_Supabase::instance();
$founders_count = $supabase->get_founders_count();
$founders_remaining = 25 - $founders_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UL/NEC Compliance Checker</title>
    
    <!-- Copy the <style> section from your HTML file -->
    <style>
        /* Paste styles here */
    </style>
</head>
<body>
    <!-- Replace static counter with dynamic PHP -->
    <div class="founders-counter">
        <h2><?php echo $founders_remaining; ?></h2>
        <p>Spots Remaining</p>
    </div>
    
    <!-- Rest of your HTML -->
</body>
</html>
```

### Step 4: Add Shortcode (5 min)

In `includes/class-ncm-shortcodes.php`, add:

```php
add_shortcode('ncm_landing_page', array($this, 'landing_page'));

public function landing_page($atts) {
    ob_start();
    include NCM_PLUGIN_DIR . 'templates/landing-page.php';
    return ob_get_clean();
}
```

### Step 5: Create WordPress Page (3 min)

1. Pages → Add New
2. Title: "AutoCAD Compliance Checker"
3. Content: `[ncm_landing_page]`
4. Publish
5. Set as Front Page (Settings → Reading)

✅ **Checkpoint:** Landing page is live with real-time Founders counter!

---

## 🎉 SUCCESS! YOU'RE DONE!

### What You've Accomplished (in 3 hours):

1. ✅ Supabase database set up
2. ✅ WordPress plugin created
3. ✅ Connection working
4. ✅ First page live
5. ✅ Real-time data flowing

### What You Have Now:

```
✅ Working plugin infrastructure
✅ Supabase integration
✅ Admin dashboard
✅ Shortcode system
✅ First template converted
✅ Real-time Founders counter
```

---

## 📋 NEXT STEPS (Week 1-2)

### Tomorrow: Convert More Pages (2-3 hours each)

1. Dashboard page
2. Application form
3. Download page
4. Support center

### This Week: Core Features

1. User registration
2. License generation
3. Payment integration
4. Email setup

### Next Week: Advanced Features

1. Bug tracking
2. Feature requests
3. Founders progress
4. Admin tools

---

## 🆘 TROUBLESHOOTING

### "Supabase Not Connected"
```bash
# Check credentials in nexus-compliance-manager.php
# Make sure you replaced:
# - NCM_SUPABASE_URL
# - NCM_SUPABASE_ANON_KEY
# - NCM_SUPABASE_SERVICE_KEY
```

### "Class Not Found"
```bash
# Check file names match class names:
# class-ncm-core.php → NCM_Core
# class-ncm-supabase.php → NCM_Supabase
```

### "Shortcode Not Working"
```bash
# Make sure plugin is activated
# Check WordPress admin → Plugins
# Should see "Nexus Compliance Manager" active
```

### "Database Error"
```bash
# Go to Supabase → SQL Editor
# Run: SELECT * FROM users;
# Should return at least the test row
```

---

## 💡 TESTING CHECKLIST

After 3 hours, test these:

- [ ] Plugin appears in WordPress Plugins list
- [ ] Plugin activates without errors
- [ ] "Compliance" menu appears in admin sidebar
- [ ] Admin dashboard shows "Connected"
- [ ] Test page shows green success message
- [ ] Founders counter displays correct number
- [ ] Landing page loads without errors
- [ ] Counter updates when you add test user in Supabase

---

## 🚀 YOU'RE READY!

You now have:
- ✅ Foundation built
- ✅ Infrastructure working
- ✅ First integration complete
- ✅ Path forward clear

**Ready to continue? Let's convert the remaining 14 pages!**

Each page will take 1-2 hours following the same pattern:
1. Copy HTML to templates/
2. Add PHP for dynamic data
3. Create shortcode
4. Create WordPress page
5. Test

---

## 📞 NEED HELP?

If you get stuck on any step, let me know:
- Which step failed?
- What error message?
- What did you try?

I'll help you debug and continue! 🛠️

**Let's build this! 🚀**
