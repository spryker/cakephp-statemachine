<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Mocks;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\StateMachine\BuilderInterface;
use StateMachine\Business\StateMachine\ConditionInterface;
use StateMachine\Business\StateMachine\FinderInterface;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdaterInterface;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Business\StateMachine\TriggerInterface;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\StateMachineConfig;

class StateMachineMocks extends TestCase
{
    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Logger\TransitionLog
     */
    protected function createTransitionLogMock()
    {
        $transitionLogMock = $this->getMockBuilder(TransitionLogInterface::class)->getMock();

        return $transitionLogMock;
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

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\ConditionPluginInterface
     */
    protected function createConditionPluginMock()
    {
        $conditionPluginMock = $this->getMockBuilder(ConditionPluginInterface::class)->getMock();

        return $conditionPluginMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Model\QueryContainerInterface
     */
    protected function createStateMachineQueryContainerMock()
    {
        $builderMock = $this->getMockBuilder(QueryContainerInterface::class)->getMock();

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

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected function createTimeoutMock()
    {
        $timeoutMock = $this->getMockBuilder(TimeoutInterface::class)->getMock();

        return $timeoutMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createStateMachinePersistenceMock()
    {
        $persistenceMock = $this->getMockBuilder(PersistenceInterface::class)->getMock();

        return $persistenceMock;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\StateMachineConfig
     */
    protected function createStateMachineConfigMock()
    {
        $stateMachineConfigMock = $this->getMockBuilder(StateMachineConfig::class)->getMock();

        return $stateMachineConfigMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\TriggerInterface
     */
    protected function createTriggerMock()
    {
        $triggerLockMock = $this->getMockBuilder(TriggerInterface::class)->getMock();

        return $triggerLockMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Lock\ItemLockInterface
     */
    protected function createItemLockMock()
    {
        $itemLockMock = $this->getMockBuilder(ItemLockInterface::class)->getMock();

        return $itemLockMock;
    }
}
