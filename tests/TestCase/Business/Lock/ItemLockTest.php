<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Lock;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Lock\ItemLock;
use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineLocksTable;
use StateMachine\StateMachineConfig;

class ItemLockTest extends TestCase
{
    /**
     * @var \StateMachine\Model\Table\StateMachineLocksTable
     */
    protected $StateMachineLocks;

    /**
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineLocks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineLocks') ? [] : ['className' => StateMachineLocksTable::class];
        $this->StateMachineLocks = TableRegistry::getTableLocator()->get('StateMachineLocks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineLocks);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testAcquireLockShouldCreateItemWithLockInPersistence(): void
    {
        $itemLock = $this->createItemLock();

        $lockResult = $itemLock->acquire($this->createIdentifier());

        $this->assertTrue($lockResult);
    }

    /**
     * @return void
     */
    public function testReleaseLockShouldDeleteLockFromDatabase(): void
    {
        $itemLock = $this->createItemLock();

        $itemLock->release($this->createIdentifier());

        $stateMachineItemLock = $this->createStateMachineQueryContainer()->queryLockItemsByIdentifier($this->createIdentifier())->first();

        $this->assertNull($stateMachineItemLock);
    }

    /**
     * @return \StateMachine\Business\Lock\ItemLockInterface
     */
    protected function createItemLock(): ItemLockInterface
    {
        return new ItemLock($this->createStateMachineQueryContainer(), $this->createStateMachineConfigMock(), $this->StateMachineLocks);
    }

    /**
     * @return string
     */
    protected function createIdentifier(): string
    {
        return sha1(1);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\StateMachineConfig
     */
    protected function createStateMachineConfigMock()
    {
        $stateMachineConfigMock = $this->getMockBuilder(StateMachineConfig::class)->getMock();

        return $stateMachineConfigMock;
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    protected function createStateMachineQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }
}
