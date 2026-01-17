# Nexus Theme v3.2.1 - Release Notes

**Release Date:** January 11, 2026  
**Version:** 3.2.1  
**Type:** Major Feature Release - Theme Builder Widgets

---

## 🎉 Major New Features

### Theme Builder - 12 New Widgets Added

Massive expansion of the Theme Builder with 12 professional widgets, bringing the total to **20 widgets**. All widgets are fully responsive, customizable, and production-ready.

---

## 🆕 New Widgets

### Medium Priority Widgets (8)

#### 1. **Animated Headline Widget**
Create eye-catching animated text headlines with word rotation.

**Features:**
- 7 animation types: Fade, Slide, Clip, Zoom, Rotate, Flip, Typing
- Rotating word display with customizable timing
- Before/after text support
- Speed and pause duration controls
- Full typography customization
- 9 HTML tag options (h1-h6, p, span, div)
- Alignment controls

**Use Cases:**
- Hero sections with dynamic messaging
- Attention-grabbing headlines
- Marketing promotions with rotating offers

---

#### 2. **Price List Widget**
Display products or services with prices in a professional format.

**Features:**
- Unlimited items via repeater field
- Image support for each item
- 2 layout options: Inline, Stacked
- 3 separator styles: Dotted, Dashed, Solid
- Clickable titles with custom links
- Item descriptions
- Customizable colors and typography

**Use Cases:**
- Restaurant menus
- Service pricing
- Product catalogs
- Package comparisons

---

#### 3. **Price Table Widget**
Create beautiful pricing comparison tables for plans and packages.

**Features:**
- 1-4 column layouts with CSS Grid
- Ribbon badges for featured plans
- Feature lists with checkmark icons
- Button CTAs per plan
- Featured plan highlighting
- Extensive color customization
- Responsive design with mobile stacking
- Hover effects and animations

**Use Cases:**
- SaaS pricing pages
- Membership plans
- Subscription packages
- Service tiers

---

#### 4. **Flip Box Widget**
Interactive cards that flip to reveal content on hover.

**Features:**
- 5 flip effects: Horizontal, Vertical, Fade, Zoom-In, Zoom-Out
- Front and back content areas
- Icon support on both sides
- CTA button on back side
- Customizable height
- Individual styling for front/back
- Smooth 3D transforms

**Use Cases:**
- Team member profiles
- Service showcases
- Feature highlights
- Call-to-action cards

---

#### 5. **Media Carousel Widget**
Versatile carousel supporting both images and videos.

**Features:**
- Mixed image/video content
- YouTube and Vimeo embed support
- Thumbnail navigation (bottom/left/right positions)
- Autoplay with custom speed
- Arrow navigation with customizable positioning
- Image captions (title + description)
- Custom image height
- Pause on hover
- Infinite loop

**Use Cases:**
- Portfolio galleries
- Product showcases
- Video playlists
- Before/after comparisons

---

#### 6. **Countdown Timer Widget**
Live countdown timer with multiple trigger options.

**Features:**
- Fixed date OR evergreen countdown
- Live JavaScript updates every second
- Customizable labels (days/hours/minutes/seconds)
- 4 expire actions: None, Hide, Message, Redirect
- Inline/block layout options
- Individual box styling
- Custom colors and typography
- Digit and label size controls

**Use Cases:**
- Product launches
- Event countdowns
- Limited-time offers
- Webinar registrations
- Flash sales

---

#### 7. **Testimonial Carousel Widget**
Showcase customer reviews and testimonials professionally.

**Features:**
- Display 1-3 slides simultaneously
- Star ratings (0-5 stars, half-star support)
- Author images and titles
- 3 layout styles: Classic, Bubble, Card
- Autoplay with pause on hover
- Arrow and dot navigation
- Infinite loop
- Customizable colors and typography

**Use Cases:**
- Customer reviews
- Case studies
- Social proof sections
- Client testimonials

---

#### 8. **Share Buttons Widget**
Social media sharing buttons for content distribution.

**Features:**
- 9 social networks: Facebook, Twitter, LinkedIn, Pinterest, Reddit, WhatsApp, Telegram, Email, Print
- 4 button styles: Flat, Framed, Minimal, Rounded
- Official brand colors or custom colors
- Icon/Text/Icon+Text display modes
- 4 hover effects: None, Fade, Grow, Shrink
- Horizontal/vertical layouts
- Share counter option (coming soon)
- Current or custom URL sharing

**Use Cases:**
- Blog post sharing
- Product promotion
- Content marketing
- Social engagement

---

### Lower Priority Widgets (4)

#### 9. **Blockquote Widget**
Beautifully styled quotations with author attribution.

