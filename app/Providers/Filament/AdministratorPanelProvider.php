<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Reports;
use App\Filament\Widgets\StatsOverview;
use App\Services\GuidedTour;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdministratorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('administrator')
            ->path('administrator')
            ->viteTheme('resources/css/filament/administrator/theme.css')
            ->spa()
            ->login(Login::class)
            ->brandName('HBCR PEDIA')
            // ->brandLogo(asset('bg.png'))
            // ->brandLogoHeight('2.75rem')
            // ->favicon(asset('favicon.svg'))
            ->simplePageMaxContentWidth(Width::SevenExtraLarge)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->navigationGroups([

                NavigationGroup::make()
                    ->label('Main')
                    ->collapsible(false),

                NavigationGroup::make()
                    ->label('Classifications')
                    ->collapsible(),

                NavigationGroup::make()
                    ->label('Settings')
                    ->collapsible(false),

                NavigationGroup::make()
                    ->label('Reports')
                    ->icon(Heroicon::OutlinedChartBar),
            ])
            // ->navigationItems([
            //     NavigationItem::make('Generate Report')
            //         ->icon(Heroicon::OutlinedLink)
            //         ->url('reports')
            //         ->group('Main')
            //         ->sort(3),
            // ])
            ->pages([
                Dashboard::class,
                Reports::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
                StatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Settings')
                    ->navigationLabel('Roles & Permissions')
                    ->navigationSort(2),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.tour.tour-button')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.tour.guided-tour', ['tour' => self::currentTour()])->render(),
            )
            ->resources([
                config('filament-logger.activity_resource'),
            ]);
    }

    /**
     * The guided tour for the page being viewed, or null on pages without one.
     *
     * @return array{id: string, title: string, steps: list<array{target: ?string, match: string, title: string, body: string}>}|null
     */
    private static function currentTour(): ?array
    {
        if (! auth()->check()) {
            return null;
        }

        return app(GuidedTour::class)->forRoute(Route::currentRouteName());
    }
}
