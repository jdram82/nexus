# UL/NEC AutoCAD Plugin - Future Development Plan

**Planning Horizon:** March 2026 - December 2028 (33 months)  
**Document Version:** 1.0  
**Last Updated:** February 23, 2026  
**Next Review:** March 31, 2026

---

## 🎯 Strategic Vision

Transform the UL/NEC AutoCAD Plugin from a **validation tool** into a **comprehensive electrical design platform** with AI-powered features, international standards support, and multi-CAD compatibility.

### **3-Year Goals**
- **Users:** 400+ paying customers by end of 2026, 2,000+ by end of 2028
- **Revenue:** $1.5M ARR by end of 2028
- **Market Position:** #1 UL508A compliance tool for AutoCAD/BricsCAD
- **Geographic Expansion:** North America → Europe/Asia (IEC support)
- **Platform Expansion:** AutoCAD → BricsCAD → EPLAN → Revit MEP

---

## 📅 Development Timeline Overview

```
2026                                    2027                                    2028
│                                       │                                       │
Q2          Q3          Q4              Q1          Q2          Q3          Q4  │ Q1-Q4
│           │           │               │           │           │           │   │
Priority 0  Phase 1     Phase 3         Phase 4     Phase 5     Phase 6     V2.0│ Enterprise
(Legal +    (Component  (AI +           (IEC +      (Platform   (Cloud +    │   │ Features
Calc        Library +   CAD             Standards)  Expansion)  Mobile)     │   │ Rollout
Transparency)Advanced   Expansion)                                          │   │
            Validation)                                                     │   │
                                                                            │   │
Mar         Jun         Oct             Jan         Apr         Jul         Oct │ 2028
2026        2026        2026            2027        2027        2027        2027│
```

---

## 🔴 Priority 0: Critical Pre-GA Enhancements

**Timeline:** March 1-31, 2026 (4 weeks)  
**Status:** In Development  
**Target Release:** v1.0.0 GA (April 1, 2026)

### Week 1: Legal Protection (Mar 1-7)

#### ✅ Task 1: Legal Disclaimers & Liability Protection
**Objective:** Protect from liability, meet professional engineering standards

**Deliverables:**
- [ ] Create `ReportDisclaimers.cs` class with standard engineering software disclaimer text
- [ ] Add NEC 2023 and UL508A 5th Edition watermarks to all report pages
- [ ] Add "PE Review Required" footer to all reports
- [ ] Add liability limitation section to PDF/HTML reports
- [ ] Add assumption documentation section (ambient temp, power factor, etc.)
- [ ] Update PDF generator to include disclaimers on page 1
- [ ] Update HTML generator to show disclaimers in header
- [ ] Git commit: "Add legal disclaimers and liability protection"

