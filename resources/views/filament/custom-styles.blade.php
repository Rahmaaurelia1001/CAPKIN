<style>
/* ══════════════════════════════════════════════════════════════════
   SIMONIKA MODERN & VIBRANT DESIGN SYSTEM (2026)
   Unified Enterprise UI with Plus Jakarta Sans & Perfect Dark Mode
   ══════════════════════════════════════════════════════════════════ */

/* ── 1. GLOBAL TYPOGRAPHY (Plus Jakarta Sans) ── */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600;1,700&display=swap');

html, body, button, input, select, textarea,
.fi-page, .fi-sidebar, .fi-topbar, .fi-ta, .fi-fo, .fi-in, .fi-modal, .fi-dropdown, .fi-badge {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

/* ── 2. LIGHT CANVAS BACKGROUND (Subtle Dynamic Mesh) ── */
html:not(.dark) body {
    background-color: #f8fafc !important;
    background-image: 
        radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.10) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(147, 51, 234, 0.08) 0px, transparent 45%),
        radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.06) 0px, transparent 50%) !important;
    background-attachment: fixed !important;
    color: #0f172a !important;
}

/* ── 3. TOPBAR (Frosted Glass with Vibrant Gradient Line) ── */
html:not(.dark) .fi-topbar {
    background: rgba(255, 255, 255, 0.88) !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    border-bottom: 1px solid rgba(226, 232, 240, 0.85) !important;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04) !important;
    height: 60px !important;
    position: relative !important;
}
html:not(.dark) .fi-topbar::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2.5px;
    background: linear-gradient(90deg, #1e40af, #0284c7, #10b981, #f59e0b);
    opacity: 0.95;
}

/* Brand Logo & Sidebar Header */
.fi-logo {
    display: inline-flex !important;
    align-items: center !important;
    max-height: 42px !important;
    overflow: hidden !important;
}
.fi-simple-layout .fi-logo,
.fi-simple-main .fi-logo,
.fi-simple-header .fi-logo {
    max-height: none !important;
    height: auto !important;
    overflow: visible !important;
    display: flex !important;
    justify-content: center !important;
}
.fi-sidebar-header {
    height: 64px !important;
    min-height: 64px !important;
    max-height: 64px !important;
    padding: 0 18px !important;
    display: flex !important;
    align-items: center !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: #ffffff !important;
}
@media (min-width: 1024px) {
    .fi-topbar .fi-logo {
        display: none !important;
    }
}

/* ── 4. SIDEBAR STYLING & VIBRANT ACTIVE ITEM ── */
html:not(.dark) .fi-sidebar {
    background-color: #ffffff !important;
    border-right: 1px solid rgba(226, 232, 240, 0.85) !important;
    box-shadow: 2px 0 16px rgba(15, 23, 42, 0.03) !important;
}
html:not(.dark) .fi-sidebar-item > a,
html:not(.dark) .fi-sidebar-item > button {
    border-radius: 10px !important;
    margin: 3px 12px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
html:not(.dark) .fi-sidebar-item:hover > a,
html:not(.dark) .fi-sidebar-item:hover > button {
    background-color: #f0f7ff !important;
    color: #1e40af !important;
    transform: translateX(3px) !important;
}
html:not(.dark) .fi-sidebar-item-active > a,
html:not(.dark) .fi-sidebar-item-active > button {
    background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%) !important;
    color: #ffffff !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35) !important;
    border-left: none !important;
}
html:not(.dark) .fi-sidebar-item-active svg {
    color: #ffffff !important;
}
html:not(.dark) .fi-sidebar-group-label {
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    color: #94a3b8 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    padding: 0 16px !important;
}

/* ── 5. STATS OVERVIEW WIDGET (Transparent Wrapper + 4 Distinct Vibrant Cards) ── */
.fi-wi-stats-overview,
.fi-wi-stats-overview section.fi-section,
.fi-wi-stats-overview .fi-section,
.fi-wi-stats-overview > div,
.fi-wi-stats-overview .fi-section-content-ctn,
.fi-wi-stats-overview .fi-section-content {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    --tw-ring-color: transparent !important;
    --tw-ring-shadow: 0 0 #0000 !important;
    padding: 0 !important;
    margin: 0 !important;
}

