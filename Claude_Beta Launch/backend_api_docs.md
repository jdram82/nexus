# Backend API Structure Documentation
## UL/NEC Compliance Checker - Complete API Reference

---

## 🎯 PURPOSE OF BACKEND API

### What is a Backend API?
A Backend API (Application Programming Interface) is the "brain" behind your website. While users see the beautiful pages you created (the "frontend"), the backend API handles all the important business logic:

**Think of it like a restaurant:**
- **Frontend** = The dining room where customers sit and order (your HTML pages)
- **Backend API** = The kitchen where food is prepared (your server handling data)
- **Database** = The pantry storing ingredients (where user data is stored)

### Why You Need It

**1. Data Storage & Retrieval**
- Store user accounts, licenses, bug reports, feature requests
- Retrieve information when users log in or view their dashboard
- Keep track of who's a Founder, Early Adopter, or Beta Tester

**2. Security**
- Verify license keys are valid
- Check if user has permission to access certain features
- Process payments securely through Stripe
- Prevent unauthorized access to user data

**3. Business Logic**
- Calculate if Founders completed their 4 requirements
- Automatically move users between tiers
- Send emails when trials end or payments fail
- Update the "spots remaining" counter

**4. Integration**
- Connect to Stripe for payments
- Connect to email service (SendGrid) for notifications
- Connect to file storage for uploaded videos/screenshots
- Connect to analytics to track user behavior

### Example Flow:
```
User clicks "Download" button
    ↓
Frontend sends request to API: "Give me download link"
    ↓
API checks: Is user logged in? Do they have valid license?
    ↓
API queries database: Get user info, check license status
    ↓
API responds: "Here's your download link and license key"
    ↓
Frontend displays the download page
```

---

## 🏗️ API ARCHITECTURE

### Technology Stack Recommendation

**Option 1: Node.js (Easiest)**
```
- Runtime: Node.js v18+
- Framework: Express.js
- Database: PostgreSQL or MongoDB
- ORM: Prisma or Mongoose
- Authentication: JWT (JSON Web Tokens)
```

**Option 2: Python (Great for data processing)**
```
- Runtime: Python 3.10+
- Framework: FastAPI or Django
- Database: PostgreSQL
- ORM: SQLAlchemy or Django ORM
- Authentication: JWT
```

**Option 3: PHP (Traditional hosting)**
```
- Runtime: PHP 8.1+
- Framework: Laravel
- Database: MySQL
- ORM: Eloquent
- Authentication: Laravel Sanctum
```

---

## 📊 DATABASE SCHEMA

### Tables Structure

