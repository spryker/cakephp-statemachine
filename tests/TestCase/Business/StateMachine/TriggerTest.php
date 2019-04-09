<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Logger\TransitionLog;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\EventInterface;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\Process\TransitionInterface;
use StateMachine\Business\StateMachine\Builder;
use StateMachine\Business\StateMachine\BuilderInterface;
use StateMachine\Business\StateMachine\ConditionInterface;
use StateMachine\Business\StateMachine\Finder;
use StateMachine\Business\StateMachine\FinderInterface;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\Persistence;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdaterInterface;
use StateMachine\Business\StateMachine\Trigger;
use StateMachine\Business\StateMachine\TriggerInterface;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;
use StateMachine\StateMachineConfig;
use StateMachine\Test\Fixture\StateMachineProcessesFixture;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class TriggerTest extends TestCase
{
    public const ITEM_IDENTIFIER = 1985;
    public const INITIAL_STATE = 'new';
    public const TEST_COMMAND = 'TestCommand';

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStateHistoryTable
     */
    protected $StateMachineItemStateHistory;

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
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected $StateMachineTransitionLogs;

    /**
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineTransitionLogs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStateHistory') ? [] : ['className' => StateMachineItemStateHistoryTable::class];
        $this->StateMachineItemStateHistory = TableRegistry::getTableLocator()->get('StateMachineItemStateHistory', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineProcesses') ? [] : ['className' => StateMachineProcessesTable::class];
        $this->StateMachineProcesses = TableRegistry::getTableLocator()->get('StateMachineProcesses', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStates') ? [] : ['className' => StateMachineItemStatesTable::class];
        $this->StateMachineItemStates = TableRegistry::getTableLocator()->get('StateMachineItemStates', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineTimeouts') ? [] : ['className' => StateMachineTimeoutsTable::class];
        $this->StateMachineTimeouts = TableRegistry::getTableLocator()->get('StateMachineTimeouts', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineTransitionLogs') ? [] : ['className' => StateMachineTransitionLogsTable::class];
        $this->StateMachineTransitionLogs = TableRegistry::getTableLocator()->get('StateMachineTransitionLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStateHistory, $this->StateMachineProcesses, $this->StateMachineItemStates, $this->StateMachineTimeouts);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testTriggerForNewItemShouldExecutedSMAndPersistNewItem(): void
    {
        $conditionMock = $this->createTriggerConditionMock();

        $trigger = $this->createTrigger($conditionMock);

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setStateMachineName(StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME);
        $stateMachineProcessTransfer->setProcessName(StateMachineProcessesFixture::PROCESS_NAME_1);

        $affectedItems = $trigger->triggerForNewStateMachineItem($stateMachineProcessTransfer, static::ITEM_IDENTIFIER);

        $this->assertEquals(1, $affectedItems);
    }

    /**
     * @return void
     */
    public function testTriggerEventShouldTriggerSmForGiveItems()
    {
        $conditionMock = $this->createTriggerConditionMock();

        $trigger = $this->createTrigger($conditionMock);

        $stateMachineItemTransfer = $this->createTriggerStateMachineItem();
        $stateMachineItems[] = $stateMachineItemTransfer;

        $affectedItems = $trigger->triggerEvent(
            'event',
            $stateMachineItems
        );

        $this->assertEquals(1, $affectedItems);
    }

    /**
     * @return void
     */
    public function testTriggerConditionsWithoutEventShouldExecuteConditionCheckAndTriggerEvents()
    {
        $conditionMock = $this->createTriggerConditionMock();

        $conditionMock->expects($this->once())
            ->method('getOnEnterEventsForStatesWithoutTransition')
            ->willReturn($this->createStateMachineItems());

        $trigger = $this->createTrigger($conditionMock);

        $affectedItems = $trigger->triggerConditionsWithoutEvent(StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME);

        $this->assertEquals(1, $affectedItems);
    }

    /**
     * @return void
     */
    public function testTriggerForTimeoutExpiredItemsShouldExecuteSMOnItemsWithExpiredTimeout()
    {
        $conditionMock = $this->createTriggerConditionMock();

        $trigger = $this->createTrigger($conditionMock);

        $affectedItems = $trigger->triggerForTimeoutExpiredItems(StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME);

        $this->assertEquals(1, $affectedItems);
    }

    /**
     * @return void
     */
    public function testTriggerShouldLogTransitionsForTriggerEvent()
    {
        $conditionMock = $this->createTriggerConditionMock();

        $trigger = $this->createTrigger($conditionMock);

        $stateMachineItems[] = $this->createTriggerStateMachineItem();

        $trigger->triggerEvent(
            'event',
            $stateMachineItems
        );
    }

    /**
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    protected function createStateMachineItems(): array
    {
        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName(StateMachineProcessesFixture::PROCESS_NAME_1);
        $stateMachineItemTransfer->setIdentifier(1);
        $stateMachineItemTransfer->setStateName('new');
        $items['event'][] = $stateMachineItemTransfer;

        return $items;
    }

    /**
     * @param \StateMachine\Business\StateMachine\ConditionInterface|null $conditionMock
     * @param \StateMachine\Business\StateMachine\StateUpdaterInterface|null $stateUpdaterMock
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface|null $handlerResolverMock
     *
     * @return \StateMachine\Business\StateMachine\TriggerInterface
     */
    protected function createTrigger(
        ?ConditionInterface $conditionMock = null,
        ?StateUpdaterInterface $stateUpdaterMock = null,
        ?HandlerResolverInterface $handlerResolverMock = null
    ): TriggerInterface {
        if ($handlerResolverMock === null) {
            $handlerResolverMock = $this->createHandlerResolverMock();

            $commandMock = $this->createCommandMock();

            $handlerMock = $this->createStateMachineHandlerMock();
            $handlerMock->method('getActiveProcesses')->willReturn([StateMachineProcessesFixture::PROCESS_NAME_1]);
            $handlerMock->method('getInitialStateForProcess')->willReturn(static::INITIAL_STATE);
            $handlerMock->method('getCommandPlugins')->willReturn([
                static::TEST_COMMAND => $commandMock,
            ]);
            $handlerResolverMock->method('get')->willReturn($handlerMock);
        }

        if ($stateUpdaterMock === null) {
            $stateUpdaterMock = $this->createStateUpdaterMock();
        }

        if ($conditionMock === null) {
            $conditionMock = $this->createConditionMock();
        }

        return new Trigger(
            $this->createTransitionLog(),
            $handlerResolverMock,
            $this->createFinder(),
            $this->createPersistence(),
            $conditionMock,
            $stateUpdaterMock
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\ConditionInterface
     */
    protected function createTriggerConditionMock()
    {
        $conditionMock = $this->createConditionMock();
        $targetState = new State();
        $targetState->setName('target state');
        $conditionMock->expects($this->once())
            ->method('getTargetStatesFromTransitions')
            ->willReturn($targetState);

        return $conditionMock;
    }

    /**
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected function createTriggerStateMachineItem(): StateMachineItemTransfer
    {
        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setIdentifier(1);
        $stateMachineItemTransfer->setStateName('new');
        $stateMachineItemTransfer->setIdItemState(1);
        $stateMachineItemTransfer->setEventName('event');
        $stateMachineItemTransfer->setProcessName(StateMachineProcessesFixture::PROCESS_NAME_1);

        return $stateMachineItemTransfer;
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    protected function createQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }

    /**
     * @return \StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistence(): PersistenceInterface
    {
        return new Persistence(
            $this->createQueryContainer(),
            $this->StateMachineItemStateHistory,
            $this->StateMachineProcesses,
            $this->StateMachineItemStates,
            $this->StateMachineTimeouts
        );
    }

    /**
     * @return \StateMachine\Business\Logger\TransitionLogInterface
     */
    protected function createTransitionLog(): TransitionLogInterface
    {
        return new TransitionLog($this->StateMachineTransitionLogs);
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\ConditionInterface
     */
    protected function createConditionMock()
    {
        $conditionMock = $this->getMockBuilder(ConditionInterface::class)->getMock();

        return $conditionMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\CommandPluginInterface
     */
    protected function createCommandMock()
    {
        $commandMock = $this->getMockBuilder(CommandPluginInterface::class)->getMock();

        return $commandMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    protected function createStateUpdaterMock()
    {
        $stateUpdaterMock = $this->getMockBuilder(StateUpdaterInterface::class)->getMock();

        return $stateUpdaterMock;
    }

    /**
     * @return \StateMachine\Business\StateMachine\FinderInterface
     */
    protected function createFinder(): FinderInterface
    {
        return new Finder(
            $this->createBuilder(),
            $this->createHandlerResolverMock(),
            $this->createQueryContainer()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\BuilderInterface
     */
    protected function createBuilder(): BuilderInterface
    {
        return new Builder(
            $this->createEvent(),
            $this->createState(),
            $this->createTransition(),
            $this->createProcess(),
            $this->createStateMachineConfig()
        );
    }

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    protected function createEvent(): EventInterface
    {
        return new Event();
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function createState(): StateInterface
    {
        return new State();
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    protected function createTransition(): TransitionInterface
    {
        return new Transition();
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    protected function createProcess(): ProcessInterface
    {
        return new Process();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\StateMachineConfig
     */
    protected function createStateMachineConfig()
    {
        $stateMachineConfigMock = $this->getMockBuilder(StateMachineConfig::class)->getMock();

        $pathToStateMachineFixtures = realpath(__DIR__ . '/../../../test_files') . DIRECTORY_SEPARATOR;
        $stateMachineConfigMock->method('getPathToStateMachineXmlFiles')->willReturn($pathToStateMachineFixtures);
        $stateMachineConfigMock->method('getSubProcessPrefixDelimiter')->willReturn(' - ');

        return $stateMachineConfigMock;
    }
}
