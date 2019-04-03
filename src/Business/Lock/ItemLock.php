<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Lock;

use Cake\ORM\Exception\PersistenceFailedException;
use DateInterval;
use DateTime;
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
     * @param \StateMachine\Model\Table\StateMachineLocksTable
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
    public function acquire($identifier)
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
    public function release($identifier)
    {
        $this->queryContainer
            ->queryLockItemsByIdentifier($identifier)
            ->delete();
    }

    /**
     * @return void
     */
    public function clearLocks()
    {
        $this->queryContainer
            ->queryLockedItemsByExpirationDate(new DateTime('now'))
            ->delete();
    }

    /**
     * @return \DateTime
     */
    protected function createExpirationDate()
    {
        $dateInterval = DateInterval::createFromDateString(
            $this->stateMachineConfig->getStateMachineItemLockExpirationInterval()
        );
        $expirationDate = new DateTime();
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