```sql
-- Users Table
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company_name VARCHAR(255),
    job_title VARCHAR(255),
    company_size VARCHAR(50),
    tier VARCHAR(50) NOT NULL, -- 'founders', 'early_adopter', 'beta_tester'
    status VARCHAR(50) DEFAULT 'active', -- 'active', 'cancelled', 'expired'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Licenses Table
CREATE TABLE licenses (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    license_key VARCHAR(50) UNIQUE NOT NULL,
    tier VARCHAR(50) NOT NULL,
    activated_at TIMESTAMP,
    expires_at TIMESTAMP,
    activations_remaining INT DEFAULT 1,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- License Activations Table
CREATE TABLE license_activations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    license_id UUID REFERENCES licenses(id),
    computer_name VARCHAR(255),
    computer_id VARCHAR(255),
    activated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP,
    status VARCHAR(50) DEFAULT 'active'
);

-- Founders Progress Table
CREATE TABLE founders_progress (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    bug_reports_count INT DEFAULT 0,
    video_submitted BOOLEAN DEFAULT false,
    video_url VARCHAR(500),
    video_submitted_at TIMESTAMP,
    case_study_complete BOOLEAN DEFAULT false,
    case_study_approved_at TIMESTAMP,
    linkedin_posted BOOLEAN DEFAULT false,
    linkedin_url VARCHAR(500),
    linkedin_posted_at TIMESTAMP,
    deadline TIMESTAMP NOT NULL,
    requirements_completed BOOLEAN DEFAULT false,
    completed_at TIMESTAMP
);

-- Bug Reports Table
CREATE TABLE bug_reports (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    bug_id VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    steps_to_reproduce TEXT,
    expected_behavior TEXT,
    actual_behavior TEXT,
    cad_version VARCHAR(100),
    windows_version VARCHAR(100),
    plugin_version VARCHAR(50),
    severity VARCHAR(50), -- 'critical', 'major', 'minor', 'enhancement'
    status VARCHAR(50) DEFAULT 'new', -- 'new', 'in_progress', 'fixed', 'closed'
    attachments JSONB, -- Array of file URLs
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Feature Requests Table
CREATE TABLE feature_requests (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    feature_id VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    importance TEXT,
    use_case TEXT,
    category VARCHAR(50), -- 'ui', 'compliance', 'export', 'integration', 'performance', 'other'
    priority VARCHAR(50), -- 'critical', 'high', 'medium', 'low'
    workaround TEXT,
    willing_to_pay VARCHAR(50),
    votes_count INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'new', -- 'new', 'under_review', 'planned', 'in_progress', 'completed', 'declined'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Feature Votes Table
CREATE TABLE feature_votes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    feature_request_id UUID REFERENCES feature_requests(id),
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, feature_request_id)
);

-- Subscriptions Table (Stripe Integration)
CREATE TABLE subscriptions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    stripe_subscription_id VARCHAR(255) UNIQUE,
    stripe_customer_id VARCHAR(255),
    tier VARCHAR(50) NOT NULL,
    status VARCHAR(50), -- 'active', 'cancelled', 'past_due', 'unpaid'
    current_period_start TIMESTAMP,
    current_period_end TIMESTAMP,
    cancel_at_period_end BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment History Table
CREATE TABLE payments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    subscription_id UUID REFERENCES subscriptions(id),
    stripe_payment_id VARCHAR(255),
    amount DECIMAL(10,2),
    currency VARCHAR(10) DEFAULT 'USD',
    status VARCHAR(50), -- 'succeeded', 'failed', 'pending'
    description VARCHAR(500),
    invoice_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Email Notifications Log
CREATE TABLE email_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    email_type VARCHAR(100), -- 'welcome', 'trial_reminder', 'payment_success', etc.
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) -- 'sent', 'failed', 'bounced'
);

-- Activity Log
CREATE TABLE activity_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    action VARCHAR(255), -- 'compliance_check', 'pdf_export', 'license_activated', etc.
    details JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔌 API ENDPOINTS

### Authentication & User Management

#### POST /api/auth/register
**Purpose:** Create new user account
```json
Request:
{
  "email": "john@example.com",
  "password": "SecurePass123!",
  "fullName": "John Smith",
  "companyName": "ABC Engineering",
  "jobTitle": "Senior Engineer",
  "companySize": "11-50",
  "tier": "early_adopter"
}

Response (201 Created):
{
  "success": true,
  "userId": "uuid-here",
  "message": "Account created successfully",
  "token": "jwt-token-here"
}
```

#### POST /api/auth/login
**Purpose:** User login
```json
Request:
{
  "email": "john@example.com",
  "password": "SecurePass123!"
}

Response (200 OK):
{
  "success": true,
  "token": "jwt-token-here",
  "user": {
    "id": "uuid",
    "email": "john@example.com",
    "fullName": "John Smith",
    "tier": "founders"
  }
}
```

#### POST /api/auth/forgot-password
**Purpose:** Send password reset email
```json
Request:
{
  "email": "john@example.com"
}

Response (200 OK):
{
  "success": true,
  "message": "Password reset email sent"
}
```

#### POST /api/auth/reset-password
**Purpose:** Reset password with token
```json
Request:
{
  "token": "reset-token-from-email",
  "newPassword": "NewSecurePass123!"
}

Response (200 OK):
{
  "success": true,
  "message": "Password reset successfully"
}
```

---

### Founders Tier Applications

#### POST /api/founders/apply
**Purpose:** Submit Founders tier application
```json
Request:
{
  "fullName": "John Smith",
  "email": "john@example.com",
  "phone": "+1234567890",
  "companyName": "ABC Engineering",
  "jobTitle": "Senior Electrical Engineer",
  "companySize": "11-50",
  "industry": "manufacturing",
  "panelsPerMonth": "6-15",
  "cadVersion": "AutoCAD 2025",
  "challenge": "Time-consuming SCCR calculations...",
  "linkedinUrl": "https://linkedin.com/in/johnsmith",
  "agreeRequirements": true,
  "agreeTerms": true,
  "agreeMarketing": false
}

