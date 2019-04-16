<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business;

use StateMachine\FactoryTrait;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class StateMachineFacade implements StateMachineFacadeInterface
{
    use FactoryTrait;

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $identifier - this is id of foreign entity you want to track in state machine, it's stored in history table.
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(
        StateMachineProcessTransfer $stateMachineProcessTransfer,
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return int
     */
    public function triggerEvent(string $eventName, StateMachineItemTransfer $stateMachineItemTransfer): int
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
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
     * @return \StateMachine\Transfer\StateMachineProcessTransfer[]
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
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string|null $highlightState
     * @param string|null $format
     * @param int|null $fontSize
     *
     * @return string
     */
    public function drawProcess(
        StateMachineProcessTransfer $stateMachineProcessTransfer,
        ?string $highlightState = null,
        ?string $format = null,
        ?int $fontSize = null
    ): string {
        $process = $this->getFactory()
            ->createStateMachineBuilder()
            ->createProcess($stateMachineProcessTransfer);

        return $this->getFactory()
            ->createGraphDrawer(
                $stateMachineProcessTransfer->getStateMachineName()
            )->draw($process, $highlightState, $format, $fontSize);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return int
     */
    public function getStateMachineProcessId(StateMachineProcessTransfer $stateMachineProcessTransfer): int
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return array
     */
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer): array
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return array
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    public function getProcessedStateMachineItemTransfer(StateMachineItemTransfer $stateMachineItemTransfer): StateMachineItemTransfer
    {
        return $this->getFactory()
            ->createStateMachinePersistence()
            ->getProcessedStateMachineItemTransfer($stateMachineItemTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
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
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
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
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flagName
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getItemsWithFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, string $flagName): array
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
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flagName
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getItemsWithoutFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, string $flagName): array
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