.fi-wi-stats-overview-stat {
    border-radius: 18px !important;
    padding: 22px 24px !important;
    transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    position: relative !important;
    overflow: hidden !important;
    border: 1px solid rgba(255, 255, 255, 0.22) !important;
    cursor: default !important;
    color: #ffffff !important;
}
.fi-wi-stats-overview-stat:hover {
    transform: translateY(-5px) scale(1.015) !important;
}

/* Force semua teks dalam stat card selalu putih — override Filament color system */
.fi-wi-stats-overview-stat,
.fi-wi-stats-overview-stat *,
.fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-label,
.fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-value,
.fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-description,
.fi-wi-stats-overview-stat span,
.fi-wi-stats-overview-stat svg,
.fi-wi-stats-overview-stat [class*="fi-color-"] {
    color: #ffffff !important;
    --tw-text-opacity: 1 !important;
}

.fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-label {
    font-weight: 800 !important;
    font-size: 0.82rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    opacity: 0.9 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-value {
    font-weight: 900 !important;
    font-size: 1.85rem !important;
    letter-spacing: -0.02em !important;
    font-variant-numeric: tabular-nums !important;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
    margin-top: 8px !important;
    margin-bottom: 6px !important;
    line-height: 1.1 !important;
}

.fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-description {
    font-weight: 600 !important;
    font-size: 0.8rem !important;
    opacity: 0.88 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* ════════════════════════════════════════════════════════
   STAT CARD WARNA — Langsung di elemen .fi-wi-stats-overview-stat
   Menggunakan nth-of-type pada elemen stat itu sendiri,
   dan nth-child dari parent grid untuk jangkauan paling luas.
   ════════════════════════════════════════════════════════ */

/* Card 1 → Ocean Blue (sama dengan detail-ikk c1) */
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(1),
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(1),
.fi-wi-stats-overview-stat:nth-child(1),
.fi-wi-stats-overview-stat:first-child {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d6fbd 60%, #0284c7 100%) !important;
    box-shadow: 0 10px 28px -4px rgba(2, 132, 199, 0.5), inset 0 1px 0 rgba(255,255,255,0.15) !important;
}
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(1):hover,
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(1):hover,
.fi-wi-stats-overview-stat:nth-child(1):hover {
    box-shadow: 0 18px 36px -4px rgba(2, 132, 199, 0.65) !important;
}

/* Card 2 → Indigo Violet (sama dengan detail-ikk c2) */
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(2),
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(2),
.fi-wi-stats-overview-stat:nth-child(2) {
    background: linear-gradient(135deg, #4338ca 0%, #6366f1 60%, #8b5cf6 100%) !important;
    box-shadow: 0 10px 28px -4px rgba(139, 92, 246, 0.5), inset 0 1px 0 rgba(255,255,255,0.15) !important;
}
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(2):hover,
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(2):hover,
.fi-wi-stats-overview-stat:nth-child(2):hover {
    box-shadow: 0 18px 36px -4px rgba(139, 92, 246, 0.65) !important;
}

/* Card 3 → Amber Gold (sama dengan detail-ikk c3) */
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(3),
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(3),
.fi-wi-stats-overview-stat:nth-child(3) {
    background: linear-gradient(135deg, #92400e 0%, #d97706 60%, #f59e0b 100%) !important;
    box-shadow: 0 10px 28px -4px rgba(245, 158, 11, 0.5), inset 0 1px 0 rgba(255,255,255,0.15) !important;
}
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(3):hover,
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(3):hover,
.fi-wi-stats-overview-stat:nth-child(3):hover {
    box-shadow: 0 18px 36px -4px rgba(245, 158, 11, 0.65) !important;
}

/* Card 4 → Emerald Green (sama dengan detail-ikk c4) */
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(4),
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(4),
.fi-wi-stats-overview-stat:nth-child(4) {
    background: linear-gradient(135deg, #064e3b 0%, #059669 60%, #10b981 100%) !important;
    box-shadow: 0 10px 28px -4px rgba(16, 185, 129, 0.5), inset 0 1px 0 rgba(255,255,255,0.15) !important;
}
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-child(4):hover,
.fi-wi-stats-overview .fi-wi-stats-overview-stat:nth-of-type(4):hover,
.fi-wi-stats-overview-stat:nth-child(4):hover {
    box-shadow: 0 18px 36px -4px rgba(16, 185, 129, 0.65) !important;
}

/* ── 6. SECTION & CARD CONTAINERS ── */
html:not(.dark) .fi-section:not(.fi-wi-stats-overview .fi-section),
html:not(.dark) .fi-wi-chart,
html:not(.dark) .fi-wi-table-wrapper,
html:not(.dark) .fi-ta-ctn {
    background-color: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.9) !important;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02) !important;
    border-radius: 16px !important;
    overflow: hidden !important;
}
.fi-wi-chart header h3,
.fi-wi-table-wrapper header h3,
.fi-section-header-heading {
    font-weight: 800 !important;
    font-size: 1.02rem !important;
    letter-spacing: -0.01em !important;
    color: #0f172a !important;
}

/* ── 7. TABLE STYLING (Modern Clean Grid) ── */
html:not(.dark) .fi-ta-header {
    background: rgba(248, 250, 252, 0.8) !important;
    border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
    padding: 12px 18px !important;
}
html:not(.dark) .fi-ta-header-cell {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
    font-size: 0.74rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    color: #475569 !important;
    padding: 13px 16px !important;
    border-bottom: 2px solid #e2e8f0 !important;
}
html:not(.dark) .fi-ta-row {
    transition: background-color 0.16s ease !important;
}
html:not(.dark) .fi-ta-row:hover td {
    background-color: #f0f7ff !important;
}
html:not(.dark) .fi-ta-row td {
    padding: 13px 16px !important;
    color: #334155 !important;
}

/* ── 8. BADGES & STATUS PILLS ── */
.fi-badge {
    font-weight: 700 !important;
    font-size: 0.74rem !important;
    letter-spacing: 0.02em !important;
    border-radius: 9999px !important;
    padding: 3px 11px !important;
}
html:not(.dark) .fi-badge.fi-color-success,
html:not(.dark) [class*="fi-badge"][data-value="final"] {
    background: #ecfdf5 !important;
    color: #047857 !important;
    border: 1px solid #a7f3d0 !important;
}
html:not(.dark) .fi-badge.fi-color-danger,
html:not(.dark) [class*="fi-badge"][data-value="perlu_revisi"] {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
}
html:not(.dark) .fi-badge.fi-color-warning {
    background: #fffbeb !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
}
html:not(.dark) .fi-badge.fi-color-primary,
html:not(.dark) .fi-badge.fi-color-info {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
}
html:not(.dark) .fi-badge.fi-color-gray {
    background: #f8fafc !important;
    color: #64748b !important;
    border: 1px solid #e2e8f0 !important;
}

/* ── 9. BUTTONS (Modern Gradient & Tactile Shadow) ── */
.fi-btn-color-primary,
button[type="submit"].fi-btn {
    background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35) !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.fi-btn-color-primary:hover,
button[type="submit"].fi-btn:hover {
    box-shadow: 0 6px 18px rgba(2, 132, 199, 0.45) !important;
    transform: translateY(-1px) !important;
}

/* ── 10. FORM INPUTS & SELECTS ── */
.fi-input-wrp {
    border-radius: 10px !important;
    border-color: #cbd5e1 !important;
    transition: all 0.2s ease !important;
}
.fi-input-wrp:focus-within {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18) !important;
}
.fi-fo-field-wrp-label label {
    font-weight: 700 !important;
    font-size: 0.85rem !important;
    color: #1e293b !important;
}
.fi-fo-field-wrp-helper-text {
    font-size: 0.78rem !important;
    color: #64748b !important;
}

/* ── 11. INFOLIST TEXT ENTRIES ── */
html:not(.dark) .fi-in-text-label,
html:not(.dark) .fi-in-entry-ctn-label {
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    margin-bottom: 0.25rem !important;
}
html:not(.dark) .fi-in-text-entry > div,
html:not(.dark) .fi-in-text .fi-in-entry-ctn-content {
    font-size: 0.92rem !important;
    font-weight: 600 !important;
    color: #0f172a !important;
}

/* ── 12. PAGE TITLE WITH GRADIENT ACCENT ── */
.fi-page-header-heading {
    font-weight: 900 !important;
    font-size: 1.45rem !important;
    letter-spacing: -0.02em !important;
    background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}
.fi-page-header-subheading {
    font-weight: 600 !important;
    color: #64748b !important;
    font-size: 0.88rem !important;
}

/* ── 13. MODERN VIBRANT & COLORFUL SPLIT LOGIN PAGE (SIMONIKA DESIGN SYSTEM) ── */
.fi-simple-layout {
    background: #eef5fb !important;
    background-image: 
        radial-gradient(at 5% 5%,   rgba(2, 132, 199, 0.25) 0px, transparent 50%),
        radial-gradient(at 95% 5%,  rgba(139, 92, 246, 0.22) 0px, transparent 48%),
        radial-gradient(at 50% 95%, rgba(16, 185, 129, 0.20) 0px, transparent 50%),
        radial-gradient(at 95% 95%, rgba(245, 158, 11, 0.18) 0px, transparent 45%),
        radial-gradient(at 5% 95%,  rgba(236, 72, 153, 0.12) 0px, transparent 40%),
        #eef5fb !important;
    min-height: 100vh !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 24px 16px !important;
}

.fi-simple-main-ctn {
    width: 100% !important;
    max-width: 1080px !important;
    margin: auto !important;
    padding: 0 !important;
}

.fi-simple-main {
    background: #ffffff !important;
    border-radius: 28px !important;
    border: 1.5px solid rgba(226, 232, 240, 0.9) !important;
    box-shadow: 0 25px 70px -15px rgba(15, 44, 89, 0.18), 0 0 0 1px rgba(56, 189, 248, 0.2) !important;
    padding: 0 !important;
    max-width: 1080px !important;
    width: 100% !important;
    overflow: hidden !important;
    position: relative !important;
}

/* 5-Color Bold Rainbow Bar at Card Top (Blue, Cyan, Emerald, Amber, Violet) */
.simonika-rainbow-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #2563eb 0%, #06b6d4 25%, #10b981 50%, #f59e0b 75%, #8b5cf6 100%);
    z-index: 20;
}

/* Reset inner simple page margins */
.fi-simple-page,
.fi-simple-page-content {
    padding: 0 !important;
    margin: 0 !important;
}

/* Split Container */
.simonika-split-wrapper {
    display: flex;
    flex-direction: row;
    min-height: 630px;
    width: 100%;
    position: relative;
}

/* Left Column: Colorful Pastel Mesh Gradient with Centered Brand Showcase */
.simonika-split-left {
    flex: 1.15;
    background: linear-gradient(145deg, #e0f2fe 0%, #ede9fe 35%, #fef3c7 75%, #d1fae5 100%) !important;
    border-right: 1px solid rgba(203, 213, 225, 0.6);
    padding: 44px 38px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
    overflow: hidden;
}

/* Ambient Colorful Glowing Light Spots */
.simonika-glow {
    position: absolute;
    border-radius: 9999px;
    pointer-events: none;
    filter: blur(55px);
    z-index: 1;
    opacity: 0.75;
}
.glow-cyan {
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.65) 0%, transparent 70%);
    top: -40px;
    left: -40px;
}
.glow-amber {
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(245, 158, 11, 0.55) 0%, transparent 70%);
    bottom: -30px;
    right: -30px;
}
.glow-violet {
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.45) 0%, transparent 70%);
    top: 35%;
    right: 5%;
}