Response (201 Created):
{
  "success": true,
  "applicationId": "uuid",
  "message": "Application submitted successfully",
  "status": "pending" // or "approved" if auto-approved
}
```

#### GET /api/founders/spots-remaining
**Purpose:** Get number of Founders spots left
```json
Response (200 OK):
{
  "total": 25,
  "taken": 18,
  "remaining": 7
}
```

#### GET /api/founders/progress
**Purpose:** Get user's Founders requirements progress
**Auth:** Required
```json
Response (200 OK):
{
  "userId": "uuid",
  "deadline": "2026-03-15T00:00:00Z",
  "daysRemaining": 42,
  "progressPercent": 50,
  "requirements": {
    "bugReports": {
      "completed": true,
      "count": 3,
      "required": 3,
      "submissions": [
        {
          "bugId": "BUG-2026-001",
          "title": "SCCR calculation issue",
          "status": "fixed",
          "submittedAt": "2026-01-05"
        }
      ]
    },
    "videoTestimonial": {
      "completed": false,
      "videoUrl": null,
      "submittedAt": null
    },
    "caseStudy": {
      "completed": true,
      "approvedAt": "2026-01-14",
      "publishedUrl": "/case-studies/john-smith"
    },
    "linkedinPost": {
      "completed": false,
      "postUrl": null,
      "postedAt": null
    }
  },
  "requirementsCompleted": false
}
```

#### POST /api/founders/upload-video
**Purpose:** Upload video testimonial
**Auth:** Required
```json
Request (multipart/form-data):
{
  "video": <file>
}

Response (200 OK):
{
  "success": true,
  "videoUrl": "https://cdn.example.com/videos/user-uuid.mp4",
  "message": "Video uploaded successfully"
}
```

#### POST /api/founders/linkedin-posted
**Purpose:** Mark LinkedIn requirement as complete
**Auth:** Required
```json
Request:
{
  "linkedinUrl": "https://linkedin.com/posts/..."
}

Response (200 OK):
{
  "success": true,
  "message": "LinkedIn post verified"
}
```

---

### Dashboard & User Data

#### GET /api/dashboard/stats
**Purpose:** Get user dashboard statistics
**Auth:** Required
```json
Response (200 OK):
{
  "panelsValidated": 47,
  "timeSaved": 23.5,
  "errorsCaught": 156,
  "complianceRate": 98,
  "thisMonthValidations": 12
}
```

#### GET /api/dashboard/activity
**Purpose:** Get recent user activity
**Auth:** Required
```json
Response (200 OK):
{
  "activities": [
    {
      "id": "uuid",
      "action": "compliance_check",
      "description": "Completed check on Panel_Drawing_2024_03.dwg",
      "timestamp": "2026-01-16T14:30:00Z",
      "details": {
        "fileName": "Panel_Drawing_2024_03.dwg",
        "warningsFound": 2
      }
    }
  ]
}
```

---

### License Management

#### GET /api/license/key
**Purpose:** Get user's license key
**Auth:** Required
```json
Response (200 OK):
{
  "licenseKey": "ABC1-2DEF-3GHI-4JKL",
  "tier": "founders",
  "status": "active",
  "expiresAt": "2027-03-01T00:00:00Z",
  "activationsRemaining": 1
}
```

#### POST /api/license/activate
**Purpose:** Activate license on a computer
**Auth:** Required
```json
Request:
{
  "licenseKey": "ABC1-2DEF-3GHI-4JKL",
  "computerName": "WORKSTATION-01",
  "computerId": "unique-hardware-id"
}

Response (200 OK):
{
  "success": true,
  "message": "License activated successfully",
  "activationId": "uuid",
  "activatedAt": "2026-01-16T15:00:00Z"
}
```

#### POST /api/license/deactivate
**Purpose:** Deactivate license from a computer
**Auth:** Required
```json
Request:
{
  "activationId": "uuid"
}

