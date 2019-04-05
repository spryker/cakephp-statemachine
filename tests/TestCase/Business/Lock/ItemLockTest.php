<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Lock;

use Cake\ORM\Query;
use StateMachine\Business\Lock\ItemLock;
use StateMachine\Model\Entity\StateMachineLock;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineLocksTable;
use StateMachine\Test\TestCase\Mocks\StateMachineMocks;

class ItemLockTest extends StateMachineMocks
{
    /**
     * @return void
     */
    public function testAcquireLockShouldCreateItemWithLockInPersistence()
    {
        $stateMachineLockEntityMock = $this->createStateMachineItemLockEntityMock();
        $stateMachineLockTableMock = $this->createStateMachineLockTableMock();
        $stateMachineLockTableMock
            ->method('saveOrFail')
            ->willReturn($stateMachineLockEntityMock)
            ->method('newEntity')
            ->willReturn($stateMachineLockEntityMock);

        $itemLock = $this->createItemLock($stateMachineLockEntityMock);

        $lockResult = $itemLock->acquire($this->createIdentifier());

        $this->assertTrue($lockResult);
    }

    /**
     * @return void
     */
    public function testReleaseLockShouldDeleteLockFromDatabase()
    {
        $stateMachineQueryContainerMock = $this->createStateMachineQueryContainerMock();

        $itemLockQuery = $this->createStateMachineQueryMock();
        $itemLockQuery
            ->expects($this->once())
            ->method('delete');

        $stateMachineQueryContainerMock->expects($this->once())
            ->method('queryLockItemsByIdentifier')
            ->willReturn($itemLockQuery);

        $itemLock = $this->createItemLock(null, $stateMachineQueryContainerMock);

        $itemLock->release($this->createIdentifier());
    }

    /**
     * @param \StateMachine\Model\Entity\StateMachineLock|null $stateMachineLockEntityMock
     * @param \StateMachine\Model\QueryContainerInterface|null $stateMachineQueryContainerMock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Lock\ItemLockInterface
     */
    protected function createItemLock(
        ?StateMachineLock $stateMachineLockEntityMock = null,
        ?QueryContainerInterface $stateMachineQueryContainerMock = null
    ) {
        if ($stateMachineQueryContainerMock === null) {
            $stateMachineQueryContainerMock = $this->createStateMachineQueryContainerMock();
        }

        $stateMachineConfigMock = $this->createStateMachineConfigMock();
        $stateMachineLockTableMock = $this->createStateMachineLockTableMock();

        $itemLockPartialMock = $this->getMockBuilder(ItemLock::class)
            ->setMethods(['createStateMachineLockEntity'])
            ->setConstructorArgs([$stateMachineQueryContainerMock, $stateMachineConfigMock, $stateMachineLockTableMock])
            ->getMock();

        $itemLockPartialMock->method('createStateMachineLockEntity')->willReturn($stateMachineLockEntityMock);

        return $itemLockPartialMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Model\Entity\StateMachineLock
     */
    protected function createStateMachineItemLockEntityMock()
    {
        $stateMachineLockEntityMock = $this->getMockBuilder(StateMachineLock::class)->getMock();

        return $stateMachineLockEntityMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Model\Table\StateMachineLocksTable
     */
    protected function createStateMachineLockTableMock()
    {
        $stateMachineLockEntityMock = $this->getMockBuilder(StateMachineLocksTable::class)->getMock();

        return $stateMachineLockEntityMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Cake\ORM\Query
     */
    protected function createStateMachineQueryMock()
    {
        $query = $this->getMockBuilder(Query::class)->getMock();

        return $query;
    }

    /**
     * @return string
     */
    protected function createIdentifier(): string
    {
        return sha1(1);
    }
}
