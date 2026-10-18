<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->profile()
            ->brandName('PPSDMAP')
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('2rem')
            ->font('Plus Jakarta Sans')
            ->colors([
                'primary' => Color::Blue,
                'gray'    => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style>
                    /* ── Brand Logo & Header Fix ── */
                    .fi-logo {
                        display: inline-flex !important;
                        align-items: center !important;
                        max-height: 36px !important;
                        overflow: hidden !important;
                    }
                    .fi-logo svg, .fi-logo img {
                        max-height: 28px !important;
                        width: auto !important;
                    }
                    .fi-sidebar-header {
                        height: 56px !important;
                        min-height: 56px !important;
                        max-height: 56px !important;
                        padding: 0 16px !important;
                        display: flex !important;
                        align-items: center !important;
                        border-bottom: 1px solid #e2e8f0 !important;
                    }
                    .fi-topbar {
                        height: 56px !important;
                    }

                    /* Sembunyikan duplikasi logo di topbar pada desktop jika sidebar terbuka */
                    @media (min-width: 1024px) {
                        .fi-topbar .fi-logo {
                            display: none !important;
                        }
                    }

                    /* ── Light Mode ── */
                    html:not(.dark) body {
                        background-color: #f8fafc !important;
                    }
                    html:not(.dark) .fi-sidebar {
                        background-color: #ffffff !important;
                        border-right: 1px solid #e2e8f0 !important;
                        box-shadow: none !important;
                    }
                    html:not(.dark) .fi-sidebar-item-active > a,
                    html:not(.dark) .fi-sidebar-item-active > button {
                        background-color: #eff6ff !important;
                        color: #1d4ed8 !important;
                        border-radius: 0.5rem !important;
                        font-weight: 600 !important;
                        border-left: 3px solid #1d4ed8 !important;
                    }
                    html:not(.dark) .fi-topbar {
                        background-color: #ffffff !important;
                        border-bottom: 1px solid #e2e8f0 !important;
                        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                    }
                    /* Cards & tables */
                    html:not(.dark) .fi-section,
                    html:not(.dark) .fi-wi-stats-overview-stat,
                    html:not(.dark) .fi-wi-chart,
                    html:not(.dark) .fi-wi-table-wrapper,
                    html:not(.dark) .fi-ta-ctn {
                        background-color: #ffffff !important;
                        border: 1px solid #e2e8f0 !important;
                        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05) !important;
                        border-radius: 0.75rem !important;
                    }
                    html:not(.dark) .fi-ta-header-cell {
                        background-color: #f8fafc !important;
                        font-size: 0.75rem !important;
                        font-weight: 600 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        color: #475569 !important;
                    }
                    html:not(.dark) .fi-ta-row:hover td {
                        background-color: #f8fafc !important;
                    }

                    /* ── Dark Mode ── */
                    html.dark body {
                        background-color: #070d1a !important;
                    }
                    html.dark .fi-sidebar {
                        background: linear-gradient(180deg, #0f172a 0%, #0b1120 100%) !important;
                        border-right: 1px solid #1e293b !important;
                        box-shadow: 2px 0 12px rgba(0,0,0,0.3) !important;
                    }
                    html.dark .fi-sidebar-item-active > a,
                    html.dark .fi-sidebar-item-active > button {
                        background: linear-gradient(90deg, rgba(14,165,233,0.25) 0%, rgba(14,165,233,0.12) 100%) !important;
                        color: #38bdf8 !important;
                        border-radius: 0.625rem !important;
                        font-weight: 700 !important;
                        border-left: 3px solid #0ea5e9 !important;
                    }
                    html.dark .fi-topbar {
                        background: linear-gradient(90deg, #0f172a 0%, #0b1120 100%) !important;
                        border-bottom: 1px solid #1e293b !important;
                        box-shadow: 0 2px 12px rgba(0,0,0,0.4) !important;
                    }
                    html.dark .fi-section,
                    html.dark .fi-wi-stats-overview-stat,
                    html.dark .fi-wi-chart,
                    html.dark .fi-wi-table-wrapper,
                    html.dark .fi-ta-ctn {
                        background: linear-gradient(135deg, #0f172a 0%, #111827 100%) !important;
                        border: 1px solid #1e293b !important;
                        box-shadow: 0 4px 16px rgba(0,0,0,0.25) !important;
                        border-radius: 1rem !important;
                    }
                    html.dark .fi-ta-header-cell {
                        background-color: #1e293b !important;
                        color: #38bdf8 !important;
                        font-size: 0.72rem !important;
                        font-weight: 700 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.06em !important;
                    }
                    html.dark .fi-ta-row:hover td {
                        background-color: rgba(14,165,233,0.05) !important;
                    }

                    /* ── Stat card hover animations ── */
                    .fi-wi-stats-overview-stat {
                        transition: transform 0.22s cubic-bezier(.4,0,.2,1),
                                    box-shadow 0.22s cubic-bezier(.4,0,.2,1) !important;
                        cursor: default;
                    }
                    .fi-wi-stats-overview-stat:hover {
                        transform: translateY(-3px) !important;
                        box-shadow: 0 8px 24px rgba(14,165,233,0.15) !important;
                    }

                    /* ── Chart widget heading ── */
                    .fi-wi-chart header h3,
                    .fi-wi-table-wrapper header h3 {
                        font-weight: 700 !important;
                        font-size: 0.9rem !important;
                    }

                    /* ── Brand logo / name style ── */
                    .fi-sidebar-brand-name {
                        font-weight: 800 !important;
                        font-size: 1.05rem !important;
                        background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
                        -webkit-background-clip: text !important;
                        -webkit-text-fill-color: transparent !important;
                        background-clip: text !important;
                    }

                    /* ── Page header ── */
                    .fi-page-header-heading {
                        font-weight: 800 !important;
                        font-size: 1.35rem !important;
                    }

                    /* ── Filter form ── */
                    .fi-fo-field-wrp {
                        border-radius: 0.625rem !important;
                    }

                    /* ── Smooth transitions ── */
                    .fi-sidebar-item > a,
                    .fi-sidebar-item > button,
                    .fi-btn {
                        transition: all 0.18s ease !important;
                    }
                </style>'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
