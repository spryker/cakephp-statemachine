<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\StateMachine\Condition;
use StateMachine\Business\StateMachine\FinderInterface;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdaterInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Test\TestCase\Mocks\StateMachineMocks;
use StateMachine\Transfer\StateMachineItemTransfer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group ConditionTest
 * Add your own group annotations below this line
 */
class ConditionTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckConditionForTransitionShouldReturnTargetStateOfGivenTransition()
    {
        $stateMachineHandlerResolverMock = $this->createStateMachineResolverMock(true);

        $condition = new Condition(
            $this->createTransitionLogMock(),
            $stateMachineHandlerResolverMock,
            $this->createFinderMock(),
            $this->createPersistenceMock(),
            $this->createStateUpdaterMock()
        );

        $transitions = [];
        $sourceState = new State();
        $sourceState->setName('source state');

        $targetState = new State();
        $targetState->setName('target state');

        $transition = new Transition();
        $transition->setCondition('condition');
        $transition->setSourceState($sourceState);
        $transition->setTargetState($targetState);
        $transitions[] = $transition;

        $processedTargetState = $condition->getTargetStatesFromTransitions(
            $transitions,
            new StateMachineItemTransfer(),
            new State(),
            $this->createTransitionLogMock()
        );

        $this->assertEquals($targetState->getName(), $processedTargetState->getName());
    }

    /**
     * @return void
     */
    public function testCheckConditionForTransitionWhenConditionReturnsFalseShouldReturnSourceState()
    {
        $stateMachineHandlerResolverMock = $this->createStateMachineResolverMock(false);

        $condition = new Condition(
            $this->createTransitionLogMock(),
            $stateMachineHandlerResolverMock,
            $this->createFinderMock(),
            $this->createPersistenceMock(),
            $this->createStateUpdaterMock()
        );

        $transitions = [];
        $sourceState = new State();
        $sourceState->setName('source state');

        $targetState = new State();
        $targetState->setName('target state');

        $transition = new Transition();
        $transition->setCondition('condition');
        $transition->setSourceState($sourceState);
        $transition->setTargetState($targetState);
        $transitions[] = $transition;

        $sourceState = new State();
        $sourceState->setName('initial source');

        $processedTargetState = $condition->getTargetStatesFromTransitions(
            $transitions,
            new StateMachineItemTransfer(),
            $sourceState,
            $this->createTransitionLogMock()
        );

        $this->assertEquals($sourceState->getName(), $processedTargetState->getName());
    }

    /**
     * @param bool $conditionCheckResult
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createStateMachineResolverMock($conditionCheckResult)
    {
        $conditionPluginMock = $this->createConditionPluginMock();
        $conditionPluginMock->expects($this->once())
            ->method('check')
            ->willReturn($conditionCheckResult);

        $stateMachineHandler = $this->createStateMachineHandlerMock();
        $stateMachineHandler->expects($this->exactly(2))
            ->method('getConditionPlugins')
            ->willReturn([
                    'condition' => $conditionPluginMock,
                ]);

        $stateMachineHandlerResolverMock = $this->createHandlerResolverMock();
        $stateMachineHandlerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($stateMachineHandler);

        return $stateMachineHandlerResolverMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Logger\TransitionLog
     */
    protected function createTransitionLogMock()
    {
        $transitionLogMock = $this->getMockBuilder(TransitionLogInterface::class)->getMock();

        return $transitionLogMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected function createStateMachineHandlerMock()
    {
        $stateMachineHandlerMock = $this->getMockBuilder(StateMachineHandlerInterface::class)->getMock();

        return $stateMachineHandlerMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\ConditionPluginInterface
     */
    protected function createConditionPluginMock()
    {
        $conditionPluginMock = $this->getMockBuilder(ConditionPluginInterface::class)->getMock();

        return $conditionPluginMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\FinderInterface
     */
    protected function createFinderMock()
    {
        $finderMock = $this->getMockBuilder(FinderInterface::class)->getMock();

        return $finderMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    protected function createStateUpdaterMock()
    {
        $stateUpdaterMock = $this->getMockBuilder(StateUpdaterInterface::class)->getMock();

        return $stateUpdaterMock;
    }
}
