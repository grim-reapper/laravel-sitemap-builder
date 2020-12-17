<?php


namespace GrimReapper\LaravelSitemap;


use GrimReapper\LaravelSitemap\Contracts\SitemapManagerContract;
use GrimReapper\LaravelSitemap\Supports\SitemapManager;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class LaravelSitemapServiceProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sitemap.php',
            'sitemap'
        );

        $this->app->singleton(SitemapManagerContract::class, SitemapManager::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'gr');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','gr');
        if ($this->app->runningInConsole()) {
            $this->registerConfig();
            $this->registerViews();
            $this->registerTranslations();
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../config/sitemap.php' => config_path('sitemap.php'),
        ],'config');
    }

    /**
     * Register views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $viewPath = resource_path('views/grimreapper');
        $sourcePath = __DIR__.'/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/grimreapper/laravelsitemap')
        ]);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            SitemapManagerContract::class,
        ];
    }
}
