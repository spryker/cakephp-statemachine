<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace App\StateMachine;

use App\StateMachine\Command\TriggerFooCommand;
use App\StateMachine\Condition\IsFooTriggeredCondition;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;

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
            'Test/Command' => TriggerFooCommand::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        return [
            'Test/Condition' => IsFooTriggeredCondition::class,
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
     * ItemDto:identifier is id of entity from client.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function itemStateUpdated(ItemDto $itemDto): bool
    {
        return true;
    }

    /**
     * This method should return all list of ItemDto, with (identifier, IdStateMachineProcess, IdItemState)
     *
     * @param int[] $stateIds
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        return [];
    }
}
