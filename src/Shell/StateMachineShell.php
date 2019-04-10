<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * @author Mark Scherer
 *
 * @property \StateMachine\Shell\Task\CheckConditionTask $CheckCondition
 * @property \StateMachine\Shell\Task\CheckTimeoutTask $CheckTimeout
 * @property \StateMachine\Shell\Task\ClearLocksTask $ClearLocks
 */
class StateMachineShell extends Shell
{
    /**
     * @var array
     */
    public $tasks = [
        'StateMachine.CheckCondition',
        'StateMachine.CheckTimeout',
        'StateMachine.ClearLocks',
    ];

    /**
     * @param string|null $stateMachineName
     *
     * @return void
     */
    public function checkTimeouts($stateMachineName = null): void
    {
        $this->CheckTimeout->check($stateMachineName);
    }

    /**
     * @param string|null $stateMachineName
     *
     * @return void
     */
    public function checkConditions($stateMachineName = null): void
    {
        $this->CheckCondition->check($stateMachineName);
    }

    /**
     * @return void
     */
    public function clearLocks(): void
    {
        $this->ClearLocks->main();
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('checkConditions', [
            'help' => 'Check if any conditions match. Please provide valid state machine name as an argument.',
            'parser' => $this->CheckCondition->getOptionParser(),
        ]);

        $parser->addSubcommand('checkTimeouts', [
            'help' => 'Check if any timeouts match. Please provide valid state machine name as an argument.',
            'parser' => $this->CheckTimeout->getOptionParser(),
        ]);

        $parser->addSubcommand('clearLocks', [
            'help' => 'Clear expired locks from lock table.',
            'parser' => $this->ClearLocks->getOptionParser(),
        ]);

        return $parser;
    }
}