**Features:**
- 4 style presets: Classic, Modern, Minimal, Bordered
- Quote icon with 3 positions: Before, After, Corner
- Author name, title, and image
- Typography controls
- Custom border and padding
- Alignment options
- Color customization

**Use Cases:**
- Testimonials
- Pull quotes
- Customer feedback
- Expert opinions

---

#### 10. **Video Playlist Widget**
Create video playlists with clickable thumbnails.

**Features:**
- YouTube and Vimeo support
- Auto-generated thumbnails from YouTube
- Custom thumbnail upload option
- Duration badges
- 3 playlist positions: Right, Left, Bottom
- Autoplay first video option
- Play icons on thumbnails
- Video titles and descriptions
- Responsive layout

**Use Cases:**
- Video courses
- Tutorial series
- Product demos
- Event recordings

---

#### 11. **Progress Tracker Widget**
Visual step-by-step progress indicator.

**Features:**
- Horizontal/vertical layouts
- 3 status states: Completed, Active, Pending
- 4 style presets: Default, Minimal, Modern, Circle
- Numbered or icon markers
- Connector lines between steps
- Status-based coloring (green/blue/gray)
- Step descriptions
- Responsive design

**Use Cases:**
- Order tracking
- Onboarding flows
- Process steps
- Project milestones
- Form wizards

---

#### 12. **Lottie Animation Widget**
Display JSON-based Lottie animations.

**Features:**
- 3 source types: External URL, File Upload, Paste JSON code
- 4 trigger options: Autoplay, Viewport Scroll, Hover, Click
- Loop and reverse controls
- Speed adjustment (0.1x - 3x)
- Start/end point trimming
- 3 renderers: SVG, Canvas, HTML
- CSS filters: Blur, Brightness, Contrast, Saturation, Hue Rotation
- Optional link wrapper
- Lottie Web library integration (v5.12.2)
- IntersectionObserver for viewport trigger

**Use Cases:**
- Animated icons
- Loading animations
- Interactive illustrations
- Brand animations
- Hero section effects

---

## 🎨 Technical Implementation

### Widget Architecture
- **Total Widgets:** 20
- **New Widget Code:** ~4,075 lines
- **CSS Assets:** ~1,050 lines added
- **JavaScript Assets:** ~420 lines added
- **Total New Code:** ~5,545 lines

### Widget Categories
- **Content:** 14 widgets
- **Media:** 4 widgets
- **Forms:** 1 widget
- **WordPress:** 1 widget

### Control System
All widgets feature comprehensive controls:
- 15+ control types (text, textarea, select, checkbox, slider, color, url, media, repeater, icon, datetime)
- Repeater fields for dynamic content
- Typography controls
- Color pickers
- Spacing controls
- Border controls

### Frontend Features
- Inline CSS styling from settings
- Data attributes for JavaScript
- Dashicons icon library
- Responsive breakpoints
- Hover effects and animations
- Smooth transitions

### JavaScript Functionality
Interactive features powered by jQuery:
- **Animated Headline:** Word rotation with timing control
- **Media Carousel:** Slide navigation, thumbnails, autoplay
- **Countdown:** Live updates, expire actions
- **Testimonial Carousel:** Multi-slide navigation, dots, arrows
- **Video Playlist:** Video switching via iframe
- **Lottie:** Animation triggers and controls

### External Dependencies
- **Lottie Web Library:** v5.12.2 (CDN)
  - URL: `https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js`
  - Used by: Lottie Animation widget
  - Lightweight and performant

---

## 📦 Files Added

### Widget Class Files (12):
1. `/pro/theme-builder/widgets/class-animated-headline-widget.php` (340 lines)
2. `/pro/theme-builder/widgets/class-price-list-widget.php` (310 lines)
3. `/pro/theme-builder/widgets/class-price-table-widget.php` (355 lines)
4. `/pro/theme-builder/widgets/class-flip-box-widget.php` (330 lines)
5. `/pro/theme-builder/widgets/class-media-carousel-widget.php` (380 lines)
6. `/pro/theme-builder/widgets/class-countdown-widget.php` (360 lines)
7. `/pro/theme-builder/widgets/class-testimonial-carousel-widget.php` (370 lines)
8. `/pro/theme-builder/widgets/class-share-buttons-widget.php` (365 lines)
9. `/pro/theme-builder/widgets/class-blockquote-widget.php` (280 lines)
10. `/pro/theme-builder/widgets/class-video-playlist-widget.php` (385 lines)
11. `/pro/theme-builder/widgets/class-progress-tracker-widget.php` (340 lines)
12. `/pro/theme-builder/widgets/class-lottie-widget.php` (360 lines)

### Documentation:
- `/PHASE_4_COMPLETE.md` - Complete implementation summary

---

