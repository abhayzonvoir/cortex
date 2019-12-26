<?php

declare(strict_types=1);

namespace Cortex\Pages\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cortex:install:pages {--force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Cortex Pages Module.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alert($this->description);

        $this->call('cortex:publish:pages', ['--force' => $this->option('force')]);
        $this->call('cortex:migrate:pages', ['--force' => $this->option('force')]);
        $this->call('cortex:seed:pages');
    }
}
