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
        $this->publishes([
            __DIR__ . '/config/service-gen.php' => config_path('service-gen.php'),
        ]);
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServiceGenerator();
        $this->registerRepositoryServiceGenerator();
    }

    /**
     * Register the make:service command
     */
    private function registerServiceGenerator()
    {
        $this->app->singleton('command.maltex.service', function ($app) {
            return $app['Maltex\Generators\Commands\ServiceMakeCommand'];
        });
        $this->commands('command.maltex.service');
    }

    /**
     * Register the make:repo-service command
     */
    private function registerRepositoryServiceGenerator()
    {
        $this->app->singleton('command.maltex.repo-service', function ($app) {
            return $app['Maltex\Generators\Commands\RepositoryServiceMakeCommand'];
        });
        $this->commands('command.maltex.repo-service');
    }
}