## 🔧 Files Modified

1. `/pro/theme-builder/class-builder-widgets.php`
   - Added 12 widget registrations
   - Auto-loading via glob() pattern

2. `/pro/assets/css/theme-builder.css`
   - Added ~1,050 lines of widget styles
   - Responsive breakpoints
   - Animation keyframes
   - Hover effects

3. `/pro/assets/js/theme-builder.js`
   - Added ~420 lines of JavaScript
   - Widget initialization functions
   - Event handlers
   - Timer functions

4. `/pro/theme-builder/class-theme-builder.php`
   - Added Lottie Web library enqueue
   - Updated script dependencies

---

## ✨ Feature Highlights

### Advanced Capabilities

#### Repeater Fields
6 widgets use repeater fields for unlimited dynamic content:
- Price List
- Price Table
- Testimonials
- Media Carousel
- Video Playlist
- Progress Tracker

#### Multiple Layouts
Flexible layout options across widgets:
- Price List: 2 layouts
- Price Table: 1-4 columns
- Testimonial Carousel: 3 styles
- Progress Tracker: 2 layouts
- Video Playlist: 3 positions

#### Animation & Effects
Rich visual effects:
- Animated Headline: 7 animation types
- Flip Box: 5 flip effects
- Share Buttons: 4 hover effects
- Countdown: Expire actions
- Carousels: Smooth transitions

#### Media Support
Comprehensive media handling:
- Media Carousel: Images + YouTube/Vimeo
- Video Playlist: YouTube/Vimeo embeds
- Lottie: JSON animations
- Blockquote: Author images
- Testimonials: Reviewer photos

---

## 🎯 Comparison with Competitors

### vs. Elementor
**Nexus Now Offers:**
- ✅ Animated Headline (Elementor Pro)
- ✅ Price List (Elementor Pro)
- ✅ Price Table (Elementor Pro)
- ✅ Flip Box (Elementor Pro)
- ✅ Media Carousel (Elementor Pro)
- ✅ Countdown (Elementor Pro)
- ✅ Testimonial Carousel (Elementor Pro)
- ✅ Share Buttons (Elementor Pro)
- ✅ Video Playlist (Elementor Pro)
- ✅ Lottie Animations (Elementor Pro)
- ✅ Progress Tracker (Elementor Pro)

### Unique Advantages
1. **Integrated Licensing:** No additional plugin needed
2. **Performance:** Lightweight, no bloat
3. **Customization:** Full control over all settings
4. **White-Label:** Agency tier includes rebranding
5. **Self-Hosted:** No external dependencies (except Lottie CDN)

---

## 📊 Installation & Update

### Automatic Update (Recommended)

1. Navigate to **Dashboard** → **Updates**
2. Find **Nexus Theme v3.2.1**
3. Click **Update Now**
4. Wait for update to complete

### Manual Update

1. Download `nexus-v3.2.1.zip`
2. Go to **Appearance** → **Themes**
3. Click **Add New** → **Upload Theme**
4. Select the downloaded zip file
5. Click **Install Now** → **Replace current with uploaded**

### First-Time Installation

1. Upload `nexus-v3.2.1.zip` via WordPress admin
2. Activate the theme
3. Enter your license key (Pro/Advanced/Agency)
4. Navigate to **Appearance** → **Theme Builder**
5. Create your first builder template
6. Drag and drop widgets to build your page

---

## ⚙️ System Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 8.0 or higher
- **MySQL:** 5.7 or higher
- **Recommended:** PHP 8.1+, MySQL 8.0+

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

### JavaScript Features Used
- IntersectionObserver (Lottie viewport trigger)
- CSS Grid (Price Table)
- CSS 3D Transforms (Flip Box)
- Modern ES6+ syntax

---

## 🎯 License Tier Features

### Free Tier
- All core theme features
- Basic templates
- Standard support

### Pro Tier ($199/year)
- Advanced controls
- API documentation
- Circuit simulator
- Performance analytics
- **8 Theme Builder widgets**

### Advanced Tier ($299/year)
- **All 20 Theme Builder widgets** ✓
- Popup Builder
- A/B testing
- Cloud storage integration
- Priority support

### Agency Tier ($599/year)
- All Advanced features
- White-label capabilities
- Unlimited sites
- Premium support
- Custom development

---

## 🔄 Upgrade Path

### From v3.2.0

This is a **feature release** with significant additions:

**What's New:**
- 12 new Theme Builder widgets
- ~5,545 lines of new code
- Enhanced JavaScript functionality
- Lottie animation support

**Compatibility:**
- ✅ Fully backward compatible
- ✅ No database changes required
- ✅ No settings migration needed
- ✅ Existing widgets unaffected

### From v3.1.x or Earlier

