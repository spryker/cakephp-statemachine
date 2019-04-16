<?php

namespace App\StateMachine\Command;

use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

class TriggerFooCommand implements CommandPluginInterface
{
    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        file_put_contents(TMP . 'triggered.txt', date(FORMAT_DB_DATETIME));

        return true;
    }
}
