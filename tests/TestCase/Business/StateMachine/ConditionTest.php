<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use App\StateMachine\Condition\TestFalseStateMachineCondition;
use App\StateMachine\Condition\TestTrueStateMachineCondition;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\Process\TransitionInterface;
use StateMachine\Business\StateMachine\Condition;
use StateMachine\Business\StateMachine\ConditionInterface;
use StateMachine\Business\StateMachine\FinderInterface;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\Persistence;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdater;
use StateMachine\Business\StateMachine\StateUpdaterInterface;
use StateMachine\Business\StateMachine\Timeout;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateLogsTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;

class ConditionTest extends TestCase
{
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
    protected $fixtures = [
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItems',
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
    public function testCheckConditionForTransitionShouldReturnTargetStateOfGivenTransition(): void
    {
        $condition = $this->createCondition(TestTrueStateMachineCondition::class);

        $sourceState = $this->createState('source state');
        $targetState = $this->createState('target state');

        $transitions[] = $this->createTransition($sourceState, $targetState);

        $processedTargetState = $condition->getTargetStatesFromTransitions(
            $transitions,
            (new ItemDto())->setStateMachineName('Test'),
            new State(),
            $this->createTransitionLogMock()
        );

        $this->assertSame($targetState->getName(), $processedTargetState->getName());
    }

    /**
     * @return void
     */
    public function testCheckConditionForTransitionWhenConditionReturnsFalseShouldReturnSourceState(): void
    {
        $condition = $this->createCondition(TestFalseStateMachineCondition::class);

        $sourceState = $this->createState('source state');
        $targetState = $this->createState('target state');

        $transitions[] = $this->createTransition($sourceState, $targetState);

        $sourceState = $this->createState('initial source');

        $processedTargetState = $condition->getTargetStatesFromTransitions(
            $transitions,
            (new ItemDto())->setStateMachineName('Test'),
            $sourceState,
            $this->createTransitionLogMock()
        );

        $this->assertSame($sourceState->getName(), $processedTargetState->getName());
    }

    /**
     * @param string $conditionPluginClassname
     *
     * @return \StateMachine\Business\StateMachine\ConditionInterface
     */
    protected function createCondition(string $conditionPluginClassname): ConditionInterface
    {
        return new Condition(
            $this->createTransitionLogMock(),
            $this->createStateMachineResolverMock($conditionPluginClassname),
            $this->createFinderMock(),
            $this->createPersistence(),
            $this->createStateUpdater()
        );
    }

    /**
     * @param string $name
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function createState(string $name): StateInterface
    {
        $state = new State();
        $state->setName($name);

        return $state;
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     * @param \StateMachine\Business\Process\StateInterface $targetState
     *
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    protected function createTransition(StateInterface $sourceState, StateInterface $targetState): TransitionInterface
    {
        $transition = new Transition();
        $transition->setCondition('condition');
        $transition->setSourceState($sourceState);
        $transition->setTargetState($targetState);

        return $transition;
    }

    /**
     * @param string $conditionPluginClassname
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createStateMachineResolverMock(string $conditionPluginClassname): HandlerResolverInterface
    {
        $stateMachineHandler = $this->createStateMachineHandlerMock();
        $stateMachineHandler->expects($this->exactly(2))
            ->method('getConditions')
            ->willReturn([
                'condition' => $conditionPluginClassname,
            ]);

        $stateMachineHandlerResolverMock = $this->createHandlerResolverMock();
        $stateMachineHandlerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($stateMachineHandler);

        return $stateMachineHandlerResolverMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Logger\TransitionLogInterface
     */
    protected function createTransitionLogMock(): TransitionLogInterface
    {
        $transitionLogMock = $this->getMockBuilder(TransitionLogInterface::class)->getMock();

        return $transitionLogMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected function createStateMachineHandlerMock(): StateMachineHandlerInterface
    {
        $stateMachineHandlerMock = $this->getMockBuilder(StateMachineHandlerInterface::class)->getMock();

        return $stateMachineHandlerMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\FinderInterface
     */
    protected function createFinderMock(): FinderInterface
    {
        $finderMock = $this->getMockBuilder(FinderInterface::class)->getMock();

        return $finderMock;
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    protected function createQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistence(): PersistenceInterface
    {
        return new Persistence(
            $this->createQueryContainer(),
            $this->StateMachineItemStateLogs,
            $this->StateMachineProcesses,
            $this->StateMachineItemStates,
            $this->StateMachineTimeouts
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    protected function createStateUpdater(): StateUpdaterInterface
    {
        return new StateUpdater(
            $this->createTimeout(),
            $this->createHandlerResolverMock(),
            $this->createPersistence(),
            $this->createQueryContainer()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected function createTimeout(): TimeoutInterface
    {
        return new Timeout($this->createPersistence(), $this->createTransitionLogMock());
    }
}