**Acceptance Criteria:**
- Every generated report shows disclaimer prominently
- Report states "Requires Professional Engineer review and seal"
- User acknowledges disclaimer on first ULCHECK run
- Saved to configuration (don't show again this session)

**Risk Mitigation:** Prevents lawsuits from rejected field evaluations or installation failures

---

#### ✅ Task 2: Calculation Transparency
**Objective:** Enable PE verification, build inspector confidence

**Deliverables:**
- [ ] Add "Calculation Methodology" section to ULREPORT
- [ ] Show step-by-step formulas for every violation
- [ ] Document all assumptions used (K-factor, power factor, diversity)
- [ ] Reference NEC tables by page number (e.g., "Table 310.16, Page 70")
- [ ] Add "Verification Worksheet" export (Excel format for PE review)
- [ ] Show intermediate calculation steps (not just final result)

**Example Output:**
```
Voltage Drop Calculation - Circuit 3
───────────────────────────────────────────────────────
Formula: Vd = 2 × K × I × L / CM   (NEC Chapter 9, Table 8)

Given:
  K-factor (Copper, 75°C):  12.9 ohms per mil-foot
  Load Current (I):         20 A
  Circuit Length (L):       150 ft
  Conductor Area (CM):      10,380 CM  (12 AWG, Table 8)

Calculation:
  Vd = 2 × 12.9 × 20 × 150 / 10,380
  Vd = 77,400 / 10,380
  Vd = 7.46 volts

Percent Drop:
  %Vd = (7.46 / 480) × 100%
  %Vd = 1.55%

Result: ✅ PASS (1.55% < 3% limit per NEC 210.19(A))
```

**Acceptance Criteria:**
- PE can reproduce calculations by hand using report values
- Every numerical result shows its formula and inputs
- References cite specific NEC table/page numbers

---

### Week 2: Temperature & Derating (Mar 8-14)

#### ✅ Task 3: Ambient Temperature Correction (NEC 310.15)
**Objective:** Prevent fire hazards from inadequate derating

**Deliverables:**
- [ ] Create `TemperatureCorrectionCalculator.cs`
- [ ] Implement NEC Table 310.15(B)(1) correction factors
- [ ] Add ambient temperature setting to ULSETTINGS dialog
- [ ] Apply correction to all wire ampacity calculations
- [ ] Flag violations when corrected ampacity < load current
- [ ] Add "Temperature Derating Applied" section to violation details

**Correction Factor Table (NEC 310.15(B)(1)):**
| Ambient Temp (°C) | Correction Factor (75°C wire) |
|-------------------|------------------------------|
| 30°C or less | 1.00 (no correction) |
| 31-35°C | 0.94 |
| 36-40°C | 0.88 |
| 41-45°C | 0.82 |
| 46-50°C | 0.75 |
| 51-55°C | 0.67 |
| 56-60°C | 0.58 |

**Implementation:**
```csharp
double correctedAmpacity = baseAmpacity × temperatureFactor × conduitFillFactor;
if (correctedAmpacity < loadCurrent) {
    Violation("Wire undersized after temperature derating");
}
```

**Acceptance Criteria:**
- Default ambient temp = 30°C (no correction)
- User can set custom ambient temp in ULSETTINGS
- All wire sizing violations show corrected ampacity
- Violation message: "Corrected ampacity (after 40°C derating) = 28.2A < 30A load"

---

#### ✅ Task 4: Conduit Fill Derating (NEC 310.15(C)(1))
**Objective:** Account for heat buildup with multiple conductors

**Deliverables:**
- [ ] Create `ConduitFillDeratingCalculator.cs`
- [ ] Implement NEC Table 310.15(C)(1) adjustment factors
- [ ] Auto-detect conductor count per conduit (from drawing)
- [ ] Apply derating to wires in same conduit
- [ ] Add "Bundling Derating Applied" to violation details

**Adjustment Factor Table (NEC 310.15(C)(1)):**
| Number of Conductors | Adjustment Factor |
|---------------------|------------------|
| 1-3 | 1.00 (no derating) |
| 4-6 | 0.80 |
| 7-9 | 0.70 |
| 10-20 | 0.50 |
| 21-30 | 0.45 |
| 31-40 | 0.40 |
| 41+ | 0.35 |

**Acceptance Criteria:**
- Conduit detection finds all wires in same run
- Conductor count excludes neutral/ground (per NEC 310.15(C)(1) Note)
- Combined correction: `correctedAmpacity = base × tempFactor × conduitFactor`
- Violation shows: "7 conductors in conduit → 0.70 adjustment factor"

---

### Week 3: Enhanced Validation Rules (Mar 15-21)

#### ✅ Task 5: Altitude Correction (NEC 310.15)
**Deliverables:**
- [ ] Add altitude setting to ULSETTINGS (default: 0 feet / sea level)
- [ ] Apply NEC 110.16 correction: 3% derating per 1,000 feet above 6,000 feet
- [ ] Show altitude correction in calculation transparency section

**Example:**
- Altitude = 8,000 feet
- Correction = (8,000 - 6,000) / 1,000 × 0.03 = 0.06 (6% derating)
- Ampacity factor = 1.00 - 0.06 = 0.94

---

#### ✅ Task 6: Continuous Load (125% Rule) - NEC 210.19(A)
**Objective:** Enforce 125% sizing for loads operating ≥3 hours

**Deliverables:**
- [ ] Add "Continuous Load?" checkbox to component attributes
- [ ] Auto-detect continuous loads (motors, HVAC, lighting)
- [ ] Apply 125% multiplier to load current before wire sizing check
- [ ] Violation message: "Continuous load requires 125% wire sizing (20A × 1.25 = 25A)"

---

### Week 4: Testing & Documentation (Mar 22-31)

#### ✅ Task 7: Beta Tester Validation
- [ ] Send Priority 0 update to 50+ beta testers
- [ ] Request field evaluation testing (submit to inspector)
- [ ] Collect feedback on calculation transparency
- [ ] Fix high-priority bugs reported

#### ✅ Task 8: Documentation Updates
- [ ] Update Quick Start Guide with new features
- [ ] Create "Calculation Methodology Guide" (PDF)
- [ ] Record video tutorial: "Understanding your compliance report"
- [ ] Update ULREPORT sample output in docs

---

**Priority 0 Milestone:** March 31, 2026  
**Release:** v1.0.0 GA (General Availability)

---

## 📦 Phase 1: Component Library & Advanced Validation

**Timeline:** April - June 2026 (12 weeks)  
**Target Release:** v1.1.0 (June 30, 2026)

### Month 1: Component Database Expansion (Apr 1-30)

#### Milestone 1.1: Manufacturer Additions (50+ total)
**Goal:** Expand from 11 to 50 manufacturers

**New Manufacturers (39 added):**

**Contactors & Starters:**
- Siemens SIRIUS 3RT/3RH series
- Fuji Electric SC-N series
- LS Electric MC/GMC series
- Lovato Electric BF/BFC series

**Variable Frequency Drives:**
- Yaskawa A1000 / GA800
- Danfoss VLT AutomationDrive FC 300
- Control Techniques Commander C200
- Lenze i550 / 8400

**Circuit Protection:**
- MERSEN A50/A60QS fuses
- Littelfuse POWR-GARD / KLDR
- Bussmann FWP/FWX series (expanded)
- Cooper Bussmann LPS-RK / KTK-R

**PLCs & Controllers:**
- Mitsubishi FX5U / iQ-R series
- Omron NX/NJ series
- B&R Automation X20 / powerlink
- Beckhoff TwinCAT PLC

**Power Supplies:**
- Phoenix Contact TRIO-PS / QUINT
- PULS ML / CP series
- TDK-Lambda DRJ / DRB
- MEAN WELL DR / MDR series

**Terminal Blocks:**
- Weidmüller SAK/WDU series
- Phoenix Contact ST / PTTB series
- WAGO 2002/2006 series (expanded)
- Entrelec EN series

**Enclosures:**
- nVent Hoffman A-Series / Concept
- Rittal AE / KS series
- Hammond Manufacturing 1418 / Eclipse
- Adalet XCE / XJSB series

**Implementation:**
- [ ] Web scraper for manufacturer catalogs (where available)
- [ ] 2 manufacturers per week × 12 weeks = 48 new
- [ ] Community contribution system (users submit parts)
- [ ] Validation against UL file database (cULus listings)

**Acceptance Criteria:**
- 50,000+ total components in database
- Each component has: MFG, PartNo, Description, UL_Listing, SCCR, Datasheet_URL
- Search by manufacturer, part number, or description
- Auto-update via quarterly catalog sync

---

#### Milestone 1.2: EPLAN Data Portal Integration
**Goal:** Import standardized component data automatically

**Deliverables:**
- [ ] EPLAN Data Portal API integration
- [ ] Auto-import P8 macros as block templates
- [ ] Map EPLAN properties to AutoCAD attributes
- [ ] Scheduled weekly sync (auto-update database)

**Benefit:** Reduces manual data entry, keeps database current with manufacturer releases

---

### Month 2: Advanced Electrical Validation (May 1-31)

#### Milestone 1.3: Short-Circuit Current (SCCR) Validation
**Goal:** Full fault current cascading analysis

**New Validators:**
- [ ] **SCCR-001:** Panel SCCR adequacy (NEC 110.9, UL508A 32.2)
- [ ] **SCCR-002:** Component SCCR chain validation
- [ ] **SCCR-003:** Let-through current for fuses/breakers
- [ ] **SCCR-004:** Series rating coordination (UL1066)
- [ ] **SCCR-005:** Available fault current at each point

**Implementation:**
```
Available Fault Current (at panel) = 65,000 AIC
↓
Main Breaker Let-Through = 22,000 AIC (per UL listing)
↓
Branch Circuit Components:
  - Contactor SCCR: 100,000 AIC ✅ (> 22,000)
  - Fuse SCCR: 200,000 AIC ✅ (> 22,000)
  - Load SCCR: 18,000 AIC ❌ VIOLATION (< 22,000)

Result: FAIL - Load SCCR inadequate
Fix: Use component with ≥22,000 AIC SCCR rating
```

**Acceptance Criteria:**
- Validates entire SCCR chain from service entrance to end device
- Uses actual let-through current (not available fault current)
- References UL listing data for series ratings
- Shows weakest link in chain

---

#### Milestone 1.4: Motor Control Validation (NEC 430)
**Goal:** Full motor circuit compliance

**New Validators:**
- [ ] **MOTOR-001:** Wire sizing 125% of FLC (NEC 430.22)
- [ ] **MOTOR-002:** Overload protection 115-125% (NEC 430.32)
- [ ] **MOTOR-003:** Branch circuit protection sizing (NEC 430.52)
- [ ] **MOTOR-004:** Disconnect rating adequacy (NEC 430.109)
- [ ] **MOTOR-005:** Starter coordination with overload

**Example Violation:**
```
MOTOR-002: Overload Protection Oversized
Location: Motor M1 (10 HP, 3-phase, 480V)
FLC: 14A (per NEC Table 430.250)
Overload Range: 16.1A - 17.5A (115-125% of FLC)
Installed Overload: 20A ❌
Fix: Replace with 18A overload relay
NEC Reference: 430.32(A)(1)
```

---

#### Milestone 1.5: Thermal Management Validation
**Goal:** Prevent enclosure overheating

**New Validators:**
- [ ] **THERMAL-001:** Enclosure heat dissipation adequacy
- [ ] **THERMAL-002:** Component derating for high ambient
- [ ] **THERMAL-003:** Forced cooling (fan CFM) verification
- [ ] **THERMAL-004:** Heat sink sizing for drives/power supplies

**Calculation:**
```
Total Heat Dissipation = Σ(Component Watts)
  - Drive losses: 200W (5% of 4kW motor)
  - Transformer losses: 50W (2% of 2.5kVA)
  - Breaker/contactor: 30W
  - Total: 280W

Enclosure Natural Convection Capacity:
  Surface area: 12 sq.ft × 10 W/sq.ft/°F rise
  = 120W capacity for 10°F rise
  
Result: 280W > 120W → Forced cooling required
Recommendation: Install 100 CFM fan or increase enclosure size
```

---

### Month 3: Custom Rule Builder (Jun 1-30)

#### Milestone 1.6: Visual Rule Designer
**Goal:** Enable users to create company-specific rules without coding

**Features:**
- [ ] Drag-and-drop rule builder UI
- [ ] Condition builder: IF [entity type] [operator] [value] THEN [action]
- [ ] Operators: =, ≠, <, >, ∈ (in set), matches (regex)
- [ ] Actions: Error, Warning, Info, Auto-fix, Highlight
- [ ] Rule library export/import (JSON)

**Example Custom Rule:**
```
Rule: "Company Wire Color Standard"
Conditions:
  IF entity.type = "wire"
  AND entity.voltage >= 480
  AND entity.layer NOT IN ["RED_WIRE", "ORANGE_WIRE"]
THEN
  Violation("High voltage wires must be red or orange per company standard")
  Severity: Warning
```

**Use Cases:**
- Company-specific wire color codes
- Custom labeling requirements (must include voltage rating)
- Preferred manufacturer restrictions (Siemens only for drives)
- State/local code amendments (California Title 24)

---

**Phase 1 Milestone:** June 30, 2026  
**Release:** v1.1.0 (Component Library & Advanced Validation)

---

## 🤖 Phase 2: AI Integration (Stage 1)

**Timeline:** July - September 2026 (12 weeks)  
**Target Release:** v1.2.0 (September 30, 2026)

### Overview: 3-Stage AI Rollout
**Provider:** OpenAI GPT-4o  
**Pricing Model:** AI PRO subscription ($49/month add-on)

### Month 1: ULEXPLAIN Command (Jul 1-31)

#### Milestone 2.1: Natural Language Explanations
**Goal:** Users understand violations in plain English

**Implementation:**
- [ ] Integrate OpenAI API (GPT-4o via REST)
- [ ] Create `AIExplainCommand.cs`
- [ ] Prompt engineering for violation context
- [ ] Token optimization (minimize cost)
- [ ] Cache common explanations locally

**Usage:**
```
Command: ULEXPLAIN
(Select violation in Results Palette or click entity)

GPT Response:
"This violation occurs because your 14 AWG wire is carrying 20 amps, which 
exceeds its 15-amp rating per NEC Table 310.16. Wire insulation can overheat 
and cause a fire if overloaded.

The NEC requires wires to be sized for at least 125% of continuous loads (loads 
running 3+ hours). Since your circuit breaker is rated 20A, the wire should be 
12 AWG (20A capacity) minimum.

To fix: Replace the 14 AWG wire with 12 AWG, or reduce the circuit breaker to 
15A if the load is actually <15A."
```

**API Call Structure:**
```json
{
  "model": "gpt-4o-mini",
  "messages": [
    {"role": "system", "content": "You are an electrical engineer explaining NEC violations."},
    {"role": "user", "content": "Explain: Wire undersized. 14 AWG carrying 20A. NEC 310.16 requires 12 AWG min."}
  ],
  "max_tokens": 300,
  "temperature": 0.3
}
```

**Cost Optimization:**
- Cache responses for common violations (95% hit rate expected)
- Use GPT-4o-mini ($0.15 / 1M tokens) instead of GPT-4 ($5 / 1M)
- Average cost per explanation: $0.002
- User limit: 100 explanations/month (part of AI PRO)

---

### Month 2: Code Reference Lookup (Aug 1-31)

#### Milestone 2.2: ULCODESEARCH Command
**Goal:** Instant NEC/UL508A article lookup

**Implementation:**
- [ ] Embed NEC 2023 text (license from NFPA required)
- [ ] Embed UL508A 5th Edition text
- [ ] Vector search using OpenAI Embeddings API
- [ ] GPT-4o summarizes relevant section

**Usage:**
```
Command: ULCODESEARCH "motor overload protection sizing"

Results:
┌─────────────────────────────────────────────────────────┐
│ NEC 430.32 - Overload Protection                      │
│                                                         │
│ (A) General. Each continuous-duty motor rated more    │
│ than 1 horsepower shall be protected against overload │
│ by one of the means in 430.32(A)(1), (B), and (C).   │
│                                                         │
│ (A)(1) Separate Overload Device. A separate overload  │
│ device that is responsive to motor current shall be   │
│ selected to trip or shall be rated at not more than   │
│ the following percentage of the motor nameplate       │
│ full-load current rating:                              │
│   - Motors with service factor ≥1.15: 125%           │
│   - Motors with temp rise ≤40°C: 125%                │
│   - All other motors: 115%                            │
│                                                         │
│ GPT Summary:                                           │
│ Motor overload protection must trip between 115-125%  │
│ of the motor's full-load current (FLC) depending on   │
│ the motor's service factor or temperature rating.     │
│ Use the motor nameplate FLC (not calculated FLC).     │
└─────────────────────────────────────────────────────────┘
```

**Pricing:**
- Embeddings: $0.10 / 1M tokens (one-time for code database)
- Search query: $0.02 / 1M tokens (minimal cost)
- GPT-4o summary: $0.15 / 1M tokens
- Average cost per search: $0.003

---

### Month 3: ULRECOMMEND - Intelligent Suggestions (Sep 1-30)

#### Milestone 2.3: Auto-Fix Suggestions
**Goal:** AI suggests specific components/actions to fix violations

**Implementation:**
- [ ] GPT-4o analyzes violation context
- [ ] Queries component database for suitable replacements
- [ ] Ranks recommendations by cost/availability
- [ ] Shows compatibility warnings

**Usage:**
```
Command: ULRECOMMEND
(Select "Wire Undersized" violation)

AI Recommendations:
┌─────────────────────────────────────────────────────────┐
│ 🔧 Recommended Fixes (3 options)                       │
│                                                         │
│ Option 1: Increase Wire Size (Preferred) ⭐            │
│   Current:  14 AWG THHN (on layer "14AWG")           │
│   Replace:  12 AWG THHN                               │
│   Ampacity: 25A (sufficient for 20A load)             │
│   Cost:     +$0.42/ft wire cost                       │
│   Est Time: 30 min to re-pull wire                    │
│   Action:   Change layer name to "12AWG"              │
│                                                         │
│ Option 2: Reduce Circuit Breaker                      │
│   Current:  20A breaker                               │
│   Replace:  15A breaker                               │
│   Ampacity: 15A matches 14 AWG wire                   │
│   Cost:     -$8 (smaller breaker cheaper)             │
│   ⚠️ Warning: Only if load is actually <15A           │
│   Action:   Verify load current first                 │
│                                                         │
│ Option 3: Install Derating-Compliant Wire             │
│   Current:  14 AWG at 40°C ambient                    │
│   Replace:  12 AWG with 90°C insulation (THHN)       │
│   Ampacity: 30A × 0.88 derating = 26.4A              │
│   Cost:     +$0.58/ft wire cost                       │
│   Benefit:  Future-proof for temp variations          │
│                                                         │
│ Click option to apply auto-fix (modifies drawing)     │
└─────────────────────────────────────────────────────────┘
```

**Auto-Fix Capability:**
- Option 1: Changes wire layer name in drawing
- Option 2: Suggests manual component swap (can't auto-change breaker block)
- Option 3: Offers upgrade path with cost-benefit analysis

---

**Phase 2 Milestone:** September 30, 2026  
**Release:** v1.2.0 (AI Integration Stage 1 - AI PRO tier)

---

## 🌍 Phase 3: CAD Platform Expansion

**Timeline:** October - December 2026 (12 weeks)  
**Target Release:** v1.3.0 (December 31, 2026)

### Month 1: BricsCAD Support (Oct 1-31)

#### Milestone 3.1: BricsCAD V24 Compatibility
**Goal:** Expand beyond AutoCAD to capture 15% market share using BricsCAD

**Implementation:**
- [ ] Test on BricsCAD Mechanical V24
- [ ] Test on BricsCAD Pro V24
- [ ] Adapt AutoCAD API calls to BricsCAD equivalents
- [ ] Fix toolbar/ribbon differences
- [ ] Test on Linux (BricsCAD supports Ubuntu)

**Challenges:**
- BricsCAD .NET API close to AutoCAD but not 100% compatible
- Command registration differs (LISP vs .NET)
- Ribbon/menu structure different

**Testing Matrix:**
| Platform | Version | OS | Status |
|----------|---------|-----|--------|
| BricsCAD Pro | V24 | Windows 10 | Testing |
| BricsCAD Mechanical | V24 | Windows 11 | Testing |
| BricsCAD Pro | V24 | Ubuntu 22.04 | Future |

---

### Month 2: EPLAN Electric P8 Integration (Nov 1-30)

#### Milestone 3.2: EPLAN Data Exchange
**Goal:** Import EPLAN schematics, export validation results back

**Implementation:**
- [ ] EPLAN API integration (COM interface)
- [ ] Import P8 project → AutoCAD DWG conversion
- [ ] Export validation results → EPLAN properties
- [ ] Bidirectional sync (changes in EPLAN update validation)

**Workflow:**
```
EPLAN P8 Project
    ↓ Export
AutoCAD DWG (via EPLAN DWG export)
    ↓ ULCHECK
Validation Results
    ↓ Import back
EPLAN Properties (compliance status per component)
```

**Use Case:** Large EPC firms use EPLAN for engineering, AutoCAD for layouts

---

### Month 3: Revit MEP Beta (Dec 1-31)

#### Milestone 3.3: Revit MEP Electrical Validation
**Goal:** Enter BIM market (30% of firms moving to Revit)

**Implementation:**
- [ ] Revit API integration (.NET)
- [ ] Extract electrical families as "components"
- [ ] Map Revit parameters → UL/NEC validation context
- [ ] Display violations in Revit warnings dialog
- [ ] Generate PDF report from Revit model

**Challenges:**
- Revit electrical families very different from AutoCAD blocks
- 3D model vs 2D drawing (need to flatten for validation)
- Revit users expect BIM360 cloud integration (future Phase 6)

**Beta Release:** v1.3.0-revit-beta (December 31, 2026)

---

**Phase 3 Milestone:** December 31, 2026  
**Release:** v1.3.0 (Multi-CAD Support - BricsCAD, EPLAN, Revit beta)

---

## 🌐 Phase 4: International Standards (IEC)

**Timeline:** January - March 2027 (12 weeks)  
**Target Release:** v2.0.0 (March 31, 2027)

### IEC 61439 Low-Voltage Switchgear Validation

#### Milestone 4.1: IEC 61439-1 & 61439-2 Implementation
**Goal:** Enable European/Asian market expansion (2x addressable market)

**New Rules Added (32 total):**

**IEC 61439-1 (General Rules):**
- Clearances and creepage distances (Annex B)
- Temperature rise limits (Clause 10.10)
- Short-circuit withstand (Clause 10.11)
- Degree of protection (IP rating validation)
- Mechanical robustness

**IEC 61439-2 (Power Switchgear):**
- Rated operational voltage and current
- Design verification requirements
- Busbar sizing and rating
- Cable termination clearances
- Ventilation and cooling

**Implementation:**
- [ ] Add IEC standards to rules database (JSON format)
- [ ] Create `IECValidationContext.cs`
- [ ] Implement metric units (mm, kW) alongside imperial
- [ ] Add IEC-compliant report template
- [ ] Multi-language support (English, German, French, Spanish, Chinese)

**Market Impact:**
- Opens European market (Germany, France, UK, Italy)
- Opens Asian market (China, Japan, South Korea, India)
- Estimated 2,000+ potential customers (vs 1,500 in US/Canada)

---

#### Milestone 4.2: IEC 60364 Electrical Installations
**Goal:** Complement IEC 61439 with building electrical validation

**New Rules (15 total):**
- Cable sizing per IEC 60364-5-52
- Protection coordination per IEC 60364-5-53
- Earthing systems (TN, TT, IT) per IEC 60364-5-54
- RCD/RCBO requirements per IEC 60364-4-41

---

**Phase 4 Milestone:** March 31, 2027  
**Release:** v2.0.0 (International Edition - NEC + IEC support)

---

## 📱 Phase 5: Platform Expansion & Productivity

**Timeline:** April - June 2027 (12 weeks)  
**Target Release:** v2.1.0 (June 30, 2027)

### Milestone 5.1: Intelligent Wire Routing (Auto-Drawing)
**Goal:** Auto-generate wire paths, reduce manual drafting 80%

**Features:**
- Shortest-path algorithm with obstacle avoidance
- Wire class separation (power 12", control 6", signal 3" spacing)
- Automatic annotation (wire numbers, destinations, gauge)
- Terminal block assignment logic
- Multi-conductor cable bundling

---

### Milestone 5.2: BOM with Real-Time Pricing
**Goal:** Competitive quoting, cost optimization

**Features:**
- DigiKey API integration (live pricing)
- Mouser API integration (availability)
- Multi-distributor cost comparison
- Availability alerts (in stock / lead time / obsolete)
- Historical price tracking (trend analysis)

**Example BOM Output:**
| Item | Part No | Qty | DigiKey | Mouser | Lowest | Avail |
|------|---------|-----|---------|--------|--------|-------|
| Contactor | 3RT2026 | 3 | $48.20 | $46.85 ✅ | Mouser | In Stock |
| Breaker | FAZ-C20/2 | 5 | $12.40 | $13.10 | DigiKey ✅ | Ships Today |

---

### Milestone 5.3: Customizable Report Templates
**Goal:** Company branding, PE-specific formats

**Features:**
- Drag-drop report section builder
- Company logo upload
- Custom header/footer text
- Color scheme picker (match company brand)
- Save templates as profiles

---

**Phase 5 Milestone:** June 30, 2027  
**Release:** v2.1.0 (Productivity Tools - Auto-Routing + BOM Pricing)

---

## ☁️ Phase 6: Cloud & Mobile

**Timeline:** July - December 2027 (24 weeks)  
**Target Release:** v2.2.0 (December 31, 2027)

### Milestone 6.1: Cloud Sync & Collaboration
**Goal:** Multi-user teams, real-time collaboration

**Features:**
- Cloud storage of validation results
- Team dashboards (project status overview)
- Real-time notifications (violations fixed)
- Version control integration (Git/SVN)
- Report library (past projects searchable)

**Pricing:** Cloud PRO tier ($99/month per team)

---

### Milestone 6.2: Mobile Companion App (iOS/Android)
**Goal:** Field validation review on jobsite

**Features:**
- View PDF reports on phone/tablet
- Filter violations by severity/location
- Take photos of violations (attach to report)
- Approval workflow (inspector sign-off)
- Email/share reports from app

**Use Case:** Inspector in field marks violations, sends back to office for CAD fixes

---

**Phase 6 Milestone:** December 31, 2027  
**Release:** v2.2.0 (Cloud + Mobile Platform)

---

## 🏢 Year 3: Enterprise Features (2028)

### Q1 2028: API & Integrations
- REST API for ERP integration (SAP, Oracle, Microsoft Dynamics)
- Webhook notifications (validation completed → trigger PLM workflow)
- Custom validators via API (company-specific rules)
- White-label option (rebrand plugin as your own)

### Q2 2028: Advanced AI (Stage 3)
- ULAUTOFIX: AI modifies drawing automatically (not just suggestions)
- ULOPTIMIZE: AI suggests cost reductions (component substitutions)
- ULGENERATETEST: AI creates test procedures from schematic

### Q3 2028: Compliance Management Platform
- Multi-project dashboard (portfolio view)
- Compliance score tracking over time
- Audit trail (who validated, when, changes made)
- Inspector portal (submit drawings for review)

### Q4 2028: Machine Learning Enhancements
- Anomaly detection (unusual patterns in drawings)
- Predictive violations (flag potential issues before ULCHECK runs)
- Learning from corrections (improve suggestions based on user fixes)
- Component failure prediction (MTBF analysis)

---

## 📊 Resource Requirements

### Development Team
- **Current:** 1 developer (you)
- **2026 Q3:** +1 junior developer (BricsCAD/EPLAN ports)
- **2027 Q1:** +1 AI/ML engineer (GPT integration optimization)
- **2027 Q3:** +1 mobile developer (iOS/Android apps)
- **2028:** +2 developers (enterprise features)

### Infrastructure Costs
| Component | 2026 | 2027 | 2028 |
|-----------|------|------|------|
| OpenAI API | $500/mo | $1,500/mo | $5,000/mo |
| Cloud hosting | $0 | $200/mo | $1,000/mo |
| CDN | $0 | $50/mo | $300/mo |
| Email/support | $50/mo | $150/mo | $500/mo |
| **Total** | **$550/mo** | **$1,900/mo** | **$6,800/mo** |

---

## 💰 Pricing Evolution

### 2026 (Current Beta)
- Free trial: 30 days
- Professional: $75/month early adopter (locked in)
- Team: $280/month (5 users)
- Enterprise: Custom ($12K+/year)

### 2027 (After IEC Launch)
- Professional: $149/month (new customers)
- Professional + AI PRO: $198/month ($149 + $49 AI)
- Team: $399/month (5 users)
- Cloud PRO: $99/month add-on (team collaboration)
- Enterprise: $15K-50K/year

### 2028 (Enterprise Platform)
- Professional: $179/month
- Professional + AI PRO: $228/month
- Team: $479/month
- Cloud PRO + Mobile: $149/month
- Enterprise: $25K-100K/year (API, white-label)

**Early Adopter Protection:** Users who sign up in 2026 keep their $75/month rate forever.

---

## 🎯 Success Metrics

### 2026 Goals
- **Users:** 400 paying customers by Dec 31
- **Revenue:** $360K ARR ($30K MRR × 12)
- **Churn:** <5% monthly
- **NPS:** >70

### 2027 Goals
- **Users:** 1,200 paying customers
- **Revenue:** $1.2M ARR ($100K MRR)
- **International:** 30% of revenue from Europe/Asia (IEC support)
- **AI Adoption:** 40% of Pro users upgrade to AI PRO

### 2028 Goals
- **Users:** 2,500 paying customers
- **Revenue:** $2.5M ARR ($208K MRR)
- **Enterprise:** 10% of revenue from Enterprise tier (25 customers @ $50K avg)
- **Market Share:** #1 plugin for UL508A AutoCAD validation

---

## 📅 Release Calendar Summary

| Date | Version | Major Features |
|------|---------|----------------|
| Mar 31, 2026 | v1.0.0 GA | Legal disclaimers, temperature derating, calc transparency |
| Jun 30, 2026 | v1.1.0 | 50 manufacturers, advanced validation, custom rules |
| Sep 30, 2026 | v1.2.0 | AI Stage 1 (ULEXPLAIN, ULCODESEARCH, ULRECOMMEND) |
| Dec 31, 2026 | v1.3.0 | BricsCAD, EPLAN, Revit beta |
| Mar 31, 2027 | v2.0.0 | IEC 61439/60364 international standards |
| Jun 30, 2027 | v2.1.0 | Auto-routing, BOM pricing, report templates |
| Dec 31, 2027 | v2.2.0 | Cloud sync, mobile app |
| Dec 31, 2028 | v3.0.0 | Enterprise platform, API, auto-fix AI |

---

## ⚠️ Risks & Mitigation

### Risk 1: GPT API Cost Spirals
**Mitigation:**
- Aggressive caching (95% hit rate target)
- Use GPT-4o-mini instead of GPT-4 where possible
- Token limit per user (100 requests/month)
- Offer offline mode (cached responses only)

### Risk 2: IEC Standards Licensing
**Mitigation:**
- License IEC 61439 from IEC.ch (~$2,000/year)
- Pass cost to customers (IEC edition $20/month premium)
- Offer NEC-only version at lower price

### Risk 3: BricsCAD/Revit API Changes
**Mitigation:**
- Maintain compatibility with 3 most recent versions
- Automated regression testing on each new release
- Early access programs with Bricsys/Autodesk

### Risk 4: Competitive Pressure (AutoCAD Electrical adds UL508A)
**Mitigation:**
- Focus on ease-of-use advantage (3 commands vs 40+ in ACE)
- Lock in customers with early adopter pricing ($75/mo forever)
- Build AI moat (GPT features ACE unlikely to add)
- Expand to BricsCAD/EPLAN where ACE doesn't work

---

## 📞 Feedback & Iteration

**Monthly Review Cadence:**
- Last Friday of each month: Review progress vs plan
- Adjust timeline based on beta feedback
- Reprioritize features based on customer requests
- Update this document with actual completion dates

**Next Review:** March 28, 2026

---

**Document Version:** 1.0  
**Last Updated:** February 23, 2026  
**Next Revision:** March 31, 2026 (after Priority 0 completion)
