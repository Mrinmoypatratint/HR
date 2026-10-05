---
name: IntraEats Pulse
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#5b4137'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#8f7065'
  outline-variant: '#e4beb2'
  surface-tint: '#a93800'
  primary: '#a53700'
  on-primary: '#ffffff'
  primary-container: '#ce4700'
  on-primary-container: '#fffbff'
  inverse-primary: '#ffb59b'
  secondary: '#545f73'
  on-secondary: '#ffffff'
  secondary-container: '#d5e0f8'
  on-secondary-container: '#586377'
  tertiary: '#006947'
  on-tertiary: '#ffffff'
  tertiary-container: '#00855b'
  on-tertiary-container: '#f5fff6'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbcf'
  primary-fixed-dim: '#ffb59b'
  on-primary-fixed: '#380d00'
  on-primary-fixed-variant: '#812900'
  secondary-fixed: '#d8e3fb'
  secondary-fixed-dim: '#bcc7de'
  on-secondary-fixed: '#111c2d'
  on-secondary-fixed-variant: '#3c475a'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  display:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: '800'
    lineHeight: 48px
    letterSpacing: -0.03em
  display-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '800'
    lineHeight: 38px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.015em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
    letterSpacing: -0.01em
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: 0em
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.005em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.04em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.25rem
  gutter-mobile: 0.75rem
  margin: 2rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system delivers a balance of workplace utility and culinary-logistics energy. Built for dual audiences—HR operations managers navigating high-density workforce metrics and frontline staff executing clock-ins on kiosk or mobile devices—the aesthetic combines corporate SaaS discipline with high-energy brand personality.

### Philosophy & Visual Movement
- **Corporate / Modern infused with Food-Tech Dynamism:** High clarity, structured data tables, and disciplined forms paired with optimistic, vibrant warm accents derived from the brand identity.
- **Approachability Meets Rigor:** Fast visual scanning, high contrast ratios, and reassuring feedback loops ensure attendance tracking feels seamless rather than punitive.
- **Emotional Response:** Inspires workplace reliability, speed, operational precision, and mutual transparency between frontline teams and people ops.

## Colors

The color architecture separates brand action, functional feedback, and structural surfaces to prevent cognitive overload in data-rich environments.

### Core Roles
- **Primary (`#F95700`):** The signature vibrant brand orange. Reserved for primary calls-to-action (clocking in/out, primary form submission, active navigation indicators, and branding accents).
- **Secondary (`#1E293B`):** Deep slate charcoal used for high-emphasis typography, headers, dark sidebar backgrounds, and high-contrast administrative controls.
- **Tertiary (`#10B981`):** Crisp emerald green, signifying success, verified biometric/geo check-ins, "Present" indicators, and completed shifts.
- **Neutral (`#64748B`):** Slate grey used for secondary typography, metadata, icons, and subtle structural boundaries.

### Semantic Status Matrix
- **Present / Verified:** Emerald Green (`#10B981`) on soft mint tinted base (`#ECFDF5`).
- **Late / Attention / Pending:** Amber Gold (`#F59E0B`) on warm amber base (`#FFFBEB`).
- **Absent / Breach / Error:** Crimson Red (`#EF4444`) on pale rose base (`#FEF2F2`).
- **On Leave / Remote / Scheduled:** Royal Blue (`#2563EB`) on soft ice base (`#EFF6FF`).

### Surfaces & Structural Backgrounds
- **App Canvas:** `#F8FAFC` (Slate 50) for reduced eye strain across long operational shifts.
- **Cards & Data Surfaces:** `#FFFFFF` pure crisp white with 1px border (`#E2E8F0`).
- **Hover/Selected Tiers:** `#FFF7ED` (subtle orange tint) for active employee rows and selected filters.

## Typography

**Plus Jakarta Sans** powers the entire design system. Its geometric foundation provides surgical clarity in tabular layouts, while its rounded apertures mirror the friendly warmth of the brand identity.

### Scale Application
- **Display & Large Headlines:** Used for live shift clocks, summary KPI cards (e.g., "98.4% On Time"), and hero clock-in terminal prompts.
- **Medium & Small Headlines:** Applied to page headers, drawer titles, modal confirmations, and section division within employee records.
- **Body:** Standard text across table contents, policy notes, approval workflows, and audit histories.
- **Labels:** Designed with increased weight and tracked spacing for uppercase table headers (`TH`), status badges, tab bars, and biometric status indicators.

## Layout & Spacing

The layout is built on an adaptive 8pt modular grid designed to accommodate both information-dense enterprise analytics and frictionless attendance capture terminals.

