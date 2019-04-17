<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business;

use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\FactoryTrait;

class StateMachineFacade implements StateMachineFacadeInterface
{
    use FactoryTrait;

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string $identifier - this is id of foreign entity you want to track in state machine, it's stored in history table.
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(
        ProcessDto $stateMachineProcessTransfer,
        string $identifier
    ): int {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return int
     */
    public function triggerEvent(string $eventName, ItemDto $stateMachineItemTransfer): int
    {
        return $this->getFactory()
            ->createLockedStateMachineTrigger()
            ->triggerEvent($eventName, [$stateMachineItemTransfer]);
    }

    /**
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string|null $highlightState
     * @param string|null $format
     * @param int|null $fontSize
     *
     * @return string
     */
    public function drawProcess(
        ProcessDto $stateMachineProcessTransfer,
        ?string $highlightState = null,
        ?string $format = null,
        ?int $fontSize = null
    ): string {
        $process = $this->getFactory()
            ->createStateMachineBuilder()
            ->createProcess($stateMachineProcessTransfer);

        return $this->getFactory()
            ->createGraphDrawer(
                $stateMachineProcessTransfer->getStateMachineNameOrFail()
            )->draw($process, $highlightState, $format, $fontSize);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     *
     * @return int
     */
    public function getStateMachineProcessId(ProcessDto $stateMachineProcessTransfer): int
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getProcessId($stateMachineProcessTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return array
     */
    public function getManualEventsForStateMachineItem(ItemDto $stateMachineItemTransfer): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getManualEventsForStateMachineItem($stateMachineItemTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getProcessedItemDto(ItemDto $stateMachineItemTransfer): ItemDto
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getProcessedItemDto($stateMachineItemTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
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
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idStateMachineProcess
     * @param string $identifier
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateHistoryByStateItemIdentifier(int $idStateMachineProcess, string $identifier): array
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getStateHistoryByStateItemIdentifier($identifier, $idStateMachineProcess);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string $flagName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getItemsWithFlag(ProcessDto $stateMachineProcessTransfer, string $flagName): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getItemsWithFlag($stateMachineProcessTransfer, $flagName);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string $flagName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getItemsWithoutFlag(ProcessDto $stateMachineProcessTransfer, string $flagName): array
    {
        return $this->getFactory()
            ->createStateMachineFinder()
            ->getItemsWithoutFlag($stateMachineProcessTransfer, $flagName);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return void
     */
    public function clearLocks(): void
    {
        $this->getFactory()->createItemLock()->clearLocks();
    }
}
