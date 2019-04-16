<?php

namespace App\StateMachine\Command;

use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

class IsFooTriggeredCondition implements ConditionPluginInterface
{
    /**
     * Specification:
     * - This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function check(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        if (!file_exists(TMP . 'triggered.txt')) {
            return false;
        }

        return (bool)file_get_contents(TMP . 'triggered.txt', date(FORMAT_DB_DATETIME));
    }
}
