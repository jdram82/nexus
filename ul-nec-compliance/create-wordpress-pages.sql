-- ================================================================
-- CREATE WORDPRESS PAGES FOR UL-NEC PLUGIN
-- ================================================================
-- INSTRUCTIONS:
-- 1. Export this to your WordPress database via phpMyAdmin or MySQL
-- 2. Replace 'wp_' with your actual table prefix if different
-- 3. All pages will be created as "Published" and ready to use
-- ================================================================

-- Page 1: Bug Report
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
VALUES (
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '[ulnec_bug_report]',
    'Bug Report',
    '',
    'publish',
    'closed',
    'closed',
    '',
    'bug-report',
    '',
    '',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    0,
    '',
    0,
    'page',
    '',
    0
);

-- Page 2: Feature Request
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
VALUES (
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '[ulnec_feature_request]',
    'Feature Request',
    '',
    'publish',
    'closed',
    'closed',
    '',
    'feature-request',
    '',
    '',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    0,
    '',
    0,
    'page',
    '',
    0
);

-- Page 3: Founders Progress
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
VALUES (
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '[ulnec_founders_progress]',
    'Founders Progress',
    '',
    'publish',
    'closed',
    'closed',
    '',
    'founders-progress',
    '',
    '',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    0,
    '',
    0,
    'page',
    '',
    0
);

-- Page 4: Account Settings
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
VALUES (
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '[ulnec_account_settings]',
    'Account Settings',
    '',
    'publish',
    'closed',
    'closed',
    '',
    'account-settings',
    '',
    '',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    0,
    '',
    0,
    'page',
    '',
    0
);

-- Page 5: Billing & Licenses
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
VALUES (
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '[ulnec_billing]',
    'Billing',
    '',
    'publish',
    'closed',
    'closed',
    '',
    'billing',
    '',
    '',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    0,
    '',
    0,
    'page',
    '',
    0
);

-- Note: After importing, go to WordPress Admin → Pages to verify all pages were created