/* Centered Brand Content Box */
.simonika-brand-center-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    justify-content: center;
    width: 100%;
    max-width: 420px;
    margin: auto;
    position: relative;
    z-index: 5;
}

/* Big SIMONIKA Emblem Logo (Centered & Enhanced) */
.simonika-big-logo-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}

.simonika-big-logo {
    width: 105px;
    height: 105px;
    object-fit: contain;
    border-radius: 22px;
    box-shadow: 0 14px 34px -4px rgba(2, 132, 199, 0.42), 0 0 0 2px rgba(255, 255, 255, 0.9);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.simonika-big-logo:hover {
    transform: scale(1.05);
    box-shadow: 0 18px 40px -4px rgba(2, 132, 199, 0.55), 0 0 0 3px rgba(56, 189, 248, 0.8);
}

/* Brand Main Title */
.simonika-brand-main-title {
    font-size: 36px;
    font-weight: 900;
    letter-spacing: 0.04em;
    margin: 0 0 6px 0;
    line-height: 1.1;
    background: linear-gradient(135deg, #0b2545 0%, #1e40af 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* PPSDMAP Below SIMONIKA */
.simonika-ppsdmap-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    margin-bottom: 16px;
}

.simonika-ppsdmap-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 850;
    background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
    color: #ffffff;
    padding: 3px 14px;
    border-radius: 6px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.32);
}