Response (200 OK):
{
  "success": true,
  "message": "License deactivated successfully"
}
```

#### GET /api/license/activations
**Purpose:** Get all active license activations
**Auth:** Required
```json
Response (200 OK):
{
  "activations": [
    {
      "id": "uuid",
      "computerName": "WORKSTATION-01",
      "activatedAt": "2026-01-01T10:00:00Z",
      "lastUsedAt": "2026-01-16T14:30:00Z"
    }
  ]
}
```

---

### Bug Reports

#### POST /api/bugs/submit
**Purpose:** Submit a bug report
**Auth:** Required
```json
Request (multipart/form-data):
{
  "title": "SCCR calculation incorrect",
  "description": "When calculating SCCR for 3-phase...",
  "stepsToReproduce": "1. Open drawing\n2. Run check...",
  "expectedBehavior": "Should calculate correctly",
  "actualBehavior": "Shows incorrect value",
  "cadVersion": "AutoCAD 2025",
  "windowsVersion": "Windows 11",
  "pluginVersion": "1.0 Beta",
  "severity": "major",
  "attachments": [<file1>, <file2>]
}

Response (201 Created):
{
  "success": true,
  "bugId": "BUG-2026-042",
  "message": "Bug report submitted successfully"
}
```

#### GET /api/bugs/my-reports
**Purpose:** Get user's bug reports
**Auth:** Required
```json
Response (200 OK):
{
  "bugReports": [
    {
      "bugId": "BUG-2026-042",
      "title": "SCCR calculation incorrect",
      "severity": "major",
      "status": "in_progress",
      "submittedAt": "2026-01-15T10:00:00Z",
      "updatedAt": "2026-01-16T09:00:00Z"
    }
  ],
  "total": 3
}
```

---

### Feature Requests

#### POST /api/features/submit
**Purpose:** Submit a feature request
**Auth:** Required
```json
Request:
{
  "title": "Export to Excel",
  "description": "Add ability to export reports as Excel...",
  "importance": "Would save time on data analysis",
  "useCase": "After compliance check, export to Excel for...",
  "category": "export",
  "priority": "high",
  "workaround": "Currently copy/paste to Excel manually",
  "willingToPay": "yes"
}

Response (201 Created):
{
  "success": true,
  "featureId": "FEAT-2026-089",
  "message": "Feature request submitted successfully"
}
```

#### POST /api/features/vote
**Purpose:** Vote for a feature request
**Auth:** Required
```json
Request:
{
  "featureId": "FEAT-2026-089"
}

Response (200 OK):
{
  "success": true,
  "voted": true,
  "newVoteCount": 48
}
```

#### GET /api/features/popular
**Purpose:** Get popular feature requests
```json
Response (200 OK):
{
  "features": [
    {
      "featureId": "FEAT-2026-089",
      "title": "Export to Excel",
      "description": "...",
      "votesCount": 47,
      "category": "export",
      "status": "under_review",
      "userVoted": true
    }
  ]
}
```

---

### Billing & Payments

#### GET /api/billing/subscription
**Purpose:** Get subscription details
**Auth:** Required
```json
Response (200 OK):
{
  "subscriptionId": "sub_founders_12345",
  "tier": "founders",
  "status": "active",
  "currentPeriodStart": "2026-01-01T00:00:00Z",
  "currentPeriodEnd": "2027-01-01T00:00:00Z",
  "cancelAtPeriodEnd": false,
  "nextPaymentAmount": 149.00,
  "nextPaymentDate": "2027-01-01T00:00:00Z"
}
```

#### GET /api/billing/payment-method
**Purpose:** Get payment method
**Auth:** Required
```json
Response (200 OK):
{
  "paymentMethod": {
    "type": "card",
    "brand": "visa",
    "last4": "4242",
    "expiryMonth": 12,
    "expiryYear": 2026
  }
}
```

#### POST /api/billing/update-payment-method
**Purpose:** Update payment method
**Auth:** Required
```json
Request:
{
  "stripePaymentMethodId": "pm_xxx"
}

