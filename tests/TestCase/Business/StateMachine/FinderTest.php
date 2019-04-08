<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\StateMachine\BuilderInterface;
use StateMachine\Business\StateMachine\Finder;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Model\Entity\StateMachineItemState;
use StateMachine\Model\Entity\StateMachineItemStateHistory;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class FinderTest extends TestCase
{
    public const TEST_STATE_MACHINE_NAME = 'TestStateMachine';

    /**
     * @return void
     */
    public function testGetActiveProcessShouldReturnProcessesRegisteredByHandler()
    {
        $statemachineHandlerMock = $this->createStateMachineHandlerMock();
        $statemachineHandlerMock->expects($this->once())
            ->method('getActiveProcesses')
            ->willReturn(['Process1', 'Process2']);

        $handlerResolverMock = $this->createHandlerResolverMock();
        $handlerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($statemachineHandlerMock);

        $finder = $this->createFinder($handlerResolverMock);

        $subProcesses = $finder->getProcesses(static::TEST_STATE_MACHINE_NAME);

        $this->assertCount(2, $subProcesses);

        $subProcess = array_pop($subProcesses);
        $this->assertInstanceOf(StateMachineProcessTransfer::class, $subProcess);
        $this->assertEquals(static::TEST_STATE_MACHINE_NAME, $subProcess->getStateMachineName());
        $this->assertEquals('Process2', $subProcess->getProcessName());
    }

    /**
     * @uses ProcessInterface::getManuallyExecutableEventsBySource()
     *
     * @return void
     */
    public function testGetManualEventsForStateMachineItemsShouldReturnManualEventsForGivenItems()
    {
        $manualEvents = [
           'state name' => [
               'event1',
               'event2',
           ],
        ];

        $processMock = $this->createProcessMock();
        $processMock->method('getManuallyExecutableEventsBySource')->willReturn($manualEvents);

        $builderMock = $this->createBuilderMock();
        $builderMock->method('createProcess')->willReturn($processMock);

        $finder = $this->createFinder(null, $builderMock);

        $stateMachineItems = [];

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName('Process1');
        $stateMachineItemTransfer->setStateName('state name');

        $stateMachineItems[] = $stateMachineItemTransfer;

        $manualEvents = $finder->getManualEventsForStateMachineItems($stateMachineItems);

        $this->assertCount(1, $manualEvents);
    }

    /**
     * @return void
     */
    public function testGetItemWithFlagShouldReturnStatesMarkedWithGivenFlag()
    {
        $states = [];
        $state = new State();
        $state->addFlag('test');
        $states[] = $state;

        $state = new State();
        $state->addFlag('test2');
        $states[] = $state;

        $processMock = $this->createProcessMock();
        $processMock->expects($this->once())
            ->method('getAllStates')
            ->willReturn($states);

        $builderMock = $this->createBuilderMock();
        $builderMock->method('createProcess')->willReturn($processMock);

        $stateMachineQueryContainer = $this->createStateMachineQueryContainer();

        $stateMachineItemEntity = new StateMachineItemState();
        $stateMachineItemEntity->id = 1;
        $stateMachineItemEntity->state_machine_process_id = 1;
        $stateMachineItemEntity->name = 'State';

        $itemStateHistory = new StateMachineItemStateHistory();

        $stateHistories[] = $itemStateHistory;

        $stateMachineItemEntity->state_machine_item_state_history = $stateHistories;

        $finder = $this->createFinder(null, $builderMock, $stateMachineQueryContainer);

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName('Process1');
        $stateMachineProcessTransfer->setStateMachineName(static::TEST_STATE_MACHINE_NAME);

        $stateMachineItems = $finder->getItemsWithFlag($stateMachineProcessTransfer, 'test');

        $this->assertCount(1, $stateMachineItems);

        $stateMachineItem = $stateMachineItems[0];
        $this->assertInstanceOf(StateMachineItemTransfer::class, $stateMachineItem);
    }

    /**
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface|null $handlerResolverMock
     * @param \StateMachine\Business\StateMachine\BuilderInterface|null $builderMock
     * @param \StateMachine\Model\QueryContainerInterface|null $stateMachineQueryContainerMock
     *
     * @return \StateMachine\Business\StateMachine\Finder
     */
    protected function createFinder(
        ?HandlerResolverInterface $handlerResolverMock = null,
        ?BuilderInterface $builderMock = null,
        ?QueryContainerInterface $stateMachineQueryContainerMock = null
    ) {

        if ($builderMock === null) {
            $builderMock = $this->createBuilderMock();
        }

        if ($handlerResolverMock === null) {
            $handlerResolverMock = $this->createHandlerResolverMock();
        }

        if ($stateMachineQueryContainerMock === null) {
            $stateMachineQueryContainerMock = $this->createStateMachineQueryContainer();
        }

        return new Finder(
            $builderMock,
            $handlerResolverMock,
            $stateMachineQueryContainerMock
        );
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createHandlerResolverMock()
    {
        $handlerResolverMock = $this->getMockBuilder(HandlerResolverInterface::class)->getMock();

        return $handlerResolverMock;
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    protected function createStateMachineQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\BuilderInterface
     */
    public function createBuilderMock()
    {
        $builderMock = $this->getMockBuilder(BuilderInterface::class)->getMock();

        return $builderMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Process\ProcessInterface
     */
    protected function createProcessMock()
    {
        $processMock = $this->getMockBuilder(ProcessInterface::class)->getMock();

        return $processMock;
    }
}