All features from v3.2.0 + new widgets:
- Popup Builder fixes (v3.2.0)
- Analytics dashboard (v3.2.0)
- Template library (v3.2.0)
- **12 new widgets (v3.2.1)**

---

## 🧪 Testing Recommendations

### Visual Testing
1. Test all 12 new widgets in Theme Builder
2. Verify responsive behavior on mobile
3. Check color customization options
4. Test typography controls

### Functional Testing
1. **Animated Headline:** All 7 animation types
2. **Price List/Table:** Repeater functionality
3. **Flip Box:** All 5 flip effects
4. **Media Carousel:** Image/video mix, thumbnails
5. **Countdown:** Fixed date and evergreen modes
6. **Testimonial Carousel:** Multi-slide display
7. **Share Buttons:** Sharing URLs for all networks
8. **Video Playlist:** Video switching
9. **Progress Tracker:** Connector lines
10. **Lottie:** All 4 trigger types

### Browser Testing
- Desktop: Chrome, Firefox, Safari, Edge
- Mobile: iOS Safari, Chrome Mobile
- Tablets: iPad Safari, Android Chrome

---

## 🐞 Known Considerations

1. **Lottie Library:** External CDN dependency (consider self-hosting for offline use)
2. **Google Maps:** Requires API key configuration (existing widget)
3. **Video Embeds:** YouTube/Vimeo require internet connection
4. **Share Buttons:** Some networks may have iframe restrictions
5. **CSS Grid:** Requires modern browsers (IE11+ or newer)

---

## 📝 Changelog Summary

### Added
- 12 new Theme Builder widgets (~4,075 lines)
- Lottie Web library integration (v5.12.2)
- ~1,050 lines of widget CSS styles
- ~420 lines of interactive JavaScript
- Comprehensive control system for all widgets
- Animation effects and transitions
- Repeater field support for dynamic content
- Multiple layout options per widget
- Responsive design for all widgets

### Enhanced
- Theme Builder widget library (20 total widgets)
- Frontend asset management
- JavaScript carousel functionality
- Video embed handling
- Animation system

### Technical
- Total new code: ~5,545 lines
- All widgets extend Nexus_Widget_Base
- Security: Proper escaping and nonce validation
- Performance: Optimized asset loading
- Accessibility: Semantic HTML and ARIA labels

---

## 📈 Statistics

### Phase 4 Complete
- **Total Widgets:** 20/20 (100%)
- **Phase 4.1:** 8 widgets (~2,455 lines)
- **Phase 4.2:** 12 widgets (~4,075 lines)
- **Supporting Code:** ~1,470 lines (CSS + JS)
- **Grand Total:** ~8,000 lines of production code

### Widget Breakdown
- Content Widgets: 14
- Media Widgets: 4
- Form Widgets: 1
- WordPress Widgets: 1

---

## 🎉 What This Means

Nexus Theme Builder is now **feature-complete** for the Pro tier with a comprehensive widget library that rivals premium page builders like Elementor Pro and Beaver Builder Pro.

**Key Achievements:**
- 20 professional widgets ready for production
- Comprehensive customization options
- Responsive design across all widgets
- Interactive JavaScript features
- Performance-optimized implementation
- Security-hardened code
- Accessible markup

---

## 🚀 Next Steps

### For Users
1. Update to v3.2.1
2. Explore new widgets in Theme Builder
3. Create stunning pages with new options
4. Provide feedback for future improvements

### Future Enhancements
1. Widget presets/templates
2. Widget export/import functionality
3. Display conditions (show/hide rules)
4. A/B testing for widgets
5. More animation types
6. Additional social networks
7. Share counter API integration
8. Self-hosted Lottie library option

---

## 🙏 Acknowledgments

Thank you to all Pro, Advanced, and Agency tier users for your continued support. Your feedback drives our development roadmap.

---

## 📞 Support

- **Documentation:** [https://jdsandigitel.com/nexus/docs](https://jdsandigitel.com/nexus/docs)
- **Support Forum:** [https://jdsandigitel.com/support](https://jdsandigitel.com/support)
- **Email:** support@jdsandigitel.com
- **License Server:** [https://jdsandigitel.com/license-server](https://jdsandigitel.com/license-server)

---

## 🔐 License Information

This theme is licensed under the GNU General Public License v2 or later.

**Premium Features Licensing:**
- Advanced features require active license key
- Automatic updates require license validation
- Annual renewal for continued access
- Multi-site licensing available (Agency tier)

---

**Full Changelog:** [View on GitHub](https://github.com/jdram82/nexus/releases/tag/v3.2.1)

---

*Nexus Theme - Professional WordPress Development Platform*  
*© 2026 Jdsan Digitel. All rights reserved.*
