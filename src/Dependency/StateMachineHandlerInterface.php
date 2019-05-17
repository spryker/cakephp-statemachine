<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Dependency;

use StateMachine\Dto\StateMachine\ItemDto;

interface StateMachineHandlerInterface
{
    /**
     * List of command classes for this state machine for all processes. Array key is identifier in SM XML file.
     *
     * [
     *   'Prefix/OneCommand' => OneCommand::class,
     *   'Prefix/TwoCommand' => TwoCommand::class,
     * ]
     *
     * @return string[]
     */
    public function getCommands(): array;

    /**
     * List of condition classes for this state machine for all processes. Array key is identifier in SM XML file.
     *
     *  [
     *   'Prefix/OneCondition' => OneCondition::class,
     *   'Prefix/TwoCondition' => TwoCondition::class,
     * ]
     *
     * @return string[]
     */
    public function getConditions(): array;

    /**
     * Name of state machine used by this handler.
     *
     * @return string
     */
    public function getStateMachineName(): string;

    /**
     * List of active processes used for this state machine.
     * The last one is used by default for new items if not specified otherwise.
     *
     * [
     *   'ProcessName',
     *   'ProcessName2 ,
     * ]
     *
     * @return string[]
     */
    public function getActiveProcesses(): array;

    /**
     * Provide initial state name for item when state machine initialized. Using process name.
     *
     * @param string $processName
     *
     * @return string
     */
    public function getInitialStateForProcess(string $processName): string;

    /**
     * This method is called when state of item was changed, client can create custom logic for example update it's related table with new stateId and processId.
     * ItemDto:identifier is id of entity from client.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function itemStateUpdated(ItemDto $itemDto): bool;

    /**
     * This method should return all list of ItemDto, with (identifier, IdStateMachineProcess, IdItemState)
     *
     * @param int[] $stateIds
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array;
}
