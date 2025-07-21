<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Process\Process;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\Persistence;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdater;
use StateMachine\Business\StateMachine\StateUpdaterInterface;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateLogsTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;

class StateUpdaterTest extends TestCase
{
    /**
     * @var string
     */
    protected const TEST_STATE_MACHINE_NAME = 'test state machine name';

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStateLogsTable
     */
    protected $StateMachineItemStateLogs;

    /**
     * @var \StateMachine\Model\Table\StateMachineProcessesTable
     */
    protected $StateMachineProcesses;

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStatesTable
     */
    protected $StateMachineItemStates;

    /**
     * @var \StateMachine\Model\Table\StateMachineTimeoutsTable
     */
    protected $StateMachineTimeouts;

    /**
     * @var array
     */
    protected array $fixtures = [
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTimeouts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStateLogs') ? [] : ['className' => StateMachineItemStateLogsTable::class];
        $this->StateMachineItemStateLogs = TableRegistry::getTableLocator()->get('StateMachineItemStateLogs', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineProcesses') ? [] : ['className' => StateMachineProcessesTable::class];
        $this->StateMachineProcesses = TableRegistry::getTableLocator()->get('StateMachineProcesses', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStates') ? [] : ['className' => StateMachineItemStatesTable::class];
        $this->StateMachineItemStates = TableRegistry::getTableLocator()->get('StateMachineItemStates', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineTimeouts') ? [] : ['className' => StateMachineTimeoutsTable::class];
        $this->StateMachineTimeouts = TableRegistry::getTableLocator()->get('StateMachineTimeouts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStateLogs, $this->StateMachineProcesses, $this->StateMachineItemStates, $this->StateMachineTimeouts);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldUpdateStateSameWithoutLog(): void
    {
        $stateUpdater = $this->createStateUpdater();

        $stateMachineItemStateLogsCount = $this->StateMachineItemStateLogs->find()->count();
        $this->assertSame(1, $stateMachineItemStateLogsCount);

        $stateUpdater->updateStateMachineItemState(
            [$this->createStateMachineItems()[0]],
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );

        $stateMachineItemStateLogsCount = $this->StateMachineItemStateLogs->find()->count();
        $this->assertSame(1, $stateMachineItemStateLogsCount);
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldTriggerHandlerWhenStateChanged(): void
    {
        $stateMachineHandlerResolverMock = $this->createHandlerResolverMock();

        $handlerMock = $this->createStateMachineHandlerMock();
        $handlerMock->expects($this->once())
            ->method('itemStateUpdated')
            ->with($this->isInstanceOf(ItemDto::class));

        $stateMachineHandlerResolverMock->method('get')->willReturn($handlerMock);

        $stateUpdater = $this->createStateUpdater(
            null,
            $stateMachineHandlerResolverMock,
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldUpdateTimeoutsWhenStateChanged(): void
    {
        $timeoutMock = $this->createTimeoutMock();

        $timeoutMock->expects($this->once())->method('dropOldTimeout');
        $timeoutMock->expects($this->once())->method('setNewTimeout');

        $stateUpdater = $this->createStateUpdater(
            $timeoutMock,
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );
    }

    /**
     * @return void
     */
    public function testStateMachineUpdaterShouldPersistStateHistory(): void
    {
        $stateUpdater = $this->createStateUpdater();

        $stateMachineItemStateLogsCount = $this->StateMachineItemStateLogs->find()->count();
        $this->assertSame(1, $stateMachineItemStateLogsCount);

        $stateUpdater->updateStateMachineItemState(
            [$this->createStateMachineItems()[1]],
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );

        $stateMachineItemStateLogsCount = $this->StateMachineItemStateLogs->find()->count();
        $this->assertSame(2, $stateMachineItemStateLogsCount);
    }

    /**
     * @return array<\StateMachine\Dto\StateMachine\ItemDto>
     */
    protected function createStateMachineItemsSame(): array
    {
        $items = [];

        $itemDto = new ItemDto();
        $itemDto->setProcessName('Test');
        $itemDto->setIdentifier('1');
        $itemDto->setStateName('source');
        $itemDto->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $items[] = $itemDto;

        $itemDto = new ItemDto();
        $itemDto->setProcessName('Test');
        $itemDto->setIdentifier('2');
        $itemDto->setStateName('source');
        $itemDto->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $items[] = $itemDto;

        return $items;
    }

    /**
     * @return array<\StateMachine\Dto\StateMachine\ItemDto>
     */
    protected function createStateMachineItems(): array
    {
        $items = [];

        $StateMachineItemStates = $this->getTableLocator()->get('StateMachine.StateMachineItemStates');
        $data = [
            'state_machine_process_id' => 1,
            'name' => 'x',
            'description' => 'Lorem ipsum dolor sit amet',
        ];
        $stateMachineItemState = $StateMachineItemStates->newEntity($data);
        $StateMachineItemStates->saveOrFail($stateMachineItemState);

        $itemDto = new ItemDto();
        $itemDto->setProcessName('Test');
        $itemDto->setIdentifier('1');
        $itemDto->setStateName('source');
        $itemDto->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $itemDto->setIdItemState(1);
        $items[] = $itemDto;

        $data = [
            'state_machine_process_id' => 1,
            'name' => 'y',
            'description' => 'Lorem ipsum dolor sit amet',
        ];
        $stateMachineItemState = $StateMachineItemStates->newEntity($data);
        $StateMachineItemStates->saveOrFail($stateMachineItemState);

        $itemDto = new ItemDto();
        $itemDto->setProcessName('Test');
        $itemDto->setIdentifier('2');
        $itemDto->setStateName('target');
        $itemDto->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $itemDto->setIdItemState(2);
        $items[] = $itemDto;

        return $items;
    }

    /**
     * @return array<\StateMachine\Business\Process\Process>
     */
    protected function createProcesses(): array
    {
        $processes = [];

        $process = new Process();

        $processes['Test'] = $process;

        return $processes;
    }

    /**
     * @return array<string>
     */
    protected function createSourceStateBuffer(): array
    {
        $sourceStates = [];

        $sourceStates['1'] = 'source';
        $sourceStates['2'] = 'source';

        return $sourceStates;
    }

    /**
     * @param \StateMachine\Business\StateMachine\TimeoutInterface|null $timeout
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface|null $handlerResolver
     *
     * @return \StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    protected function createStateUpdater(
        ?TimeoutInterface $timeout = null,
        ?HandlerResolverInterface $handlerResolver = null
    ): StateUpdaterInterface {
        if ($timeout === null) {
            $timeout = $this->createTimeoutMock();
        }

        if ($handlerResolver === null) {
            $handlerResolver = $this->createHandlerResolverMock();

            $handlerMock = $this->createStateMachineHandlerMock();
            $handlerResolver->method('get')->willReturn($handlerMock);
        }

        return new StateUpdater(
            $timeout,
            $handlerResolver,
            $this->createPersistence(),
            $this->createQueryContainer(),
        );
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    protected function createQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createHandlerResolverMock(): HandlerResolverInterface
    {
        $handlerResolverMock = $this->getMockBuilder(HandlerResolverInterface::class)->getMock();

        return $handlerResolverMock;
    }

    /**
     * @return \StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistence(): PersistenceInterface
    {
        return new Persistence(
            $this->createQueryContainer(),
            $this->StateMachineItemStateLogs,
            $this->StateMachineProcesses,
            $this->StateMachineItemStates,
            $this->StateMachineTimeouts,
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected function createStateMachineHandlerMock(): StateMachineHandlerInterface
    {
        $stateMachineHandlerMock = $this->getMockBuilder(StateMachineHandlerInterface::class)->getMock();

        return $stateMachineHandlerMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected function createTimeoutMock(): TimeoutInterface
    {
        $timeoutMock = $this->getMockBuilder(TimeoutInterface::class)->getMock();

        return $timeoutMock;
    }
}
