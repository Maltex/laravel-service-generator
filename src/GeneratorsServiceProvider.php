<?php
namespace Maltex\Generators;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServiceGenerator();
    }
    /**
     * Register the make:service command
     */
    private function registerServiceGenerator()
    {
        $this->app->singleton('command.maltex.service', function ($app) {
            return $app['Maltex\Generators\Commands\GeneratorMakeCommand'];
        });
        $this->commands('command.maltex.service');
    }
}