### Screen Adaptations & Grid Philosophy
- **Desktop (Admin Dashboard / HR Center):** 12-column fluid grid with fixed collapsible navigation sidebar (260px expanded, 72px icon-only). Gutters set to `1.25rem` (20px), outer layout margins set to `2rem` (32px).
- **Tablet / Terminal Kiosk:** 8-column layout with fixed action footers and simplified touch targets for group shift punching.
- **Mobile (Employee Self-Service):** 4-column layout with `1rem` (16px) margins. Critical attendance cards stretch full-width with vertical stacking.

### Component Spacing Cadence
- Use `space-xs` (4px) for micro-alignments, pill badge padding, and icon-to-label text spacing.
- Use `space-sm` (8px) for input internal padding, dropdown gaps, and avatar group overlaps.
- Use `space-md` (16px) for card body internal padding, standard form field vertical gaps, and table cell padding.
- Use `space-lg` (24px) for section separating margins and multi-column card gutters.
- Use `space-xl` (40px) between distinct workflow sections and metric grid blocks.

## Elevation & Depth

Visual hierarchy leverages a hybrid model of soft ambient shadows and low-contrast borders (`#E2E8F0`), keeping the interface crisp, lightweight, and modern.

### Layer Hierarchy
- **Level 0 (Base Canvas):** Background color `#F8FAFC`. Zero elevation.
- **Level 1 (Cards, Metric Tiles & Tables):** Surface `#FFFFFF`, 1px border in `#E2E8F0`, with soft ambient shadow: `box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02)`.
- **Level 2 (Hover States, Quick Filters & Dropdowns):** Subtle lift: `box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05)`.
- **Level 3 (Modals, Overlays & Slide-out Drawers):** Floating depth with high diffusion: `box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.05)`.
- **Accent Glow (Clock-in / High Impact CTA):** Primary button focus/active state introduces a subtle branded ambient halo: `0 0 0 4px rgba(249, 87, 0, 0.20)`.

## Shapes

The interface embraces a friendly yet modern industrial form factor with a baseline border-radius of `0.5rem` (8px) scaling up to `0.75rem` (12px) and `1rem` (16px) for focal content blocks.

### Corner Radius Usage
- **Cards & Data Tables:** `12px` to `16px` outer container curvature, creating an approachable, polished frame that balances data sharpness.
- **Buttons & Form Fields:** `8px` to `10px` for confident, easily tappable inputs.
- **Badges, Status Pills & Avatars:** Fully rounded pill shapes (`9999px`) for quick categorization at a glance.
- **Progress Bars:** Fully rounded track and indicators (`9999px`) for smooth velocity perception.

## Components

### Buttons
- **Primary:** Background `#F95700`, text `#FFFFFF`, bold weight (`label-lg`), height 44px (48px on kiosk/mobile). Hover state deepens to `#E04F00`.
- **Secondary / Outline:** Background `#FFFFFF`, 1px border `#CBD5E1`, text `#1E293B`. Hover turns surface to `#F8FAFC`.
- **Ghost / Tertiary:** No border, text `#64748B`, hover background `#F1F5F9`.
- **Destructive:** Pale red surface `#FEF2F2`, border `#FECACA`, text `#DC2626`.

### Attendance Status Badges
- Compact pill-shaped elements with `6px 12px` padding and `label-sm` font.
- **Present:** `#ECFDF5` background, `#059669` text, paired with a solid 6px `#10B981` pulsing dot.
- **Late:** `#FFFBEB` background, `#D97706` text.
- **Absent:** `#FEF2F2` background, `#DC2626` text.
- **Leave / Holiday:** `#EFF6FF` background, `#2563EB` text.

### Data Tables
- Header row rendered with `#F8FAFC` background, uppercase `label-sm` typography in `#64748B`, with explicit subtle bottom border `#E2E8F0`.
- Body rows at 56px height, pure `#FFFFFF` background, transitioning to `#FFF7ED` on hover.
- Sticky columns for Employee ID and Avatar + Name on horizontal overflow.

### Input Fields & Controls
- Border: 1px `#CBD5E1` resting, shifting to `#F95700` with 3px `#F95700`/15% halo on focus.
- Placeholder text in `#94A3B8`.
- Checkboxes & Radios: `#F95700` fill on selected state, soft square with 4px radius for checkboxes, full circle for radio buttons.

### Specialized Workforce Components
- **Clock Punch Console:** Large card featuring real-time digital clock display (`display`), geo-location indicator, and full-width high-contrast punch button.
- **Shift Progress Tracker:** Segmented bar displaying planned hours, overtime thresholds, and mandatory break intervals using semantic color segments.
- **Biometric / QR Verification State:** Centered card with scan reticle, status ring transition from brand orange (scanning) to emerald green (authenticated).