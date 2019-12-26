<?php

declare(strict_types=1);

namespace Cortex\Foundation\Console\Commands;

use Illuminate\Console\ConfirmableTrait;
use Cortex\Foundation\Traits\ConsoleMakeModuleCommand;
use Illuminate\Foundation\Console\EventGenerateCommand as BaseEventGenerateCommand;

class EventGenerateCommand extends BaseEventGenerateCommand
{
    use ConfirmableTrait;
    use ConsoleMakeModuleCommand;
}