Response (200 OK):
{
  "success": true,
  "message": "Payment method updated"
}
```

#### GET /api/billing/history
**Purpose:** Get payment history
**Auth:** Required
```json
Response (200 OK):
{
  "payments": [
    {
      "id": "uuid",
      "amount": 0.00,
      "currency": "USD",
      "status": "succeeded",
      "description": "Founders Tier - First Year",
      "date": "2026-01-01T00:00:00Z",
      "invoiceUrl": "https://invoice-url"
    }
  ]
}
```

#### POST /api/billing/cancel
**Purpose:** Cancel subscription
**Auth:** Required
```json
Request:
{
  "reason": "user_requested",
  "feedback": "Optional cancellation feedback"
}

Response (200 OK):
{
  "success": true,
  "message": "Subscription will cancel at period end",
  "accessUntil": "2027-01-01T00:00:00Z"
}
```

---

### User Settings

#### GET /api/user/profile
**Purpose:** Get user profile
**Auth:** Required
```json
Response (200 OK):
{
  "id": "uuid",
  "email": "john@example.com",
  "fullName": "John Smith",
  "phone": "+1234567890",
  "companyName": "ABC Engineering",
  "jobTitle": "Senior Electrical Engineer",
  "companySize": "11-50"
}
```

#### PUT /api/user/profile
**Purpose:** Update user profile
**Auth:** Required
```json
Request:
{
  "fullName": "John A. Smith",
  "phone": "+1234567891",
  "jobTitle": "Lead Electrical Engineer"
}

Response (200 OK):
{
  "success": true,
  "message": "Profile updated successfully"
}
```

#### POST /api/user/change-password
**Purpose:** Change password
**Auth:** Required
```json
Request:
{
  "currentPassword": "OldPass123!",
  "newPassword": "NewPass456!"
}

Response (200 OK):
{
  "success": true,
  "message": "Password changed successfully"
}
```

#### GET /api/user/notifications-preferences
**Purpose:** Get notification preferences
**Auth:** Required
```json
Response (200 OK):
{
  "productUpdates": true,
  "releaseNotes": true,
  "billing": true,
  "security": true,
  "support": false,
  "beta": true,
  "founders": true,
  "marketing": false,
  "newsletter": false
}
```

#### PUT /api/user/notifications-preferences
**Purpose:** Update notification preferences
**Auth:** Required
```json
Request:
{
  "productUpdates": true,
  "marketing": true
}

Response (200 OK):
{
  "success": true,
  "message": "Preferences updated"
}
```

#### DELETE /api/user/account
**Purpose:** Delete user account
**Auth:** Required
```json
Request:
{
  "confirmation": "DELETE"
}

Response (200 OK):
{
  "success": true,
  "message": "Account scheduled for deletion"
}
```

---

### Admin Endpoints

#### GET /api/admin/dashboard/stats
**Purpose:** Get admin dashboard overview
**Auth:** Admin Required
```json
Response (200 OK):
{
  "totalUsers": 147,
  "foundersTier": 22,
  "earlyAdopters": 68,
  "betaTesters": 57,
  "revenue": {
    "thisMonth": 18940.00,
    "lastMonth": 15200.00,
    "allTime": 33575.00
  },
  "activeTrials": 34,
  "trialConversionRate": 72,
  "supportTickets": {
    "open": 8,
    "pending": 3,
    "resolved": 156
  }
}
```

#### GET /api/admin/users
**Purpose:** Get all users with filters
**Auth:** Admin Required
```json
Query Parameters:
?tier=founders&status=active&page=1&limit=50

Response (200 OK):
{
  "users": [...],
  "total": 147,
  "page": 1,
  "totalPages": 3
}
```

#### PUT /api/admin/users/:userId/tier
**Purpose:** Change user tier
**Auth:** Admin Required
```json
Request:
{
  "tier": "founders",
  "reason": "Completed all requirements"
}

