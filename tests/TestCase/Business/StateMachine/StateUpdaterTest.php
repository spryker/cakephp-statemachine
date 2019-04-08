<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Process\Process;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdater;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Model\QueryContainer;
use StateMachine\Transfer\StateMachineItemTransfer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group StateUpdaterTest
 * Add your own group annotations below this line
 */
class StateUpdaterTest extends TestCase
{
    public const TEST_STATE_MACHINE_NAME = 'test state machine name';

    /**
     * @return void
     */
    public function testStateUpdaterShouldUpdateStateInTransaction()
    {
        $stateUpdater = $this->createStateUpdater();

        $stateUpdater->updateStateMachineItemState(
            [$this->createStateMachineItems()[0]],
            $this->createProcesses(),
            $this->createSourceStateBuffer()
        );
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldTriggerHandlerWhenStateChanged()
    {
        $stateMachineHandlerResolverMock = $this->createHandlerResolverMock();

        $handlerMock = $this->createStateMachineHandlerMock();
        $handlerMock->expects($this->once())
            ->method('itemStateUpdated')
            ->with($this->isInstanceOf(StateMachineItemTransfer::class));

        $stateMachineHandlerResolverMock->method('get')->willReturn($handlerMock);

        $stateUpdater = $this->createStateUpdater(
            null,
            $stateMachineHandlerResolverMock
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer()
        );
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldUpdateTimeoutsWhenStateChanged()
    {
        $timeoutMock = $this->createTimeoutMock();

        $timeoutMock->expects($this->once())->method('dropOldTimeout');
        $timeoutMock->expects($this->once())->method('setNewTimeout');

        $stateUpdater = $this->createStateUpdater(
            $timeoutMock
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer()
        );
    }

    /**
     * @return void
     */
    public function testStateMachineUpdaterShouldPersistStateHistory()
    {
        $persistenceMock = $this->createPersistenceMock();
        $persistenceMock->expects($this->once())->method('saveItemStateHistory')->with(
            $this->isInstanceOf(StateMachineItemTransfer::class)
        );

        $stateUpdater = $this->createStateUpdater(
            null,
            null,
            $persistenceMock
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer()
        );
    }

    /**
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    protected function createStateMachineItems()
    {
        $items = [];

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName('Test');
        $stateMachineItemTransfer->setIdentifier(1);
        $stateMachineItemTransfer->setStateName('target');
        $stateMachineItemTransfer->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $items[] = $stateMachineItemTransfer;

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName('Test');
        $stateMachineItemTransfer->setIdentifier(2);
        $stateMachineItemTransfer->setStateName('target');
        $stateMachineItemTransfer->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $items[] = $stateMachineItemTransfer;

        return $items;
    }

    /**
     * @return \StateMachine\Business\Process\Process[]
     */
    protected function createProcesses()
    {
        $processes = [];

        $process = new Process();

        $processes['Test'] = $process;

        return $processes;
    }

    /**
     * @return \StateMachine\Business\Process\State[]
     */
    protected function createSourceStateBuffer()
    {
        $sourceStates = [];

        $sourceStates[1] = 'target';
        $sourceStates[2] = 'updated';

        return $sourceStates;
    }

    /**
     * @param \StateMachine\Business\StateMachine\TimeoutInterface|null $timeoutMock
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface|null $handlerResolverMock
     * @param \StateMachine\Business\StateMachine\PersistenceInterface|null $stateMachinePersistenceMock
     *
     * @return \StateMachine\Business\StateMachine\StateUpdater
     */
    protected function createStateUpdater(
        ?TimeoutInterface $timeoutMock = null,
        ?HandlerResolverInterface $handlerResolverMock = null,
        ?PersistenceInterface $stateMachinePersistenceMock = null
    ) {

        if ($timeoutMock === null) {
            $timeoutMock = $this->createTimeoutMock();
        }

        if ($handlerResolverMock === null) {
            $handlerResolverMock = $this->createHandlerResolverMock();

            $handlerMock = $this->createStateMachineHandlerMock();
            $handlerResolverMock->method('get')->willReturn($handlerMock);
        }

        if ($stateMachinePersistenceMock === null) {
            $stateMachinePersistenceMock = $this->createPersistenceMock();
        }

        $queryContainerMock = $this->createQueryContainer();

        return new StateUpdater(
            $timeoutMock,
            $handlerResolverMock,
            $stateMachinePersistenceMock,
            $queryContainerMock
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Model\QueryContainerInterface
     */
    protected function createQueryContainer()
    {
        return new QueryContainer();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createHandlerResolverMock()
    {
        $handlerResolverMock = $this->getMockBuilder(HandlerResolverInterface::class)->getMock();

        return $handlerResolverMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistenceMock()
    {
        $persistenceMock = $this->getMockBuilder(PersistenceInterface::class)->getMock();

        return $persistenceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected function createStateMachineHandlerMock()
    {
        $stateMachineHandlerMock = $this->getMockBuilder(StateMachineHandlerInterface::class)->getMock();

        return $stateMachineHandlerMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected function createTimeoutMock()
    {
        $timeoutMock = $this->getMockBuilder(TimeoutInterface::class)->getMock();

        return $timeoutMock;
    }
}