.simonika-ppsdmap-full {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    max-width: 340px;
    line-height: 1.4;
    margin: 0;
}

/* Tagline Box */
.simonika-tagline-box {
    margin: 4px 0 22px 0;
    padding: 9px 18px;
    background: rgba(255, 255, 255, 0.82);
    backdrop-filter: blur(10px);
    border-radius: 9999px;
    border: 1.5px solid rgba(2, 132, 199, 0.25);
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.08);
}

.simonika-tagline-quote {
    font-size: 13px;
    font-weight: 800;
    color: #0369a1;
    margin: 0;
    letter-spacing: -0.01em;
}

/* Quick Summary Grid (3 Compact Cards) */
.simonika-quick-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    width: 100%;
}

.summary-item {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1.5px solid rgba(255, 255, 255, 0.95);
    border-radius: 14px;
    padding: 12px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 4px;
    box-shadow: 0 4px 14px rgba(15, 44, 89, 0.08);
    transition: all 0.25s ease;
}

.summary-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(2, 132, 199, 0.18);
    border-color: #bae6fd;
}

.summary-icon {
    font-size: 22px;
    line-height: 1;
}

.summary-text strong {
    font-size: 12px;
    font-weight: 850;
    color: #0f172a;
    display: block;
}

.summary-text small {
    font-size: 10px;
    font-weight: 600;
    color: #64748b;
    line-height: 1.25;
    display: block;
    margin-top: 1px;
}

