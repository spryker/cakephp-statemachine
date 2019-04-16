<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell\Task;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use StateMachine\FacadeAwareTrait;

class CheckTimeoutTask extends Shell
{
    use FacadeAwareTrait;

    /**
     * @param string $stateMachineName
     *
     * @return void
     */
    public function check(string $stateMachineName): void
    {
        $this->validateStateMachineName($stateMachineName);

        $this->getFacade()->checkTimeouts($stateMachineName);
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

        $parser->addArgument('stateMachineName', [
            'help' => 'Required state machine name',
            'required' => true,
        ]);

        return $parser;
    }
}
