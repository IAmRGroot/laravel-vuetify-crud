<?php

namespace IAmRGroot\VuetifyCRUD;

use IAmRGroot\VuetifyCRUD\Commands\GenerateVuetifyCRUD;
use Illuminate\Support\ServiceProvider;

class VuetifyCRUDServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateVuetifyCRUD::class,
            ]);
        }
    }
}
