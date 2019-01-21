<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Lock;

use DateInterval;
use DateTime;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineLock;
use Propel\Runtime\Exception\PropelException;
use StateMachine\Business\Exception\LockException;
use StateMachine\Model\QueryContainerInterface;
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
     * @param \StateMachine\Model\QueryContainerInterface $queryContainer
     * @param \StateMachine\StateMachineConfig $stateMachineConfig
     */
    public function __construct(
        QueryContainerInterface $queryContainer,
        StateMachineConfig $stateMachineConfig
    ) {
        $this->queryContainer = $queryContainer;
        $this->stateMachineConfig = $stateMachineConfig;
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

        $stateMachineLockEntity->setIdentifier($identifier);
        $expirationDate = $this->createExpirationDate();
        $stateMachineLockEntity->setExpires($expirationDate);

        try {
            $affectedRows = $stateMachineLockEntity->save();
        } catch (PropelException $exception) {
            throw new LockException(
                sprintf(
                    'State machine trigger is locked. Propel exception: %s',
                    $exception->getMessage()
                ),
                $exception->getCode(),
                $exception
            );
        }

        return $affectedRows > 0;
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
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineLock
     */
    protected function createStateMachineLockEntity()
    {
        return new SpyStateMachineLock();
    }
}