/* Right Column: Clean White with Vibrant Accents */
.simonika-split-right {
    flex: 1;
    background: #ffffff;
    padding: 46px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    z-index: 5;
}

.simonika-form-header {
    margin-bottom: 22px;
}

.simonika-portal-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 4.5px 12px;
    background: linear-gradient(135deg, #eff6ff 0%, #ede9fe 100%);
    border: 1.5px solid #bfdbfe;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 800;
    color: #1d4ed8;
    letter-spacing: 0.02em;
    margin-bottom: 12px;
}

.simonika-form-title {
    font-size: 26px;
    font-weight: 850;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin: 0 0 5px 0;
}

.simonika-form-subtitle {
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    margin: 0;
}

.simonika-form-body {
    width: 100%;
}

/* Input Fields & Submit Button inside Login Form */
.simonika-form-body input[type="email"],
.simonika-form-body input[type="text"],
.simonika-form-body input[type="password"] {
    background: #f8fafc !important;
    border-radius: 11px !important;
    border: 1.5px solid #cbd5e1 !important;
    padding-top: 11px !important;
    padding-bottom: 11px !important;
    font-size: 14px !important;
    transition: all 0.2s ease !important;
}

.simonika-form-body input:focus {
    background: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.2) !important;
}

/* Login Submit Button (Rich Radiant Gradient) */
.simonika-form-body button[type="submit"],
.simonika-form-body .fi-btn-primary {
    background: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #4f46e5 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    height: 48px !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    letter-spacing: 0.02em !important;
    box-shadow: 0 8px 24px -2px rgba(37, 99, 235, 0.45) !important;
    transition: all 0.25s ease !important;
}

