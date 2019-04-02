<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorInterface;
use Cake\ORM\TableRegistry;
use StateMachine\Business\Lock\ItemLock;
use StateMachine\Business\Logger\PathFinder;
use StateMachine\Business\Logger\TransitionLog;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\StateMachine\Builder;
use StateMachine\Business\StateMachine\Condition;
use StateMachine\Business\StateMachine\Finder;
use StateMachine\Business\StateMachine\HandlerResolver;
use StateMachine\Business\StateMachine\LockedTrigger;
use StateMachine\Business\StateMachine\Persistence;
use StateMachine\Business\StateMachine\StateUpdater;
use StateMachine\Business\StateMachine\Timeout;
use StateMachine\Business\StateMachine\Trigger;
use StateMachine\Graph\Drawer;
use StateMachine\Graph\Graph;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineLocksTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;

class PluginFactory
{
    /**
     * @return \StateMachine\Business\StateMachine\TriggerInterface
     */
    public function createLockedStateMachineTrigger()
    {
        return new LockedTrigger(
            $this->createStateMachineTrigger(),
            $this->createItemLock()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\TriggerInterface
     */
    public function createStateMachineTrigger()
    {
        return new Trigger(
            $this->createLoggerTransitionLog(),
            $this->createHandlerResolver(),
            $this->createStateMachineFinder(),
            $this->createStateMachinePersistence(),
            $this->createStateMachineCondition(),
            $this->createStateUpdater()
        );
    }

    /**
     * @return \StateMachine\Business\Lock\ItemLockInterface
     */
    public function createItemLock()
    {
        return new ItemLock(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\ConditionInterface
     */
    public function createStateMachineCondition()
    {
        return new Condition(
            $this->createLoggerTransitionLog(),
            $this->createHandlerResolver(),
            $this->createStateMachineFinder(),
            $this->createStateMachinePersistence(),
            $this->createStateUpdater()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    public function createStateUpdater()
    {
        return new StateUpdater(
            $this->createStateMachineTimeout(),
            $this->createHandlerResolver(),
            $this->createStateMachinePersistence(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\BuilderInterface
     */
    public function createStateMachineBuilder()
    {
        return new Builder(
            $this->createProcessEvent(),
            $this->createProcessState(),
            $this->createProcessTransition(),
            $this->createProcessProcess(),
            $this->getConfig()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\FinderInterface
     */
    public function createStateMachineFinder()
    {
        return new Finder(
            $this->createStateMachineBuilder(),
            $this->createHandlerResolver(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\TimeoutInterface
     */
    public function createStateMachineTimeout()
    {
        return new Timeout(
            $this->createStateMachinePersistence()
        );
    }

    /**
     * @return \StateMachine\Business\Logger\TransitionLogInterface
     */
    public function createLoggerTransitionLog()
    {
        return new TransitionLog(
            $this->createPathFinder(),
            $this->getUtilNetworkService()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\PersistenceInterface
     */
    public function createStateMachinePersistence()
    {
        return new Persistence($this->getQueryContainer());
    }

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function createProcessEvent()
    {
        return new Event();
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function createProcessState()
    {
        return new State();
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    public function createProcessTransition()
    {
        return new Transition();
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function createProcessProcess()
    {
        return new Process();
    }

    /**
     * @return \StateMachine\Business\Logger\PathFinder
     */
    protected function createPathFinder()
    {
        return new PathFinder();
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Graph\DrawerInterface
     */
    public function createGraphDrawer($stateMachineName)
    {
        return new Drawer(
            Graph::create(StateMachineConfig::GRAPH_NAME, $this->getConfig()->getGraphDefaults(), true, false),
            $this->createHandlerResolver()->get($stateMachineName)
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createHandlerResolver()
    {
        return new HandlerResolver($this->getStateMachineHandlers());
    }

    /**
     * @return \StateMachine\Dependency\StateMachineHandlerInterface[]
     */
    public function getStateMachineHandlers()
    {
        $handlers = (array)Configure::read('StateMachine.handlers');
        foreach ($handlers as $key => $handler) {
            $handlers[$key] = new $handler();
        }

        return $handlers;
    }

    /**
     * @return \StateMachine\StateMachineConfig
     */
    public function getConfig()
    {
        return new StateMachineConfig();
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    public function getQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }

    /**
     * @return \Cake\ORM\Locator\LocatorInterface
     */
    public function getTableLocator(): LocatorInterface
    {
        return TableRegistry::getTableLocator();
    }

    /**
     * @return \StateMachine\Model\Table\StateMachineItemStateHistoryTable
     */
    public function createStateMachineItemStateHistoryTable(): StateMachineItemStateHistoryTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineItemStateHistory');
    }

    /**
     * @return \StateMachine\Model\Table\StateMachineItemStatesTable
     */
    public function createStateMachineItemStatesTable(): StateMachineItemStatesTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineItemStates');
    }

    /**
     * @return \StateMachine\Model\Table\StateMachineLocksTable
     */
    public function createStateMachineLocksTable(): StateMachineLocksTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineLocks');
    }

    /**
     * @return \StateMachine\Model\Table\StateMachineProcessesTable
     */
    public function createStateMachineProcessesTable(): StateMachineProcessesTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineProcesses');
    }

    /**
     * @return \StateMachine\Model\Table\StateMachineTimeoutsTable
     */
    public function createStateMachineTimeoutsTable(): StateMachineTimeoutsTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineTimeouts');
    }

    /**
     * @return \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    public function createStateMachineTransitionLogsTable(): StateMachineTransitionLogsTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineTransitionLogs');
    }
}
