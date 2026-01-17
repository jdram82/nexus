-- ================================================================
-- UL-NEC COMPLIANCE AUTOCAD PLUGIN - BETA DATABASE SCHEMA
-- ================================================================
-- Target: Supabase PostgreSQL
-- Purpose: Beta launch (simplified for single product)
-- Created: January 16, 2026
-- Run this in: Supabase SQL Editor
-- ================================================================

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ================================================================
-- 1. USERS TABLE
-- ================================================================
-- Stores user accounts (synced with WordPress)
CREATE TABLE ulnec_users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    wordpress_user_id BIGINT UNIQUE,
    email TEXT UNIQUE NOT NULL,
    name TEXT,
    tier TEXT DEFAULT 'free' CHECK (tier IN ('free', 'beta', 'pro', 'enterprise')),
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'cancelled')),
    company TEXT,
    phone TEXT,
    country TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes for performance
CREATE INDEX idx_ulnec_users_email ON ulnec_users(email);
CREATE INDEX idx_ulnec_users_wordpress_id ON ulnec_users(wordpress_user_id);
CREATE INDEX idx_ulnec_users_tier ON ulnec_users(tier);

-- ================================================================
-- 2. LICENSES TABLE
-- ================================================================
-- Stores license keys for AutoCAD plugin
CREATE TABLE ulnec_licenses (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE CASCADE,
    license_key TEXT UNIQUE NOT NULL,
    tier TEXT NOT NULL CHECK (tier IN ('free', 'beta', 'pro', 'enterprise')),
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'expired', 'revoked', 'suspended')),
    machine_id TEXT, -- Hardware ID for license activation
    activation_count INT DEFAULT 0,
    max_activations INT DEFAULT 1,
    expires_at TIMESTAMPTZ,
    activated_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_licenses_user_id ON ulnec_licenses(user_id);
CREATE INDEX idx_ulnec_licenses_key ON ulnec_licenses(license_key);
CREATE INDEX idx_ulnec_licenses_status ON ulnec_licenses(status);
CREATE INDEX idx_ulnec_licenses_machine_id ON ulnec_licenses(machine_id);

-- ================================================================
-- 3. DOWNLOADS TABLE
-- ================================================================
-- Tracks .msi file downloads for analytics
CREATE TABLE ulnec_downloads (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE CASCADE,
    license_id UUID REFERENCES ulnec_licenses(id) ON DELETE SET NULL,
    version TEXT NOT NULL,
    file_name TEXT NOT NULL,
    file_size BIGINT,
    ip_address INET,
    user_agent TEXT,
    downloaded_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_downloads_user_id ON ulnec_downloads(user_id);
CREATE INDEX idx_ulnec_downloads_license_id ON ulnec_downloads(license_id);
CREATE INDEX idx_ulnec_downloads_date ON ulnec_downloads(downloaded_at);

-- ================================================================
-- 4. SUBSCRIPTIONS TABLE
-- ================================================================
-- Stores payment subscription data (PayPal/Razorpay)
CREATE TABLE ulnec_subscriptions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE CASCADE,
    gateway TEXT NOT NULL CHECK (gateway IN ('paypal', 'razorpay')),
    subscription_id TEXT UNIQUE, -- PayPal/Razorpay subscription ID
    plan TEXT NOT NULL CHECK (plan IN ('beta', 'pro', 'enterprise')),
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'cancelled', 'expired', 'paused')),
    amount DECIMAL(10,2) NOT NULL,
    currency TEXT DEFAULT 'USD',
    billing_cycle TEXT DEFAULT 'yearly' CHECK (billing_cycle IN ('monthly', 'yearly', 'lifetime')),
    next_billing_date TIMESTAMPTZ,
    cancelled_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_subscriptions_user_id ON ulnec_subscriptions(user_id);
CREATE INDEX idx_ulnec_subscriptions_gateway ON ulnec_subscriptions(gateway);
CREATE INDEX idx_ulnec_subscriptions_status ON ulnec_subscriptions(status);

