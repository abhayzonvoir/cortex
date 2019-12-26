<?php

declare(strict_types=1);

namespace Cortex\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CoreMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cortex:migrate {--force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Cortex Tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $commands = collect(Artisan::all())->filter(function ($command) {
            return mb_strpos($command->getName(), 'cortex:migrate:') !== false;
        })->partition(function ($command) {
            return in_array($command->getName(), ['cortex:migrate:foundation', 'cortex:migrate:auth']);
        })->flatten();

        $progressBar = $this->output->createProgressBar($commands->count());
        $progressBar->setBarCharacter('<fg=green>▒</>');
        $progressBar->setEmptyBarCharacter('<fg=white>▒</>');
        $progressBar->setProgressCharacter('<fg=green>➤</>');
        $progressBar->setFormat("<fg=yellow>{$this->description}. (Step %current% / %max%)</>\n[%bar%] %percent%%\nElapsed Time: %elapsed%");
        $progressBar->start();

        $output = new BufferedOutput();
        $commands->each(function (Command $command) use ($progressBar, $output) {
            $command->run(new ArrayInput(['--force' => $this->option('force')]), $output);
            $progressBar->advance();
        });

        $progressBar->finish();

        $this->laravel['log']->channel('installer')->debug("\n".$output->fetch());

        $this->line('');
        $this->line('');
    }
}
