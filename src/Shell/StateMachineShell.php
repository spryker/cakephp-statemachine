<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use StateMachine\FacadeAwareTrait;

class StateMachineShell extends Shell
{
    use FacadeAwareTrait;

    /**
     * @param string $stateMachine
     *
     * @return void
     */
    public function checkTimeouts(string $stateMachine): void
    {
        $this->validateStateMachineName($stateMachine);

        $affected = $this->getFacade()->checkTimeouts($stateMachine);

        $this->verbose('Affected: ' . $affected);
    }

    /**
     * @param string $stateMachine
     *
     * @return void
     */
    public function checkConditions(string $stateMachine): void
    {
        $this->validateStateMachineName($stateMachine);

        $affected = $this->getFacade()->checkConditions($stateMachine);

        $this->verbose('Affected: ' . $affected);
    }

    /**
     * @return void
     */
    public function clearLocks(): void
    {
        $this->getFacade()->clearLocks();
    }

    /**
     * @param string $stateMachineName
     *
     * @return void
     */
    protected function validateStateMachineName(string $stateMachineName): void
    {
        if (!$this->getFacade()->stateMachineExists($stateMachineName)) {
            $this->abort(sprintf('State machine "%s" was not found.', $stateMachineName));
        }
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('checkConditions', [
            'help' => 'Check if any conditions match.',
            'parser' => [
                'arguments' => [
                    'stateMachine' => [
                        'help' => 'State machine name',
                        'required' => true,
                    ],
                ],
            ],
        ]);

        $parser->addSubcommand('checkTimeouts', [
            'help' => 'Check if any timeouts match.',
            'parser' => [
                'arguments' => [
                    'stateMachine' => [
                        'help' => 'State machine name',
                        'required' => true,
                    ],
                ],
            ],
        ]);

        $parser->addSubcommand('clearLocks', [
            'help' => 'Clear expired locks from lock table.',
        ]);

        return $parser;
    }
}
