<?php

namespace Russsiq\Widget\Console\Commands;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Russsiq\Widget\WidgetServiceProvider;
use Symfony\Component\Console\Input\InputOption;

class MakeWidgetCommand extends GeneratorCommand
{
    /**
     * The Config Repository instance.
     *
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:laravel-widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget class with view.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Widget';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        return $this->writeView();
    }

    /**
     * Write the view for the widget.
     *
     * @return bool
     */
    protected function writeView(): bool
    {
        $path = $this->viewPath(
            str_replace('.', '/', 'components.widgets.'.$this->getView()).'.blade.php'
        );

        $this->makeDirectory($path);

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->error('Widget view already exists!');

            return false;
        }

        if (false === file_put_contents($path, "<section>\n\t<!--  -->\n</section>")) {
            $this->error('Can\'t create a widget view!');

            return false;
        }

        return true;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name): string
    {
        return str_replace(
            'DummyTemplate',
            'components.widgets.'.$this->getView(),
            parent::buildClass($name)
        );
    }

    /**
     * Get the view name relative to the components directory.
     *
     * @return string view
     */
    protected function getView(): string
    {
        $name = str_replace('\\', '/', $this->argument('name'));

        return collect(explode('/', $name))
            ->map(function ($part) {
                return Str::kebab($part);
            })
            ->implode('.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return WidgetServiceProvider::SOURCE_DIR.'/stubs/class-widget.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->configRepository()->get(
            'laravel-widget.classes-namespace', $rootNamespace.'\View\Components\Widgets'
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the widget already exists'],
        ];
    }

    /**
     * Get the Config Repository.
     *
     * @return configRepository
     */
    protected function configRepository(): configRepository
    {
        return $this->configRepository
            ?: $this->configRepository = $this->laravel['config'];
    }
}
