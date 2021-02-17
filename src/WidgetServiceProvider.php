<?php

namespace Russsiq\Widget;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Russsiq\Widget\Console\Commands\MakeWidgetCommand;
use Russsiq\Widget\Contracts\ParameterBagContract;
use Russsiq\Widget\Contracts\WidgetContract;
use Russsiq\Widget\Support\Parameters;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Package root directory.
     *
     * @const string
     */
    const SOURCE_DIR = __DIR__.'/../';

    /**
    * Short package name.
    *
    * @const string
    */
   const PACKAGE_NAME = 'laravel-widget';

    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        ParameterBagContract::class => Parameters::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->sourcePath('config/laravel-widget.php'), 'laravel-widget'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadWidgetFiles()
            ->publishWidgetFiles()
            ->registerWidgetCommands();

        $this->callAfterResolving(
            BladeCompiler::class,
            function (BladeCompiler $compiler, Application $app) {
                $compiler->componentNamespace(
                    $app['config']->get(
                        'laravel-widget.classes-namespace',
                        $app->getNamespace().'View\\Components\\Widgets'
                    ),
                    'widget'
                );
            }
        );

        $this->app->resolving(
            WidgetContract::class,
            function (WidgetContract $widget, Container $container) {
                $widget->setContainer($container)
                    ->setParameterBag(
                        $container->make(ParameterBagContract::class)
                    );
            }
        );

        $this->app->afterResolving(
            WidgetContract::class,
            function (WidgetContract $widget, Container $container) {
                $widget->validateResolved();
            }
        );
    }

    /**
     * Load package files.
     *
     * @return $this
     */
    protected function loadWidgetFiles(): self
    {
        $this->loadViewsFrom(
            $this->sourcePath('resources/views'), 'laravel-widget'
        );

        return $this;
    }

    /**
     * Register package paths to be published by the publish command.
     *
     * @cmd `php artisan vendor:publish --provider="Russsiq\Widget\WidgetServiceProvider"`
     *
     * @return $this
     */
    protected function publishWidgetFiles(): self
    {
        if ($this->app->runningInConsole()) {
            // @cmd `php artisan vendor:publish --provider="Russsiq\Widget\WidgetServiceProvider" --tag=config`
            $this->publishes([
                $this->sourcePath('config/laravel-widget.php') => config_path('laravel-widget.php'),
            ], 'config');

            // @cmd `php artisan vendor:publish --provider="Russsiq\Widget\WidgetServiceProvider" --tag=views`
            $this->publishes([
                $this->sourcePath('resources/views') => resource_path('views/vendor/laravel-widget'),
            ], 'views');
        }

        return $this;
    }

    /**
     * Register package commands.
     *
     * @return $this
     */
    protected function registerWidgetCommands(): self
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeWidgetCommand::class,
            ]);
        }

        return $this;
    }

    /**
     * Get the path to the package folder.
     *
     * @param  string  $path
     * @return string
     */
    protected function sourcePath(string $path): string
    {
        return self::SOURCE_DIR.$path;
    }
}
