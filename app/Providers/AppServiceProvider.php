<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) 
        // {
        //     DB::statement(DB::raw('PRAGMA foreign_keys=1'));
        // }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
