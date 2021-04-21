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
     * Short package name.
     *
     * @const string
     */
    const PACKAGE_NAME = 'laravel-widget';

    /**
     * Package root directory.
     *
     * @const string
     */
    const PACKAGE_DIR = __DIR__.'/../';

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
            $this->packagePath('config/laravel-widget.php'), 'laravel-widget'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->configureCommands();
        $this->configureViews();

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
     * Configure the publishable resources offered by the package.
     *
     * @cmd `php artisan vendor:publish --provider="Russsiq\Widget\WidgetServiceProvider"`
     * @return void
     */
    protected function configurePublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // @cmd `php artisan vendor:publish --tag=laravel-widget-config`
        $this->publishes([
            $this->packagePath('config/laravel-widget.php') => config_path('laravel-widget.php'),
        ], 'laravel-widget-config');

        // @cmd `php artisan vendor:publish --tag=laravel-widget-views`
        $this->publishes([
            $this->packagePath('resources/views') => resource_path('views/vendor/laravel-widget'),
        ], 'laravel-widget-views');
    }

    /**
     * Configure the commands offered by the package.
     *
     * @return void
     */
    protected function configureCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            MakeWidgetCommand::class,
        ]);
    }

    /**
     * Configure the views offered by the package.
     *
     * @return void
     */
    protected function configureViews(): void
    {
        $this->loadViewsFrom(
            $this->packagePath('resources/views'), 'laravel-widget'
        );
    }

    /**
     * Get the path to the package folder.
     *
     * @param  string  $path
     * @return string
     */
    protected function packagePath(string $path): string
    {
        return self::PACKAGE_DIR.$path;
    }
}
