<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Lock;

use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use DateInterval;
use StateMachine\Business\Exception\LockException;
use StateMachine\Model\Entity\StateMachineLock;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineLocksTable;
use StateMachine\StateMachineConfig;

class ItemLock implements ItemLockInterface
{
    /**
     * @var \StateMachine\Model\QueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \StateMachine\StateMachineConfig
     */
    protected $stateMachineConfig;

    /**
     * @var \StateMachine\Model\Table\StateMachineLocksTable
     */
    protected $stateMachineLocksTable;

    /**
     * @param \StateMachine\Model\QueryContainerInterface $queryContainer
     * @param \StateMachine\StateMachineConfig $stateMachineConfig
     * @param \StateMachine\Model\Table\StateMachineLocksTable $stateMachineLocksTable
     */
    public function __construct(
        QueryContainerInterface $queryContainer,
        StateMachineConfig $stateMachineConfig,
        StateMachineLocksTable $stateMachineLocksTable
    ) {
        $this->queryContainer = $queryContainer;
        $this->stateMachineConfig = $stateMachineConfig;
        $this->stateMachineLocksTable = $stateMachineLocksTable;
    }

    /**
     * @param string $identifier
     *
     * @throws \StateMachine\Business\Exception\LockException
     *
     * @return bool
     */
    public function acquire(string $identifier): bool
    {
        $stateMachineLockEntity = $this->createStateMachineLockEntity();

        $stateMachineLockEntity->identifier = $identifier;
        $stateMachineLockEntity->expires = $this->createExpirationDate();

        try {
            $this->stateMachineLocksTable->saveOrFail($stateMachineLockEntity);
        } catch (PersistenceFailedException $exception) {
            throw new LockException(
                sprintf(
                    'State machine trigger is locked. DB exception: %s',
                    $exception->getMessage()
                ),
                $exception->getCode(),
                $exception
            );
        }

        return true;
    }

    /**
     * @param string $identifier
     *
     * @return void
     */
    public function release(string $identifier): void
    {
        $this->queryContainer
            ->queryLockItemsByIdentifier($identifier)
            ->delete()->execute();
    }

    /**
     * @return void
     */
    public function clearLocks(): void
    {
        $this->queryContainer
            ->queryLockedItemsByExpirationDate(new FrozenTime('now'))
            ->delete()->execute();
    }

    /**
     * @return \Cake\I18n\FrozenTime
     */
    protected function createExpirationDate(): FrozenTime
    {
        $dateInterval = DateInterval::createFromDateString(
            $this->stateMachineConfig->getStateMachineItemLockExpirationInterval()
        );
        $expirationDate = new FrozenTime();
        $expirationDate->add($dateInterval);

        return $expirationDate;
    }

    /**
     * @return \StateMachine\Model\Entity\StateMachineLock
     */
    protected function createStateMachineLockEntity(): StateMachineLock
    {
        return $this->stateMachineLocksTable->newEntity();
    }
}
