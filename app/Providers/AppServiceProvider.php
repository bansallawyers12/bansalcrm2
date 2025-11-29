<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register custom facade aliases
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Form', App\Helpers\Form::class);
        $loader->alias('PDF', Barryvdh\DomPDF\Facade::class);
        $loader->alias('Excel', Maatwebsite\Excel\Facades\Excel::class);
        $loader->alias('Settings', App\Helpers\Settings::class);
        $loader->alias('Helper', App\Helpers\Helper::class);
        $loader->alias('DataTables', Yajra\DataTables\Facades\DataTables::class);
    }
}
