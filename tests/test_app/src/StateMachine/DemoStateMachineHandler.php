<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine;

use App\StateMachine\Command\TriggerFooCommand;
use App\StateMachine\Condition\IsFooTriggeredCondition;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

/**
 * Real classes for controller test cases.
 */
class DemoStateMachineHandler implements StateMachineHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        return [
            'Test/Command' => new TriggerFooCommand(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        return [
            'Test/Condition' => new IsFooTriggeredCondition(),
        ];
    }

    /**
     * Name of state machine used by this handler.
     *
     * @return string
     */
    public function getStateMachineName(): string
    {
        return 'TestingSm';
    }

    /**
     * List of active processes used for this state machine.
     *
     * [
     *   'ProcessName',
     *   'ProcessName2 ,
     * ]
     *
     * @return string[]
     */
    public function getActiveProcesses(): array
    {
        return [
            'TestProcess',
        ];
    }

    /**
     * Provide initial state name for item when state machine initialized. Using process name.
     *
     * @param string $processName
     *
     * @return string
     */
    public function getInitialStateForProcess(string $processName): string
    {
        return 'new';
    }

    /**
     * This method is called when state of item was changed, client can create custom logic for example update it's related table with new stateId and processId.
     * StateMachineItemTransfer:identifier is id of entity from client.
     *
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function itemStateUpdated(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        return true;
    }

    /**
     * This method should return all list of StateMachineItemTransfer, with (identifier, IdStateMachineProcess, IdItemState)
     *
     * @param int[] $stateIds
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        return [];
    }
}
