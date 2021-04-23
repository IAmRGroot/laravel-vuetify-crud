<?php

namespace IAmRGroot\VuetifyCRUD\Commands;

use Composer\Autoload\ClassMapGenerator;
use IAmRGroot\VuetifyCRUD\Controllers\VuetifyCRUDController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class GenerateVuetifyCRUD extends Command
{
    protected $signature = 'crud:generate';

    public function handle(): int
    {
        foreach (ClassMapGenerator::createMap('app/Http/Controllers') as $class => $path) {
            $instance = new $class();
            if (is_subclass_of($instance, VuetifyCRUDController::class)) {
                $this->generateFromController($instance);
            }
        }

        return 0;
    }

    private function generateFromController(VuetifyCRUDController $crud): void
    {
        if (! File::isDirectory(Config::get('vuetify-crud.location'))) {
            File::makeDirectory(Config::get('vuetify-crud.location'));
        }

        File::put(
            Config::get('vuetify-crud.location') . DIRECTORY_SEPARATOR . 'todo.vue',
            View::make('page', ['crud' => $crud])->render()
        );
    }
}