-- ================================================================
-- 5. BUG REPORTS TABLE
-- ================================================================
-- User-submitted bug reports
CREATE TABLE ulnec_bugs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE SET NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    steps_to_reproduce TEXT,
    expected_behavior TEXT,
    actual_behavior TEXT,
    severity TEXT DEFAULT 'medium' CHECK (severity IN ('critical', 'high', 'medium', 'low')),
    status TEXT DEFAULT 'open' CHECK (status IN ('open', 'in_progress', 'resolved', 'closed', 'wont_fix')),
    autocad_version TEXT,
    plugin_version TEXT,
    os_version TEXT,
    screenshots JSONB, -- Array of screenshot URLs
    admin_notes TEXT,
    resolved_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_bugs_user_id ON ulnec_bugs(user_id);
CREATE INDEX idx_ulnec_bugs_status ON ulnec_bugs(status);
CREATE INDEX idx_ulnec_bugs_severity ON ulnec_bugs(severity);
CREATE INDEX idx_ulnec_bugs_created_at ON ulnec_bugs(created_at);

-- ================================================================
-- 6. FEATURE REQUESTS TABLE
-- ================================================================
-- User-submitted feature requests with voting
CREATE TABLE ulnec_features (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE SET NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    use_case TEXT,
    status TEXT DEFAULT 'submitted' CHECK (status IN ('submitted', 'reviewing', 'planned', 'in_progress', 'completed', 'rejected')),
    priority TEXT DEFAULT 'medium' CHECK (priority IN ('critical', 'high', 'medium', 'low')),
    votes INT DEFAULT 0,
    admin_notes TEXT,
    implemented_in_version TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_features_user_id ON ulnec_features(user_id);
CREATE INDEX idx_ulnec_features_status ON ulnec_features(status);
CREATE INDEX idx_ulnec_features_votes ON ulnec_features(votes DESC);

-- ================================================================
-- 7. FEATURE VOTES TABLE
-- ================================================================
-- Tracks who voted for which feature (prevent duplicate votes)
CREATE TABLE ulnec_feature_votes (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    feature_id UUID REFERENCES ulnec_features(id) ON DELETE CASCADE,
    user_id UUID REFERENCES ulnec_users(id) ON DELETE CASCADE,
    voted_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(feature_id, user_id) -- One vote per user per feature
);

-- Indexes
CREATE INDEX idx_ulnec_feature_votes_feature_id ON ulnec_feature_votes(feature_id);
CREATE INDEX idx_ulnec_feature_votes_user_id ON ulnec_feature_votes(user_id);

-- ================================================================
-- 8. FOUNDERS PROGRAM TABLE
-- ================================================================
-- Tracks founders program participants and progress
CREATE TABLE ulnec_founders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE CASCADE,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'graduated')),
    contribution_type TEXT[], -- Array: ['bug_reports', 'feature_ideas', 'testing', 'referrals']
    bugs_submitted INT DEFAULT 0,
    features_submitted INT DEFAULT 0,
    referrals_count INT DEFAULT 0,
    points INT DEFAULT 0,
    perks JSONB, -- Array of earned perks
    notes TEXT,
    approved_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_founders_user_id ON ulnec_founders(user_id);
CREATE INDEX idx_ulnec_founders_status ON ulnec_founders(status);
CREATE INDEX idx_ulnec_founders_points ON ulnec_founders(points DESC);

-- ================================================================
-- 9. BETA APPLICATIONS TABLE
-- ================================================================
-- Stores beta access applications
CREATE TABLE ulnec_applications (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email TEXT NOT NULL,
    name TEXT NOT NULL,
    company TEXT,
    use_case TEXT NOT NULL,
    autocad_version TEXT,
    experience_level TEXT CHECK (experience_level IN ('beginner', 'intermediate', 'expert')),
    referral_source TEXT,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'waitlist')),
    admin_notes TEXT,
    approved_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_applications_email ON ulnec_applications(email);
CREATE INDEX idx_ulnec_applications_status ON ulnec_applications(status);
CREATE INDEX idx_ulnec_applications_created_at ON ulnec_applications(created_at);

