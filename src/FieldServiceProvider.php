<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event): void {
            Nova::script('action-buttons', __DIR__.'/../dist/js/field.js');
            Nova::style('action-buttons', __DIR__.'/../dist/css/field.css');
        });
    }
}
