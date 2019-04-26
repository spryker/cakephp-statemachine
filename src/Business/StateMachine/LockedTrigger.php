<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use RuntimeException;
use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ProcessDto;

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
     * @var \StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @param \StateMachine\Business\StateMachine\TriggerInterface $stateMachineTrigger
     * @param \StateMachine\Business\Lock\ItemLockInterface $itemLock
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     */
    public function __construct(
        TriggerInterface $stateMachineTrigger,
        ItemLockInterface $itemLock,
        HandlerResolverInterface $stateMachineHandlerResolver
    ) {
        $this->itemLock = $itemLock;
        $this->stateMachineTrigger = $stateMachineTrigger;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param int $identifier
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(ProcessDto $processDto, int $identifier): int
    {
        $processName = $processDto->getProcessName() ?: $this->getCurrentProcess(
            $this->getStateMachineHandler($processDto->getStateMachineNameOrFail())->getActiveProcesses()
        );
        $processDto->setProcessName($processName);

        $lockIdentifier = $this->buildLockIdentifier(
            $identifier,
            $processDto->getStateMachineNameOrFail(),
            $processName
        );

        $lockHash = $this->hashIdentifier($lockIdentifier);

        $this->itemLock->acquire($lockHash);

        try {
            $triggerResult = $this->stateMachineTrigger->triggerForNewStateMachineItem($processDto, $identifier);
        } finally {
            $this->itemLock->release($lockHash);
        }

        return $triggerResult;
    }

    /**
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
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
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string
     */
    protected function buildIdentifierForMultipleItemLock(array $stateMachineItems): string
    {
        $identifier = '';
        foreach ($stateMachineItems as $itemDto) {
            if ($identifier) {
                $identifier .= '-';
            }
            $identifier .= $this->buildLockIdentifier(
                $itemDto->getIdentifierOrFail(),
                $itemDto->getProcessNameOrFail(),
                $itemDto->getStateMachineNameOrFail()
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
     * @param int $identifier
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return string
     */
    protected function buildLockIdentifier(int $identifier, string $stateMachineName, string $processName): string
    {
        return $identifier . $stateMachineName . $processName;
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected function getStateMachineHandler(string $stateMachineName): StateMachineHandlerInterface
    {
        return $this->stateMachineHandlerResolver
            ->get($stateMachineName);
    }

    /**
     * @param string[] $processes
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function getCurrentProcess(array $processes): string
    {
        $process = array_pop($processes);
        if (!$process) {
            throw new RuntimeException('No active processes');
        }

        return $process;
    }
}
