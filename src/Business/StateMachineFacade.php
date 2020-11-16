<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business;

use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\FactoryTrait;

class StateMachineFacade implements StateMachineFacadeInterface
{
    use FactoryTrait;

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param int $identifier - this is id of foreign entity you want to track in state machine, it's stored in history table.
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(
        ProcessDto $processDto,
        int $identifier
    ): int {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerForNewStateMachineItem($processDto, $identifier);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return int
     */
    public function triggerEvent(string $eventName, ItemDto $itemDto): int
    {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerEvent($eventName, [$itemDto]);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return int
     */
    public function triggerEventForItems(string $eventName, array $stateMachineItems): int
    {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerEvent($eventName, $stateMachineItems);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto[]
     */
    public function getProcesses(string $stateMachineName): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getProcesses($stateMachineName);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $stateMachineName
     *
     * @return int
     */
    public function checkConditions(string $stateMachineName): int
    {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerConditionsWithoutEvent($stateMachineName);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $stateMachineName
     *
     * @return bool
     */
    public function stateMachineExists(string $stateMachineName): bool
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->hasHandler($stateMachineName);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $stateMachineName
     *
     * @return int
     */
    public function checkTimeouts(string $stateMachineName): int
    {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerForTimeoutExpiredItems($stateMachineName);
    }

    /**
     * {@inheritDoc}
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
    ): string {
        $process = $this->getFactory()
            ->createStateMachineBuilder()
            ->createProcess($processDto);

        return $this->getFactory()
            ->createGraphDrawer(
                $processDto->getStateMachineNameOrFail()
            )->draw($process, $highlightState, $format, $fontSize);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return int
     */
    public function getStateMachineProcessId(ProcessDto $processDto): int
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getProcessId($processDto);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return string[]
     */
    public function getManualEventsForStateMachineItem(ItemDto $itemDto): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getManualEventsForStateMachineItem($itemDto);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string[][]
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getManualEventsForStateMachineItems($stateMachineItems);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getProcessedItemDto(ItemDto $itemDto): ItemDto
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getProcessedItemDto($itemDto);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getProcessedStateMachineItems(array $stateMachineItems): array
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getProcessedStateMachineItems($stateMachineItems);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idStateMachineProcess
     * @param int $identifier
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateHistoryByStateItemIdentifier(int $idStateMachineProcess, int $identifier): array
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getStateHistoryByStateItemIdentifier($identifier, $idStateMachineProcess);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flagName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getItemsWithFlag(ProcessDto $processDto, string $flagName): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getItemsWithFlag($processDto, $flagName);
    }

    /**
     * {@inheritDoc}
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flagName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getItemsWithoutFlag(ProcessDto $processDto, string $flagName): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getItemsWithoutFlag($processDto, $flagName);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function clearLocks(): void
    {
        $this->getFactory()->createItemLock()->clearLocks();
    }
}