-- ================================================================
-- 10. PAYMENT TRANSACTIONS TABLE
-- ================================================================
-- Stores all payment transactions for audit trail
CREATE TABLE ulnec_transactions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE SET NULL,
    subscription_id UUID REFERENCES ulnec_subscriptions(id) ON DELETE SET NULL,
    gateway TEXT NOT NULL CHECK (gateway IN ('paypal', 'razorpay')),
    transaction_id TEXT UNIQUE NOT NULL, -- PayPal/Razorpay transaction ID
    type TEXT NOT NULL CHECK (type IN ('payment', 'refund', 'chargeback')),
    status TEXT NOT NULL CHECK (status IN ('pending', 'completed', 'failed', 'refunded')),
    amount DECIMAL(10,2) NOT NULL,
    currency TEXT DEFAULT 'USD',
    gateway_response JSONB, -- Full gateway response for debugging
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_transactions_user_id ON ulnec_transactions(user_id);
CREATE INDEX idx_ulnec_transactions_subscription_id ON ulnec_transactions(subscription_id);
CREATE INDEX idx_ulnec_transactions_gateway ON ulnec_transactions(gateway);
CREATE INDEX idx_ulnec_transactions_created_at ON ulnec_transactions(created_at);

-- ================================================================
-- 11. ANALYTICS TABLE
-- ================================================================
-- Stores plugin usage analytics
CREATE TABLE ulnec_analytics (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES ulnec_users(id) ON DELETE CASCADE,
    license_id UUID REFERENCES ulnec_licenses(id) ON DELETE CASCADE,
    event_type TEXT NOT NULL, -- 'plugin_launch', 'compliance_check', 'report_generated', etc.
    event_data JSONB, -- Additional event details
    autocad_version TEXT,
    plugin_version TEXT,
    os_version TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_ulnec_analytics_user_id ON ulnec_analytics(user_id);
CREATE INDEX idx_ulnec_analytics_license_id ON ulnec_analytics(license_id);
CREATE INDEX idx_ulnec_analytics_event_type ON ulnec_analytics(event_type);
CREATE INDEX idx_ulnec_analytics_created_at ON ulnec_analytics(created_at);

-- ================================================================
-- ROW LEVEL SECURITY (RLS) POLICIES
-- ================================================================

-- Enable RLS on all tables
ALTER TABLE ulnec_users ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_licenses ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_downloads ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_subscriptions ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_bugs ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_features ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_feature_votes ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_founders ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_applications ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_transactions ENABLE ROW LEVEL SECURITY;
ALTER TABLE ulnec_analytics ENABLE ROW LEVEL SECURITY;

-- Users can read their own data
CREATE POLICY "Users can view own profile"
    ON ulnec_users FOR SELECT
    USING (auth.uid()::text = id::text);

-- Users can update their own profile
CREATE POLICY "Users can update own profile"
    ON ulnec_users FOR UPDATE
    USING (auth.uid()::text = id::text);

-- Users can view their own licenses
CREATE POLICY "Users can view own licenses"
    ON ulnec_licenses FOR SELECT
    USING (user_id::text = auth.uid()::text);

-- Users can view their own downloads
CREATE POLICY "Users can view own downloads"
    ON ulnec_downloads FOR SELECT
    USING (user_id::text = auth.uid()::text);

-- Users can insert download records
CREATE POLICY "Users can insert downloads"
    ON ulnec_downloads FOR INSERT
    WITH CHECK (user_id::text = auth.uid()::text);

-- Users can view their own subscriptions
CREATE POLICY "Users can view own subscriptions"
    ON ulnec_subscriptions FOR SELECT
    USING (user_id::text = auth.uid()::text);

-- Users can view all bugs (transparency)
CREATE POLICY "Anyone can view bugs"
    ON ulnec_bugs FOR SELECT
    TO authenticated
    USING (true);

-- Users can insert their own bug reports
CREATE POLICY "Users can insert bugs"
    ON ulnec_bugs FOR INSERT
    WITH CHECK (user_id::text = auth.uid()::text);

-- Users can view all features
CREATE POLICY "Anyone can view features"
    ON ulnec_features FOR SELECT
    TO authenticated
    USING (true);

-- Users can insert feature requests
CREATE POLICY "Users can insert features"
    ON ulnec_features FOR INSERT
    WITH CHECK (user_id::text = auth.uid()::text);

-- Users can vote on features
CREATE POLICY "Users can vote on features"
    ON ulnec_feature_votes FOR INSERT
    WITH CHECK (user_id::text = auth.uid()::text);

-- Users can view their own analytics
CREATE POLICY "Users can view own analytics"
    ON ulnec_analytics FOR SELECT
    USING (user_id::text = auth.uid()::text);

-- Users can insert analytics
CREATE POLICY "Users can insert analytics"
    ON ulnec_analytics FOR INSERT
    WITH CHECK (user_id::text = auth.uid()::text);

-- ================================================================
-- FUNCTIONS & TRIGGERS
-- ================================================================

-- Function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Apply updated_at trigger to relevant tables
CREATE TRIGGER update_ulnec_users_updated_at BEFORE UPDATE ON ulnec_users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_ulnec_licenses_updated_at BEFORE UPDATE ON ulnec_licenses
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_ulnec_subscriptions_updated_at BEFORE UPDATE ON ulnec_subscriptions
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_ulnec_bugs_updated_at BEFORE UPDATE ON ulnec_bugs
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_ulnec_features_updated_at BEFORE UPDATE ON ulnec_features
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_ulnec_founders_updated_at BEFORE UPDATE ON ulnec_founders
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Function to increment feature vote count
CREATE OR REPLACE FUNCTION increment_feature_votes()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE ulnec_features
    SET votes = votes + 1
    WHERE id = NEW.feature_id;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Trigger to auto-increment votes
CREATE TRIGGER increment_feature_votes_trigger
    AFTER INSERT ON ulnec_feature_votes
    FOR EACH ROW EXECUTE FUNCTION increment_feature_votes();

-- Function to decrement feature vote count
CREATE OR REPLACE FUNCTION decrement_feature_votes()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE ulnec_features
    SET votes = votes - 1
    WHERE id = OLD.feature_id;
    RETURN OLD;
END;
$$ language 'plpgsql';

-- Trigger to auto-decrement votes
CREATE TRIGGER decrement_feature_votes_trigger
    AFTER DELETE ON ulnec_feature_votes
    FOR EACH ROW EXECUTE FUNCTION decrement_feature_votes();

-- ================================================================
-- STORAGE BUCKETS
-- ================================================================

-- Create storage bucket for .msi files and screenshots
-- Run these in Supabase Dashboard -> Storage

-- INSERT INTO storage.buckets (id, name, public)
-- VALUES ('ulnec-downloads', 'ulnec-downloads', false);

-- INSERT INTO storage.buckets (id, name, public)
-- VALUES ('ulnec-screenshots', 'ulnec-screenshots', true);

-- ================================================================
-- SAMPLE DATA (for testing)
-- ================================================================

-- Insert test user
INSERT INTO ulnec_users (id, email, name, tier)
VALUES 
    ('00000000-0000-0000-0000-000000000001', 'test@example.com', 'Test User', 'free');

-- Insert test license
INSERT INTO ulnec_licenses (user_id, license_key, tier, max_activations, expires_at)
VALUES 
    ('00000000-0000-0000-0000-000000000001', 'ULNEC-BETA-1234-5678-90AB', 'beta', 2, NOW() + INTERVAL '1 year');

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================

-- Verify all tables created
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
AND table_name LIKE 'ulnec_%'
ORDER BY table_name;

-- Count records in each table
SELECT 
    'ulnec_users' as table_name, COUNT(*) as records FROM ulnec_users
UNION ALL SELECT 'ulnec_licenses', COUNT(*) FROM ulnec_licenses
UNION ALL SELECT 'ulnec_downloads', COUNT(*) FROM ulnec_downloads
UNION ALL SELECT 'ulnec_subscriptions', COUNT(*) FROM ulnec_subscriptions
UNION ALL SELECT 'ulnec_bugs', COUNT(*) FROM ulnec_bugs
UNION ALL SELECT 'ulnec_features', COUNT(*) FROM ulnec_features
UNION ALL SELECT 'ulnec_feature_votes', COUNT(*) FROM ulnec_feature_votes
UNION ALL SELECT 'ulnec_founders', COUNT(*) FROM ulnec_founders
UNION ALL SELECT 'ulnec_applications', COUNT(*) FROM ulnec_applications
UNION ALL SELECT 'ulnec_transactions', COUNT(*) FROM ulnec_transactions
UNION ALL SELECT 'ulnec_analytics', COUNT(*) FROM ulnec_analytics;

-- ================================================================
-- COMPLETE! 
-- ================================================================
-- Next steps:
-- 1. Run this entire file in Supabase SQL Editor
-- 2. Create storage buckets in Supabase Dashboard
-- 3. Copy your Supabase URL and anon/service keys
-- 4. Configure plugin with these credentials
-- ================================================================
