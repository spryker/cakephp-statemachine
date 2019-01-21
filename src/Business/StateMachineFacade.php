<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

/**
 * @method \StateMachine\Business\StateMachineBusinessFactory getFactory()
 */
class StateMachineFacade extends AbstractFacade implements StateMachineFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param int $identifier - this is id of foreign entity you want to track in state machine, it's stored in history table.
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(
        StateMachineProcessTransfer $stateMachineProcessTransfer,
        $identifier
    ) {
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
    public function triggerEvent($eventName, StateMachineItemTransfer $stateMachineItemTransfer)
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
    public function triggerEventForItems($eventName, array $stateMachineItems)
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
    public function getProcesses($stateMachineName)
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
    public function checkConditions($stateMachineName)
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
    public function stateMachineExists($stateMachineName)
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
    public function checkTimeouts($stateMachineName)
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
        $highlightState = null,
        $format = null,
        $fontSize = null
    ) {
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
    public function getStateMachineProcessId(StateMachineProcessTransfer $stateMachineProcessTransfer)
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
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer)
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
    public function getManualEventsForStateMachineItems(array $stateMachineItems)
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
    public function getProcessedStateMachineItemTransfer(StateMachineItemTransfer $stateMachineItemTransfer)
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
    public function getProcessedStateMachineItems(array $stateMachineItems)
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
     * @param int $identifier
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getStateHistoryByStateItemIdentifier($idStateMachineProcess, $identifier)
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
    public function getItemsWithFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flagName)
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
    public function getItemsWithoutFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flagName)
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
    public function clearLocks()
    {
        $this->getFactory()->createItemLock()->clearLocks();
    }
}
