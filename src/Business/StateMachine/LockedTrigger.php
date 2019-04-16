<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Transfer\StateMachineProcessTransfer;

class LockedTrigger implements TriggerInterface
{
    /**
     * @var \StateMachine\Business\Lock\ItemLockInterface
     */
    protected $itemLock;

    /**
     * @var \StateMachine\Business\StateMachine\TriggerInterface
     */
    protected $stateMachineTrigger;

    /**
     * @param \StateMachine\Business\StateMachine\TriggerInterface $stateMachineTrigger
     * @param \StateMachine\Business\Lock\ItemLockInterface $itemLock
     */
    public function __construct(TriggerInterface $stateMachineTrigger, ItemLockInterface $itemLock)
    {
        $this->itemLock = $itemLock;
        $this->stateMachineTrigger = $stateMachineTrigger;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $identifier
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(StateMachineProcessTransfer $stateMachineProcessTransfer, string $identifier): int
    {
        $lockIdentifier = $this->buildLockIdentifier(
            $identifier,
            $stateMachineProcessTransfer->getStateMachineName(),
            $stateMachineProcessTransfer->getProcessName()
        );

        $lockIdentifier = $this->hashIdentifier($lockIdentifier);

        $this->itemLock->acquire($lockIdentifier);

        try {
            $triggerResult = $this->stateMachineTrigger->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);
        } finally {
            $this->itemLock->release($lockIdentifier);
        }

        return $triggerResult;
    }

    /**
     * @param string $eventName
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return int
     */
    public function triggerEvent(string $eventName, array $stateMachineItems): int
    {
        $identifier = $this->buildIdentifierForMultipleItemLock($stateMachineItems);

        $this->itemLock->acquire($identifier);

        try {
            $triggerEventResult = $this->stateMachineTrigger->triggerEvent($eventName, $stateMachineItems);
        } finally {
            $this->itemLock->release($identifier);
        }

        return $triggerEventResult;
    }

    /**
     * @param string $stateMachineName
     *
     * @return int
     */
    public function triggerConditionsWithoutEvent(string $stateMachineName): int
    {
        return $this->stateMachineTrigger->triggerConditionsWithoutEvent($stateMachineName);
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return string
     */
    protected function buildIdentifierForMultipleItemLock(array $stateMachineItems): string
    {
        $identifier = '';
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            if ($identifier) {
                $identifier .= '-';
            }
            $identifier .= $this->buildLockIdentifier(
                $stateMachineItemTransfer->getIdentifier(),
                $stateMachineItemTransfer->getProcessName(),
                $stateMachineItemTransfer->getStateMachineName()
            );
        }

        return $this->hashIdentifier($identifier);
    }

    /**
     * @param string $identifier
     *
     * @return string
     */
    protected function hashIdentifier(string $identifier): string
    {
        return hash('sha512', $identifier);
    }

    /**
     * @param string $stateMachineName
     *
     * @return int
     */
    public function triggerForTimeoutExpiredItems(string $stateMachineName): int
    {
        return $this->stateMachineTrigger->triggerForTimeoutExpiredItems($stateMachineName);
    }

    /**
     * @param string $identifier
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return string
     */
    protected function buildLockIdentifier(string $identifier, string $stateMachineName, string $processName): string
    {
        return $identifier . $stateMachineName . $processName;
    }
}
