-- ================================================================
-- ADD TEST LICENSE - CORRECTED VERSION
-- ================================================================

-- Step 1: Get your user ID (replace YOUR_EMAIL with actual email)
SELECT id, email, wordpress_user_id 
FROM ulnec_users 
WHERE email = 'YOUR_EMAIL_HERE';

-- Step 2: Insert test license (replace USER_ID_HERE with the UUID from step 1)
INSERT INTO ulnec_licenses (
    user_id, 
    license_key, 
    tier, 
    status, 
    max_activations, 
    activation_count,
    expires_at
) VALUES (
    'USER_ID_HERE',
    'ULNEC-TEST-ABCD-1234-EFGH',
    'beta',
    'active',
    2,
    0,
    NOW() + INTERVAL '1 year'
);

-- Step 3: Verify license was created
SELECT * FROM ulnec_licenses ORDER BY created_at DESC LIMIT 1;

-- ================================================================
-- ALTERNATIVE: If you know the email, do it all in one query
-- ================================================================

-- This creates a license for a user by email (replace the email)
INSERT INTO ulnec_licenses (
    user_id, 
    license_key, 
    tier, 
    status, 
    max_activations, 
    activation_count,
    expires_at
)
SELECT 
    id,
    'ULNEC-TEST-ABCD-1234-EFGH',
    'beta',
    'active',
    2,
    0,
    NOW() + INTERVAL '1 year'
FROM ulnec_users
WHERE email = 'YOUR_EMAIL_HERE';

-- ================================================================
-- VERIFY IT WORKED
-- ================================================================

-- Check licenses with user info
SELECT 
    l.license_key,
    l.tier,
    l.status,
    l.activation_count,
    l.max_activations,
    l.expires_at,
    u.email,
    u.username
FROM ulnec_licenses l
JOIN ulnec_users u ON l.user_id = u.id
ORDER BY l.created_at DESC;