.simonika-form-body button[type="submit"]:hover,
.simonika-form-body .fi-btn-primary:hover {
    background: linear-gradient(135deg, #0369a1 0%, #1d4ed8 50%, #4338ca 100%) !important;
    box-shadow: 0 12px 30px -2px rgba(37, 99, 235, 0.55) !important;
    transform: translateY(-2px) !important;
}

/* Colorful Feature Strip Under Form */
.simonika-feature-strip {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 18px;
    justify-content: center;
}

.feat-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    letter-spacing: 0.02em;
}
.feat-blue {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
}
.feat-green {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
}
.feat-amber {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #b45309;
}

.simonika-login-footer {
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    text-align: center;
}

.simonika-footer-copy {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin: 0;
}

.simonika-footer-sub {
    font-size: 10.5px;
    color: #94a3b8;
    margin: 2px 0 0 0;
}

/* Responsive Breakpoints */
@media (max-width: 880px) {
    .simonika-split-wrapper {
        flex-direction: column;
    }
    .simonika-split-left {
        border-right: none;
        border-bottom: 1px solid #edf2f7;
        padding: 32px 20px 24px 20px;
    }
    .simonika-big-logo {
        width: 75px;
        height: 75px;
    }
    .simonika-brand-main-title {
        font-size: 28px;
    }
    .simonika-quick-summary {
        gap: 6px;
    }
    .summary-item {
        padding: 8px 6px;
    }
    .summary-text strong {
        font-size: 11px;
    }
    .summary-text small {
        font-size: 9px;
    }
    .simonika-split-right {
        padding: 32px 20px;
    }
}

/* Dark Mode for Split Login */
.dark .fi-simple-main {
    background: #0f172a !important;
    border-color: #1e293b !important;
}
.dark .simonika-split-left {
    background: linear-gradient(155deg, #0b1528 0%, #111d35 50%, #0f172a 100%) !important;
    border-color: #1e293b !important;
}
.dark .simonika-split-right {
    background: #0f172a !important;
}
.dark .simonika-brand-main-title {
    background: linear-gradient(135deg, #ffffff 0%, #93c5fd 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}
.dark .simonika-form-title {
    color: #f8fafc !important;
}
.dark .simonika-ppsdmap-full,
.dark .simonika-form-subtitle {
    color: #94a3b8 !important;
}
.dark .simonika-tagline-box {
    background: rgba(30, 41, 59, 0.7) !important;
    border-color: rgba(56, 189, 248, 0.3) !important;
}
.dark .simonika-tagline-quote {
    color: #38bdf8 !important;
}
.dark .summary-item {
    background: rgba(30, 41, 59, 0.85) !important;
    border-color: rgba(51, 65, 85, 0.7) !important;
}
.dark .summary-text strong {
    color: #f8fafc !important;
}
.dark .summary-text small {
    color: #94a3b8 !important;
}
.dark .simonika-portal-tag {
    background: rgba(30, 58, 138, 0.3) !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
    color: #93c5fd !important;
}
.dark .simonika-login-footer {
    border-color: #1e293b !important;
}

/* ══════════════════════════════════════════════════════════════════
   14. COMPREHENSIVE DARK MODE SYSTEM (Zero Leaks, Perfect Contrast)
   ══════════════════════════════════════════════════════════════════ */

.dark body {
    background-color: #0b1120 !important;
    background-image:
        radial-gradient(at 0% 0%,   rgba(37, 99, 235, 0.15) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(109, 40, 217, 0.12) 0px, transparent 45%),
        radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.08) 0px, transparent 50%) !important;
    background-attachment: fixed !important;
    color: #e2e8f0 !important;
}

.dark .fi-topbar {
    background: rgba(13, 20, 38, 0.92) !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    border-bottom: 1px solid rgba(51, 65, 85, 0.6) !important;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.35) !important;
}
.dark .fi-topbar::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #38bdf8, #818cf8, #34d399, #fbbf24);
    opacity: 0.8;
}