Response (200 OK):
{
  "success": true,
  "message": "User tier updated"
}
```

---

## 🔒 AUTHENTICATION & SECURITY

### JWT Token Structure
```javascript
{
  "userId": "uuid",
  "email": "john@example.com",
  "tier": "founders",
  "role": "user", // or "admin"
  "iat": 1642521600,
  "exp": 1643126400
}
```

### Security Best Practices
1. **Password Hashing:** Use bcrypt with 10+ rounds
2. **Rate Limiting:** Max 100 requests/minute per IP
3. **CORS:** Only allow requests from your domain
4. **SQL Injection:** Use parameterized queries
5. **XSS Protection:** Sanitize all user inputs
6. **HTTPS Only:** Enforce SSL/TLS
7. **API Keys:** Never expose in frontend code

---

## 📧 EMAIL TRIGGERS

### Automated Emails
```javascript
// Email Service Integration
const emailTriggers = {
  // User Registration
  'user.registered': {
    template: 'welcome_email',
    to: user.email,
    data: { userName, downloadLink }
  },
  
  // Founders Application
  'founders.application_received': {
    template: 'founders_application_received',
    delay: '0 minutes'
  },
  'founders.application_approved': {
    template: 'founders_approved',
    delay: '0 minutes'
  },
  
  // Founders Progress
  'founders.day_7': {
    template: 'founders_checkin_day7',
    delay: '7 days after signup'
  },
  'founders.day_30': {
    template: 'founders_halfway',
    delay: '30 days after signup'
  },
  'founders.day_45': {
    template: 'founders_urgent_15days',
    delay: '45 days after signup'
  },
  'founders.day_55': {
    template: 'founders_final_notice',
    delay: '55 days after signup'
  },
  
  // Trial & Payment
  'trial.day_3': {
    template: 'trial_checkin',
    delay: '3 days after trial start'
  },
  'trial.day_20': {
    template: 'trial_payment_reminder',
    delay: '20 days after trial start'
  },
  'payment.success': {
    template: 'payment_success',
    delay: '0 minutes'
  },
  'payment.failed': {
    template: 'payment_failed',
    delay: '0 minutes'
  }
};
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Before Going Live

**1. Environment Variables**
```bash
# .env file
DATABASE_URL=postgresql://user:pass@host:5432/db
JWT_SECRET=your-super-secret-key-here
STRIPE_SECRET_KEY=sk_live_...
SENDGRID_API_KEY=SG...
AWS_S3_BUCKET=your-bucket-name
FRONTEND_URL=https://jdsancontrols.com
```

**2. Database Migrations**
- Create all tables
- Add indexes for performance
- Set up backup schedule

**3. API Testing**
- Test all endpoints
- Load testing (100+ concurrent users)
- Security audit

**4. Monitoring**
- Error tracking (Sentry)
- Performance monitoring (New Relic)
- Uptime monitoring (Pingdom)

**5. Documentation**
- API documentation (Swagger/OpenAPI)
- Internal developer docs
- Admin user guide

---

## 💻 SAMPLE CODE IMPLEMENTATION

### Node.js/Express Example

```javascript
// server.js
const express = require('express');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');
const { Pool } = require('pg');

const app = express();
const db = new Pool({ connectionString: process.env.DATABASE_URL });

app.use(express.json());

// Middleware: Verify JWT
const authenticate = async (req, res, next) => {
  try {
    const token = req.headers.authorization?.split(' ')[1];
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.user = decoded;
    next();
  } catch (error) {
    res.status(401).json({ error: 'Unauthorized' });
  }
};

// POST /api/auth/login
app.post('/api/auth/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    
    // Get user from database
    const result = await db.query(
      'SELECT * FROM users WHERE email = $1',
      [email]
    );
    
    if (result.rows.length === 0) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }
    
    const user = result.rows[0];
    
    // Verify password
    const validPassword = await bcrypt.compare(password, user.password_hash);
    if (!validPassword) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }
    
    // Generate JWT
    const token = jwt.sign(
      { 
        userId: user.id, 
        email: user.email, 
        tier: user.tier 
      },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );
    
    res.json({
      success: true,
      token,
      user: {
        id: user.id,
        email: user.email,
        fullName: user.full_name,
        tier: user.tier
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Server error' });
  }
});

// GET /api/dashboard/stats
app.get('/api/dashboard/stats', authenticate, async (req, res) => {
  try {
    const stats = await db.query(