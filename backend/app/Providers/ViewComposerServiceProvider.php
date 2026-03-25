<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.leftmenu', 'App\Http\Composers\MyRequestsCountComposer');
        View::composer(['layouts.leftmenu','layouts.master'], 'App\Http\Composers\MyApprovalsCountComposer');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
