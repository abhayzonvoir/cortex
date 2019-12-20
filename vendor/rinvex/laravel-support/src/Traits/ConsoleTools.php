<?php

declare(strict_types=1);

namespace Rinvex\Support\Traits;

trait ConsoleTools
{
    /**
     * Publish package migrations.
     *
     * @return void
     */
    protected function publishesMigrations(string $package, bool $isModule = false): void
    {
        $namespace = str_replace('laravel-', '', $package);
        $namespace = str_replace(['/', '\\', '.', '_'], '-', $namespace);
        $basePath = $isModule ? $this->app->path($package)
            : $this->app->basePath('vendor/'.$package);

        if (file_exists($path = $basePath.'/database/migrations')) {
            $stubs = $this->app['files']->glob($path.'/*.php.stub');
            $existing = $this->app['files']->glob($this->app->databasePath('migrations/'.$package.'/*.php'));

            $migrations = collect($stubs)->flatMap(function ($migration) use ($existing, $package) {
                $sequence = mb_substr(basename($migration), 0, 2);
                $match = collect($existing)->first(function ($item, $key) use ($migration, $sequence) {
                    return mb_strpos($item, str_replace(['.stub', $sequence], '', basename($migration))) !== false;
                });

                return [$migration => $this->app->databasePath('migrations/'.$package.'/'.($match ? basename($match) : date('Y_m_d_His', time() + $sequence).str_replace(['.stub', $sequence], '', basename($migration))))];
            })->toArray();

            $this->publishes($migrations, $namespace.'-migrations');
        }
    }

    /**
     * Publish package config.
     *
     * @return void
     */
    protected function publishesConfig(string $package, bool $isModule = false): void
    {
        $namespace = str_replace('laravel-', '', $package);
        $namespace = str_replace(['/', '\\', '.', '_'], '-', $namespace);
        $basePath = $isModule ? $this->app->path($package)
            : $this->app->basePath('vendor/'.$package);

        if (file_exists($path = $basePath.'/config/config.php')) {
            $this->publishes([$path => $this->app->configPath(str_replace('-', '.', $namespace).'.php')], $namespace.'-config');
        }
    }

    /**
     * Publish package views.
     *
     * @return void
     */
    protected function publishesViews(string $package, bool $isModule = false): void
    {
        $namespace = str_replace('laravel-', '', $package);
        $namespace = str_replace(['/', '\\', '.', '_'], '-', $namespace);
        $basePath = $isModule ? $this->app->path($package)
            : $this->app->basePath('vendor/'.$package);

        if (file_exists($path = $basePath.'/resources/views')) {
            $this->publishes([$path => $this->app->resourcePath('views/vendor/'.$package)], $namespace.'-views');
        }
    }

    /**
     * Publish package lang.
     *
     * @return void
     */
    protected function publishesLang(string $package, bool $isModule = false): void
    {
        $namespace = str_replace('laravel-', '', $package);
        $namespace = str_replace(['/', '\\', '.', '_'], '-', $namespace);
        $basePath = $isModule ? $this->app->path($package)
            : $this->app->basePath('vendor/'.$package);

        if (file_exists($path = $basePath.'/resources/lang')) {
            $this->publishes([$path => $this->app->resourcePath('lang/vendor/'.$package)], $namespace.'-lang');
        }
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        // Register artisan commands
        foreach ($this->commands as $key => $value) {
            $this->app->singleton($value, $key);
        }

        $this->commands(array_values($this->commands));
    }
}
