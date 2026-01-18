-- ================================================================
-- ADD IS_ADMIN COLUMN TO ULNEC_USERS TABLE
-- ================================================================
-- Purpose: Separate SaaS admin from WordPress admin
-- Run in: Supabase SQL Editor
-- Date: January 18, 2026
-- ================================================================

-- Add is_admin column (defaults to false)
ALTER TABLE ulnec_users 
ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT false;

-- Create index for performance
CREATE INDEX IF NOT EXISTS idx_ulnec_users_is_admin ON ulnec_users(is_admin);

-- Set durgaram@jdsancontrols.com as SaaS admin
UPDATE ulnec_users 
SET is_admin = true 
WHERE email = 'durgaram@jdsancontrols.com';

-- Verify the change
SELECT id, email, name, tier, is_admin, created_at 
FROM ulnec_users 
WHERE is_admin = true;

-- ================================================================
-- NOTES:
-- - is_admin = true: Can manage UL-NEC SaaS (users, licenses, bugs)
-- - WordPress admin: Can manage WordPress itself (themes, plugins)
-- - These are now SEPARATE roles for better security
-- ================================================================
