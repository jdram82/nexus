<?php
/**
 * Templates View
 * 
 * @package Nexus
 * @since 3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$license_tier = isset($license['tier']) ? $license['tier'] : 'free';
$is_pro = in_array($license_tier, array('pro', 'advanced', 'agency'));

// Template categories
$templates = array(
    'business' => array(
        'label' => 'Business',
        'items' => array(
            array('id' => 'corporate', 'name' => 'Corporate', 'pro' => false, 'image' => 'corporate.svg'),
            array('id' => 'agency', 'name' => 'Agency', 'pro' => true, 'image' => 'agency.svg'),
            array('id' => 'consulting', 'name' => 'Consulting', 'pro' => true, 'image' => 'consulting.svg'),
        ),
    ),
    'ecommerce' => array(
        'label' => 'E-Commerce',
        'items' => array(
            array('id' => 'shop', 'name' => 'Online Shop', 'pro' => true, 'image' => 'shop.svg'),
            array('id' => 'fashion', 'name' => 'Fashion Store', 'pro' => true, 'image' => 'fashion.svg'),
        ),
    ),
    'portfolio' => array(
        'label' => 'Portfolio',
        'items' => array(
            array('id' => 'creative', 'name' => 'Creative Portfolio', 'pro' => false, 'image' => 'creative.svg'),
            array('id' => 'photographer', 'name' => 'Photographer', 'pro' => true, 'image' => 'photographer.svg'),
        ),
    ),
    'blog' => array(
        'label' => 'Blog',
        'items' => array(
            array('id' => 'personal', 'name' => 'Personal Blog', 'pro' => false, 'image' => 'blog.svg'),
            array('id' => 'magazine', 'name' => 'Magazine', 'pro' => true, 'image' => 'magazine.svg'),
        ),
    ),
);
?>

<div class="wrap nexus-admin-wrap">
    <div class="nexus-admin-header">
        <h1><?php echo esc_html__('Starter Templates', 'nexus'); ?></h1>
        <p class="subtitle"><?php echo esc_html__('Import a complete website in seconds', 'nexus'); ?></p>
    </div>

    <div class="nexus-admin-content">
        <!-- Filter Tabs -->
        <div class="nexus-template-filters">
            <button class="filter-btn active" data-filter="all">
                <?php echo esc_html__('All Templates', 'nexus'); ?>
            </button>
            <?php foreach ($templates as $category => $data): ?>
                <button class="filter-btn" data-filter="<?php echo esc_attr($category); ?>">
                    <?php echo esc_html($data['label']); ?>
                </button>
            <?php endforeach; ?>
            <?php if ($is_pro): ?>
                <button class="filter-btn" data-filter="premium">
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php echo esc_html__('Premium', 'nexus'); ?>
                </button>
            <?php endif; ?>
        </div>

        <!-- Templates Grid -->
        <div class="nexus-templates-grid">
            <?php foreach ($templates as $category => $data): ?>
                <?php foreach ($data['items'] as $template): ?>
                    <?php
                    $is_locked = $template['pro'] && !$is_pro;
                    $template_class = 'template-item category-' . $category;
                    if ($template['pro']) {
                        $template_class .= ' premium-template';
                    }
                    if ($is_locked) {
                        $template_class .= ' locked';
                    }
                    ?>
                    <div class="<?php echo esc_attr($template_class); ?>">
                        <div class="template-preview">
                            <div class="template-image">
                                <?php if ($is_locked): ?>
                                    <div class="template-overlay">
                                        <span class="dashicons dashicons-lock"></span>
                                        <p><?php echo esc_html__('Pro Only', 'nexus'); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php
                                $image_url = get_template_directory_uri() . '/assets/admin/images/templates/' . $template['image'];
                                ?>
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     alt="<?php echo esc_attr($template['name']); ?>" 
                                     style="width: 100%; height: auto; display: block; border-radius: 8px 8px 0 0;" />
                            </div>
                            </div>
                            <div class="template-info">
                                <h3>
                                    <?php echo esc_html($template['name']); ?>
                                    <?php if ($template['pro']): ?>
                                        <span class="pro-badge">PRO</span>
                                    <?php endif; ?>
                                </h3>
                                <p class="template-category"><?php echo esc_html($data['label']); ?></p>
                            </div>
                        </div>
                        <div class="template-actions">
                            <?php if ($is_locked): ?>
                                <a href="https://jdsandigitel.com/shop/" class="button" target="_blank">
                                    <?php echo esc_html__('Upgrade to Pro', 'nexus'); ?>
                                </a>
                            <?php else: ?>
                                <button class="button button-primary template-import" data-template="<?php echo esc_attr($template['id']); ?>">
                                    <?php echo esc_html__('Import', 'nexus'); ?>
                                </button>
                                <button class="button template-preview-btn" data-template="<?php echo esc_attr($template['id']); ?>">
                                    <?php echo esc_html__('Preview', 'nexus'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

        <!-- Import Notice -->
        <?php if (!$is_pro): ?>
            <div class="nexus-card nexus-upgrade-notice" style="margin-top: 40px;">
                <div class="card-body" style="text-align: center;">
                    <span class="dashicons dashicons-star-filled" style="font-size: 48px; color: #f0b849;"></span>
                    <h2><?php echo esc_html__('Unlock All Templates', 'nexus'); ?></h2>
                    <p><?php echo esc_html__('Upgrade to Pro or Advanced to access 40+ premium templates', 'nexus'); ?></p>
                    <a href="https://jdsandigitel.com/shop/" class="button button-primary button-hero" target="_blank">
                        <?php echo esc_html__('View Plans', 'nexus'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Import Modal -->
<div id="nexus-import-modal" class="nexus-modal" style="display: none;">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2><?php echo esc_html__('Import Template', 'nexus'); ?></h2>
        <div class="import-options">
            <label>
                <input type="checkbox" name="import_content" checked>
                <?php echo esc_html__('Import Content', 'nexus'); ?>
            </label>
            <label>
                <input type="checkbox" name="import_customizer" checked>
                <?php echo esc_html__('Import Customizer Settings', 'nexus'); ?>
            </label>
            <label>
                <input type="checkbox" name="import_widgets" checked>
                <?php echo esc_html__('Import Widgets', 'nexus'); ?>
            </label>
        </div>
        <div class="import-warning">
            <p><strong><?php echo esc_html__('Warning:', 'nexus'); ?></strong> <?php echo esc_html__('This will import demo content. Your existing content will not be affected.', 'nexus'); ?></p>
        </div>
        <div class="modal-actions">
            <button class="button button-primary confirm-import">
                <?php echo esc_html__('Import Template', 'nexus'); ?>
            </button>
            <button class="button cancel-import">
                <?php echo esc_html__('Cancel', 'nexus'); ?>
            </button>
        </div>
        <div class="import-progress" style="display: none;">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <p class="progress-text"><?php echo esc_html__('Importing...', 'nexus'); ?></p>
        </div>
    </div>
</div>
