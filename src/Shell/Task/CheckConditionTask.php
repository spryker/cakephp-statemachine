<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell\Task;

use Cake\Console\Shell;
use StateMachine\FacadeAwareTrait;

class CheckConditionTask extends Shell
{
    use FacadeAwareTrait;

    protected const ERR_NO_STATE_MACHINE_NAME = 'No state machine name was provided.';
    protected const ERR_STATE_MACHINE_NOT_FOUND = 'State machine "%s" was not found.';

    public function check($stateMachineName = null): void
    {
        if ($this->validateStateMachineName($stateMachineName) === false) {
            return;
        }

        $this->getFacade()->checkConditions($stateMachineName);
    }

    /**
     * @param string|null $stateMachineName
     *
     * @return bool
     */
    protected function validateStateMachineName($stateMachineName = null): bool
    {
        if ($stateMachineName === null) {
            $this->err(static::ERR_NO_STATE_MACHINE_NAME);

            return false;
        }

        if ($this->getFacade()->stateMachineExists($stateMachineName) === false) {
            $this->err(sprintf(static::ERR_STATE_MACHINE_NOT_FOUND, $stateMachineName));

            return false;
        }

        return true;
    }
}
