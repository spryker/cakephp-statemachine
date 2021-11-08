<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\StateMachine\BuilderInterface;
use StateMachine\Business\StateMachine\Finder;
use StateMachine\Business\StateMachine\FinderInterface;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Test\Fixture\StateMachineItemStatesFixture;
use StateMachine\Test\Fixture\StateMachineProcessesFixture;

class FinderTest extends TestCase
{
    /**
     * @var \StateMachine\Model\Table\StateMachineProcessesTable
     */
    protected $StateMachineProcesses;

    /**
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineItems',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineProcesses') ? [] : ['className' => StateMachineProcessesTable::class];
        $this->StateMachineProcesses = TableRegistry::getTableLocator()->get('StateMachineProcesses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineProcesses);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testGetActiveProcessShouldReturnProcessesRegisteredByHandler(): void
    {
        $stateMachineHandlerMock = $this->createStateMachineHandlerMock();
        $stateMachineHandlerMock->expects($this->once())
            ->method('getActiveProcesses')
            ->willReturn([StateMachineProcessesFixture::PROCESS_NAME_1, StateMachineProcessesFixture::PROCESS_NAME_2]);

        $handlerResolverMock = $this->createHandlerResolverMock();
        $handlerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($stateMachineHandlerMock);

        $finder = $this->createFinder($handlerResolverMock);

        $subProcesses = $finder->getProcesses(StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME);

        $this->assertCount(2, $subProcesses);

        $subProcess = array_pop($subProcesses);
        $this->assertInstanceOf(ProcessDto::class, $subProcess);
        $this->assertSame(StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME, $subProcess->getStateMachineName());
        $this->assertSame(StateMachineProcessesFixture::PROCESS_NAME_2, $subProcess->getProcessName());
    }

    /**
     * @return void
     */
    public function testGetManualEventsForStateMachineItemsShouldReturnManualEventsForGivenItems(): void
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

        $itemDto = new ItemDto();
        $itemDto->setProcessName(StateMachineProcessesFixture::PROCESS_NAME_1);
        $itemDto->setIdentifier(1);
        $itemDto->setStateMachineName('Test');
        $itemDto->setStateName('state name');

        $stateMachineItems[] = $itemDto;

        $manualEvents = $finder->getManualEventsForStateMachineItems($stateMachineItems);

        $this->assertCount(1, $manualEvents);
    }

    /**
     * @return void
     */
    public function testGetItemWithFlagShouldReturnStatesMarkedWithGivenFlag(): void
    {
        $states = [
            $this->createState(StateMachineItemStatesFixture::DEFAULT_STATE_ITEM_NAME, 'test'),
            $this->createState('random', 'test2'),
        ];

        $processMock = $this->createProcessMock();
        $processMock->expects($this->once())
            ->method('getAllStates')
            ->willReturn($states);

        $builderMock = $this->createBuilderMock();
        $builderMock->method('createProcess')->willReturn($processMock);

        $stateMachineQueryContainer = $this->createStateMachineQueryContainer();

        $finder = $this->createFinder(null, $builderMock, $stateMachineQueryContainer);

        $processDto = new ProcessDto();
        $processDto->setProcessName(StateMachineProcessesFixture::PROCESS_NAME_1);
        $processDto->setStateMachineName(StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME);

        $stateMachineItems = $finder->getItemsWithFlag($processDto, 'test');

        $this->assertCount(1, $stateMachineItems);

        $stateMachineItem = $stateMachineItems[0];
        $this->assertInstanceOf(ItemDto::class, $stateMachineItem);
    }

    /**
     * @return void
     */
    public function testGetItemMatrix(): void
    {
        $states = [
            $this->createState(StateMachineItemStatesFixture::DEFAULT_STATE_ITEM_NAME, 'test'),
            $this->createState('random', 'test2'),
        ];

        $processMock = $this->createProcessMock();
        $builderMock = $this->createBuilderMock();
        $builderMock->method('createProcess')->willReturn($processMock);

        $stateMachineQueryContainer = $this->createStateMachineQueryContainer();

        $finder = $this->createFinder(null, $builderMock, $stateMachineQueryContainer);
        $stateMachineItems = $finder->getItemMatrix('Test');

        $this->assertSame([], $stateMachineItems);
    }

    /**
     * @param string $name
     * @param string $flag
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function createState(string $name, string $flag): StateInterface
    {
        $state = new State();
        $state->setName($name);
        $state->addFlag($flag);

        return $state;
    }

    /**
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface|null $handlerResolverMock
     * @param \StateMachine\Business\StateMachine\BuilderInterface|null $builderMock
     * @param \StateMachine\Model\QueryContainerInterface|null $stateMachineQueryContainer
     *
     * @return \StateMachine\Business\StateMachine\FinderInterface
     */
    protected function createFinder(
        ?HandlerResolverInterface $handlerResolverMock = null,
        ?BuilderInterface $builderMock = null,
        ?QueryContainerInterface $stateMachineQueryContainer = null
    ): FinderInterface {
        if ($builderMock === null) {
            $builderMock = $this->createBuilderMock();
        }

        if ($handlerResolverMock === null) {
            $handlerResolverMock = $this->createHandlerResolverMock();
        }

        if ($stateMachineQueryContainer === null) {
            $stateMachineQueryContainer = $this->createStateMachineQueryContainer();
        }

        return new Finder(
            $builderMock,
            $handlerResolverMock,
            $stateMachineQueryContainer,
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createHandlerResolverMock(): HandlerResolverInterface
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
    public function createBuilderMock(): BuilderInterface
    {
        return $this->getMockBuilder(BuilderInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Process\ProcessInterface
     */
    protected function createProcessMock(): ProcessInterface
    {
        return $this->getMockBuilder(ProcessInterface::class)->getMock();
    }
}
