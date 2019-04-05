<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistory;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineProcessQuery;
use Propel\Runtime\Collection\ObjectCollection;
use StateMachine\Business\Process\State;
use StateMachine\Business\StateMachine\BuilderInterface;
use StateMachine\Business\StateMachine\Finder;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Test\TestCase\Mocks\StateMachineMocks;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group FinderTest
 * Add your own group annotations below this line
 */
class FinderTest extends StateMachineMocks
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

        $stateMachineQueryContainerMock = $this->createStateMachineQueryContainerMock();

        $stateMachineProcessQuery = $this->getMockBuilder(SpyStateMachineProcessQuery::class)->getMock();
        $stateMachineProcessQuery->method('findOne')->willReturn(new SpyStateMachineProcess());

        $stateMachineQueryContainerMock->expects($this->once())
            ->method('queryProcessByStateMachineAndProcessName')
            ->willReturn($stateMachineProcessQuery);

        $stateMachineItemStateQuery = $this->getMockBuilder(SpyStateMachineItemStateQuery::class)->getMock();

        $stateMachineItemEntity = new SpyStateMachineItemState();
        $stateMachineItemEntity->setIdStateMachineItemState(1);
        $stateMachineItemEntity->setFkStateMachineProcess(1);
        $stateMachineItemEntity->setName('State');

        $itemStateHistory = new SpyStateMachineItemStateHistory();

        $stateHistories = new ObjectCollection();
        $stateHistories->append($itemStateHistory);

        $stateMachineItemEntity->setStateHistories($stateHistories);

        $stateMachineItemStateQuery->method('find')->willReturn([$stateMachineItemEntity]);

        $stateMachineQueryContainerMock->expects($this->once())
            ->method('queryItemsByIdStateMachineProcessAndItemStates')
            ->willReturn($stateMachineItemStateQuery);

        $finder = $this->createFinder(null, $builderMock, $stateMachineQueryContainerMock);

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
            $stateMachineQueryContainerMock = $this->createStateMachineQueryContainerMock();
        }

        return new Finder(
            $builderMock,
            $handlerResolverMock,
            $stateMachineQueryContainerMock
        );
    }
}
