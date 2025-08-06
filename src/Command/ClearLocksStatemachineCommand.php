<?php

namespace StateMachine\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use StateMachine\FacadeAwareTrait;

class ClearLocksStatemachineCommand extends Command
{
    use FacadeAwareTrait;

    public static function defaultName(): string {
        return 'state_machine clear-locks';
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->getFacade()->clearLocks();

        return static::CODE_SUCCESS;
    }
}
