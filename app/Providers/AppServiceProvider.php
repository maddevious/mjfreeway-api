<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Validator;
use Queue;
use App\Models\Drink;
use App\Observers\DrinkObserver;
use App\Models\Usage;
use App\Observers\UsageObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Drink::observe(DrinkObserver::class);
        Usage::observe(UsageObserver::class);

        if (env('APP_DEBUG')) {
            ini_set('display_errors', 'On');
        } else {
            ini_set('display_errors', 'Off');
            set_error_handler([$this, "notice_handler"], E_NOTICE);
            set_error_handler([$this, "warning_handler"], E_WARNING);
        }
        Validator::extend('money', 'App\Services\CustomValidator@validateMoney');
        Validator::extend('phone', 'App\Services\CustomValidator@validatePhone');
    }

    public function notice_handler($severity, $message, $filename, $lineno) {
        Log::notice("$message in {$filename}:{$lineno}");
    }

    public function warning_handler($severity, $message, $filename, $lineno) {
        Log::warning("$message in {$filename}:{$lineno}");
    }
}