.dark .fi-sidebar {
    background: linear-gradient(180deg, #0d1626 0%, #0f1e35 100%) !important;
    border-right: 1px solid rgba(51, 65, 85, 0.5) !important;
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3) !important;
}
.dark .fi-sidebar-header {
    background: rgba(13, 22, 38, 0.95) !important;
    border-bottom: 1px solid rgba(51, 65, 85, 0.5) !important;
}
.dark .fi-sidebar-item > a,
.dark .fi-sidebar-item > button {
    color: #94a3b8 !important;
}
.dark .fi-sidebar-item > a:hover,
.dark .fi-sidebar-item > button:hover {
    background-color: rgba(59, 130, 246, 0.14) !important;
    color: #93c5fd !important;
}
.dark .fi-sidebar-item-active > a,
.dark .fi-sidebar-item-active > button {
    background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35) !important;
}
.dark .fi-sidebar-item-active svg {
    color: #ffffff !important;
}

.dark .fi-page-header-heading {
    background: linear-gradient(135deg, #f1f5f9 0%, #93c5fd 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}
.dark .fi-page-header-subheading {
    color: #94a3b8 !important;
}

.dark .fi-section:not(.fi-wi-stats-overview .fi-section),
.dark .fi-wi-chart,
.dark .fi-wi-table-wrapper,
.dark .fi-ta-ctn {
    background-color: #111827 !important;
    border: 1px solid rgba(51, 65, 85, 0.6) !important;
    box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.03) !important;
    border-radius: 16px !important;
}
.dark .fi-section-header-heading,
.dark .fi-wi-chart header h3,
.dark .fi-wi-table-wrapper header h3 {
    color: #f1f5f9 !important;
}

/* Dark Table */
.dark .fi-ta-header {
    background: rgba(17, 24, 39, 0.8) !important;
    border-bottom: 1px solid rgba(51, 65, 85, 0.6) !important;
}
.dark .fi-ta-header-cell {
    background: #0f172a !important;
    color: #94a3b8 !important;
    border-bottom: 1px solid rgba(51, 65, 85, 0.7) !important;
}
.dark .fi-ta-row td {
    color: #cbd5e1 !important;
    border-bottom: 1px solid rgba(30, 41, 59, 0.8) !important;
}
.dark .fi-ta-row:hover td {
    background-color: rgba(59, 130, 246, 0.08) !important;
    color: #f8fafc !important;
}

/* Dark Forms & Inputs (NO WHITE BOXES) */
.dark .fi-input-wrp {
    background-color: #0f172a !important;
    border-color: rgba(51, 65, 85, 0.8) !important;
    color: #f1f5f9 !important;
}
.dark .fi-input-wrp:focus-within {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
}
.dark input, .dark textarea, .dark select {
    background-color: transparent !important;
    color: #f1f5f9 !important;
}
.dark select option {
    background-color: #111827 !important;
    color: #f1f5f9 !important;
}
.dark .fi-fo-field-wrp-label label {
    color: #e2e8f0 !important;
}
.dark .fi-fo-field-wrp-helper-text {
    color: #94a3b8 !important;
}
.dark .choices__list--dropdown,
.dark .choices__list[aria-expanded] {
    background-color: #111827 !important;
    border-color: rgba(51, 65, 85, 0.8) !important;
    color: #f1f5f9 !important;
}

/* Dark Infolist */
.dark .fi-in-text-label,
.dark .fi-in-entry-ctn-label {
    color: #94a3b8 !important;
}
.dark .fi-in-text-entry > div,
.dark .fi-in-text .fi-in-entry-ctn-content {
    color: #f1f5f9 !important;
}
.dark .fi-in-repeatable-entry-ctn {
    background: rgba(17, 24, 39, 0.6) !important;
    border-color: rgba(51, 65, 85, 0.6) !important;
}

/* Dark Badges */
.dark .fi-badge.fi-color-success,
.dark [class*="fi-badge"][data-value="final"] {
    background: rgba(6, 78, 59, 0.45) !important;
    color: #6ee7b7 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
}
.dark .fi-badge.fi-color-danger,
.dark [class*="fi-badge"][data-value="perlu_revisi"] {
    background: rgba(127, 29, 29, 0.45) !important;
    color: #fca5a5 !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important;
}
.dark .fi-badge.fi-color-warning {
    background: rgba(120, 53, 15, 0.45) !important;
    color: #fcd34d !important;
    border: 1px solid rgba(245, 158, 11, 0.35) !important;
}
.dark .fi-badge.fi-color-primary,
.dark .fi-badge.fi-color-info {
    background: rgba(30, 58, 138, 0.45) !important;
    color: #93c5fd !important;
    border: 1px solid rgba(59, 130, 246, 0.35) !important;
}
.dark .fi-badge.fi-color-gray {
    background: rgba(30, 41, 59, 0.6) !important;
    color: #cbd5e1 !important;
    border: 1px solid rgba(51, 65, 85, 0.6) !important;
}

/* Dark Modals & Dialogs */
.dark .fi-modal-window {
    background: #111827 !important;
    border: 1px solid rgba(51, 65, 85, 0.7) !important;
    box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.7) !important;
    border-radius: 20px !important;
}
.dark .fi-modal-header {
    border-bottom: 1px solid rgba(51, 65, 85, 0.5) !important;
}
.dark .fi-modal-header-heading {
    color: #f1f5f9 !important;
}
.dark .fi-modal-footer {
    border-top: 1px solid rgba(51, 65, 85, 0.5) !important;
    background: rgba(17, 24, 39, 0.6) !important;
}

