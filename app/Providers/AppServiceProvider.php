<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Category;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
Paginator::useBootstrapFive();

        View::composer('site.partials.navbar', function ($view) {
            $view->with('navCategories', Category::with('image')
                ->whereHas('products', fn ($products) => $products->publiclyVisible())
                ->latest('id')->get());
        });

        /**
         * Permission codes double as gate abilities, so can('products.edit')
         * works in routes, controllers and Blade without registering each one.
         * The owner passes every check; everyone else is limited to the codes
         * ticked on their role. Returning null leaves unknown abilities denied.
         */
        Gate::before(function ($user, string $ability) {
            if (! $user instanceof User) {
                return null;
            }

            if ($user->isSuperAdmin()) {
                return true;
            }

            if ($user->isWarehouse()) return $ability === 'dashboard.view';

            return $user->hasPermission($ability) ? true : null;
        });
    }
}
