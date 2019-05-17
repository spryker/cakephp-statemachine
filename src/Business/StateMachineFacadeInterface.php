<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business;

use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;

interface StateMachineFacadeInterface
{
    /**
     * Specification:
     * - Must be triggered once per state machine when first item is added.
     * - Creates new process item in persistent storage if it does not exist.
     * - Creates new state item in persistent storage if it does not exist.
     * - Executes registered StateMachineHandlerInterface::getInitialStateForProcess() plugin.
     * - Executes registered StateMachineHandlerInterface::itemStateUpdated() plugin methods on state change.
     * - Persists state item history.
     * - Returns with the number of transitioned items.
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param int $identifier - this is id of foreign entity you want to track in state machine, it's stored in history table.
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(ProcessDto $processDto, int $identifier): int;

    /**
     * Specification:
     * - State machine must be already initialized with StateMachineFacadeInterface::triggerForNewStateMachineItem().
     * - Triggers event for the provided item.
     * - Creates new state item in persistent storage if it does not exist.
     * - Executes registered StateMachineHandlerInterface::itemStateUpdated() plugin methods on state change.
     * - Persists state item history.
     * - Returns with the number of transitioned items.
     *
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return int
     */
    public function triggerEvent(string $eventName, ItemDto $itemDto): int;

    /**
     * Specification:
     * - State machine must be already initialized with StateMachineFacadeInterface::triggerForNewStateMachineItem().
     * - Triggers event for the provided items.
     * - Creates new state item in persistent storage if it does not exist.
     * - Executes registered StateMachineHandlerInterface::itemStateUpdated() plugin methods on state change.
     * - Persists state item history.
     * - Returns with the number of transitioned items.
     *
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return int
     */
    public function triggerEventForItems(string $eventName, array $stateMachineItems): int;

    /**
     * Specification:
     * - Finds state machine handler by provided state machine name.
     * - Retrieves active process transfer list defined in handler by process name.
     *
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto[]
     */
    public function getProcesses(string $stateMachineName): array;

    /**
     * Specification:
     * - Checks if state machine exists.
     *
     * @param string $stateMachineName
     *
     * @return bool
     */
    public function stateMachineExists(string $stateMachineName): bool;

    /**
     * Specification:
     * - Gathers all transitions without any event for the provided state machine.
     * - Executes gathered transitions.
     *
     * @param string $stateMachineName
     *
     * @return int
     */
    public function checkConditions(string $stateMachineName): int;

    /**
     * Specification:
     * - Gathers all timeout expired events for the provided state machine.
     * - Executes gathered events.
     *
     * @param string $stateMachineName
     *
     * @return int
     */
    public function checkTimeouts(string $stateMachineName): int;

    /**
     * Specification:
     * - Loads state machine process from XML.
     * - Draws graph using graph library.
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string|null $highlightState
     * @param string|null $format
     * @param int|null $fontSize
     *
     * @return string
     */
    public function drawProcess(
        ProcessDto $processDto,
        ?string $highlightState = null,
        ?string $format = null,
        ?int $fontSize = null
    ): string;

    /**
     * Specification:
     * - Retrieves process id by provided process name.
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return int
     */
    public function getStateMachineProcessId(ProcessDto $processDto): int;

    /**
     * Specification:
     * - Loads state machine process from XML using provided state machine item.
     * - Retrieves manual event list.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return string[]
     */
    public function getManualEventsForStateMachineItem(ItemDto $itemDto): array;

    /**
     * Specification:
     * - Loads state machine process from XML using provided state machine item.
     * - Retrieves manual event list per items identifier.
     * - Items without any manual events are not part of result.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string[][]
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems): array;

    /**
     * Specification:
     * - Retrieves hydrated item transfer by provided item id and identifier pair.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getProcessedItemDto(ItemDto $itemDto): ItemDto;

    /**
     * Specification:
     * - Retrieves hydrated item transfers by provided item id and identifier pairs.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getProcessedStateMachineItems(array $stateMachineItems): array;

    /**
     * Specification:
     * - Retrieves state item history by state item identifier.
     *
     * @param int $idStateMachineProcess
     * @param int $identifier
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateHistoryByStateItemIdentifier(int $idStateMachineProcess, int $identifier): array;

    /**
     * Specification:
     * - Loads state machine process from XML.
     * - Retrieves all items with state which have the provided flag.
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flagName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getItemsWithFlag(ProcessDto $processDto, string $flagName): array;

    /**
     * Specification:
     * - Loads state machine process from XML.
     * - Retrieves all items with state which have do not have the provided flag.
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flagName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getItemsWithoutFlag(ProcessDto $processDto, string $flagName): array;

    /**
     * Specification:
     * - Clears all expired item locks.
     *
     * @return void
     */
    public function clearLocks(): void;
}