/* ── 15. CUSTOM SLEEK SCROLLBAR ── */
::-webkit-scrollbar {
    width: 6px !important;
    height: 6px !important;
}
html:not(.dark) ::-webkit-scrollbar-track {
    background: #f1f5f9 !important;
}
html:not(.dark) ::-webkit-scrollbar-thumb {
    background: #cbd5e1 !important;
    border-radius: 9999px !important;
}
html:not(.dark) ::-webkit-scrollbar-thumb:hover {
    background: #0284c7 !important;
}
.dark ::-webkit-scrollbar-track {
    background: #0b1120 !important;
}
.dark ::-webkit-scrollbar-thumb {
    background: #334155 !important;
    border-radius: 9999px !important;
}
.dark ::-webkit-scrollbar-thumb:hover {
    background: #38bdf8 !important;
}

/* ── TOPBAR PAGE INDICATOR / STICKY BREADCRUMB ── */
.fi-topbar-page-indicator {
    display: inline-flex !important;
    align-items: center !important;
    user-select: none !important;
}
@media (max-width: 1023px) {
    .fi-topbar-page-indicator {
        display: none !important;
    }
}

/* ── VIEW RECORD / DETAIL REPORT POLISH ── */
.fi-in-hero-banner {
    transition: all 0.25s ease !important;
}
.fi-in-hero-banner:hover {
    box-shadow: 0 14px 36px -4px rgba(2, 132, 199, 0.45) !important;
}

/* Repeatable items in detail pages (Pelatihan, NSPK, Kerjasama) */
.fi-in-repeatable-entry {
    background: #f8fafc !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 12px !important;
    padding: 16px !important;
    margin-bottom: 12px !important;
    transition: all 0.2s ease !important;
}
.fi-in-repeatable-entry:hover {
    border-color: #93c5fd !important;
    background: #ffffff !important;
}
.dark .fi-in-repeatable-entry {
    background: rgba(17, 24, 39, 0.7) !important;
    border-color: rgba(51, 65, 85, 0.6) !important;
}
.dark .fi-in-repeatable-entry:hover {
    border-color: rgba(59, 130, 246, 0.5) !important;
    background: rgba(17, 24, 39, 0.95) !important;
}


/* ── DASHBOARD PIC ACTION ITEMS FULL SPAN ── */
.fi-wi-widget:has([data-widget="pic-action-items"]),
.fi-wi-dashboard-pic-action-items {
    grid-column: 1 / -1 !important;
}

</style>
