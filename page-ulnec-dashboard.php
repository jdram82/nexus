<?php
/**
 * Template Name: UL/NEC Dashboard
 * Description: User dashboard for UL/NEC Compliance Checker
 */

// Disable WordPress admin bar for clean app-like interface
show_admin_bar(false);

get_header();
?>

<style>
    /* Hide default WordPress elements */
    #site-header,
    .site-header,
    .page-header,
    .entry-header,
    #breadcrumbs {
        display: none !important;
    }
    
    body {
        background: #f8fafc;
        margin: 0;
        padding: 0;
    }
    
    .ulnec-dashboard-wrapper {
        display: flex;
        min-height: 100vh;
    }
    
    /* Sidebar Navigation */
    .ulnec-sidebar {
        width: 260px;
        background: #1e293b;
        color: white;
        padding: 20px 0;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }
    
    .ulnec-sidebar-header {
        padding: 0 20px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 20px;
    }
    
    .ulnec-sidebar-header h1 {
        margin: 0;
        font-size: 18px;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .ulnec-sidebar-header .logo {
        font-size: 24px;
    }
    
    .ulnec-sidebar-nav {
        padding: 0 10px;
    }
    
    .ulnec-sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .ulnec-sidebar-nav a:hover,
    .ulnec-sidebar-nav a.active {
        background: rgba(59, 130, 246, 0.2);
        color: white;
    }
    
    .ulnec-sidebar-nav .icon {
        font-size: 20px;
        width: 20px;
        text-align: center;
    }
    
    .ulnec-sidebar-footer {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
        margin-top: 20px;
    }
    
    .ulnec-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(255,255,255,0.05);
        border-radius: 8px;
    }
    
    .ulnec-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .ulnec-user-details {
        flex: 1;
        font-size: 13px;
    }
    
    .ulnec-user-name {
        font-weight: 600;
        color: white;
    }
    
    .ulnec-user-email {
        color: rgba(255,255,255,0.6);
        font-size: 12px;
    }
    
    /* Main Content */
    .ulnec-main-content {
        margin-left: 260px;
        flex: 1;
        padding: 0;
    }
    
    /* Top Bar */
    .ulnec-topbar {
        background: white;
        padding: 20px 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .ulnec-topbar h2 {
        margin: 0;
        font-size: 24px;
        color: #1e293b;
    }
    
    .ulnec-topbar-actions {
        display: flex;
        gap: 10px;
    }
    
    .ulnec-btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .ulnec-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    
    .ulnec-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }
    
    /* Dashboard Content */
    .ulnec-dashboard-content {
        padding: 30px;
    }
    
    /* Quick Stats */
    .ulnec-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .ulnec-stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .ulnec-stat-card h3 {
        margin: 0 0 10px 0;
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    
    .ulnec-stat-card .value {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }
    
    .ulnec-stat-card .change {
        font-size: 13px;
        color: #10b981;
    }
    
    .ulnec-stat-card .change.negative {
        color: #ef4444;
    }
    
    /* Content Cards */
    .ulnec-content-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 25px;
        margin-bottom: 20px;
    }
    
    .ulnec-content-card h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #1e293b;
    }
    
    @media (max-width: 768px) {
        .ulnec-sidebar {
            width: 70px;
        }
        
        .ulnec-sidebar-header span,
        .ulnec-sidebar-nav span,
        .ulnec-user-details {
            display: none;
        }
        
        .ulnec-main-content {
            margin-left: 70px;
        }
        
        .ulnec-topbar {
            padding: 15px 20px;
        }
    }
</style>

<div class="ulnec-dashboard-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="ulnec-sidebar">
        <div class="ulnec-sidebar-header">
            <h1>
                <span class="logo">⚡</span>
                <span>UL/NEC Checker</span>
            </h1>
        </div>
        
        <nav class="ulnec-sidebar-nav">
            <a href="<?php echo home_url('/dashboard'); ?>" class="active">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="#projects">
                <span class="icon">📋</span>
                <span>My Projects</span>
            </a>
            <a href="#new-check">
                <span class="icon">➕</span>
                <span>New Check</span>
            </a>
            <a href="#reports">
                <span class="icon">📄</span>
                <span>Reports</span>
            </a>
            <a href="<?php echo home_url('/bug-report'); ?>">
                <span class="icon">🐛</span>
                <span>Bug Report</span>
            </a>
            <a href="<?php echo home_url('/feature-request'); ?>">
                <span class="icon">💡</span>
                <span>Feature Request</span>
            </a>
            <a href="<?php echo home_url('/billing'); ?>">
                <span class="icon">💳</span>
                <span>Billing</span>
            </a>
            <a href="<?php echo home_url('/account-settings'); ?>">
                <span class="icon">⚙️</span>
                <span>Settings</span>
            </a>
        </nav>
        
        <div class="ulnec-sidebar-footer">
            <div class="ulnec-user-info">
                <div class="ulnec-user-avatar">👤</div>
                <div class="ulnec-user-details">
                    <div class="ulnec-user-name"><?php echo wp_get_current_user()->display_name; ?></div>
                    <div class="ulnec-user-email"><?php echo wp_get_current_user()->user_email; ?></div>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="ulnec-main-content">
        <!-- Top Bar -->
        <div class="ulnec-topbar">
            <h2>Dashboard</h2>
            <div class="ulnec-topbar-actions">
                <a href="#new-check" class="ulnec-btn ulnec-btn-primary">+ New Compliance Check</a>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="ulnec-dashboard-content">
            <?php
            // Display the dashboard shortcode content
            the_content();
            ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>
