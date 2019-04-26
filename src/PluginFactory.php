<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorInterface;
use Cake\ORM\TableRegistry;
use StateMachine\Business\Lock\ItemLock;
use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Business\Logger\PathFinder;
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
use StateMachine\Business\StateMachine\Condition;
use StateMachine\Business\StateMachine\ConditionInterface;
use StateMachine\Business\StateMachine\Finder;
use StateMachine\Business\StateMachine\FinderInterface;
use StateMachine\Business\StateMachine\HandlerResolver;
use StateMachine\Business\StateMachine\HandlerResolverInterface;
use StateMachine\Business\StateMachine\LockedTrigger;
use StateMachine\Business\StateMachine\Persistence;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\StateUpdater;
use StateMachine\Business\StateMachine\StateUpdaterInterface;
use StateMachine\Business\StateMachine\Timeout;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Business\StateMachine\Trigger;
use StateMachine\Business\StateMachine\TriggerInterface;
use StateMachine\Graph\Drawer;
use StateMachine\Graph\DrawerInterface;
use StateMachine\Graph\Graph;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemsTable;
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
    public function createLockedStateMachineTrigger(): TriggerInterface
    {
        return new LockedTrigger(
            $this->createStateMachineTrigger(),
            $this->createItemLock(),
            $this->createHandlerResolver()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\TriggerInterface
     */
    public function createStateMachineTrigger(): TriggerInterface
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
    public function createItemLock(): ItemLockInterface
    {
        return new ItemLock(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->createStateMachineLocksTable()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\ConditionInterface
     */
    public function createStateMachineCondition(): ConditionInterface
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
    public function createStateUpdater(): StateUpdaterInterface
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
    public function createStateMachineBuilder(): BuilderInterface
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
    public function createStateMachineFinder(): FinderInterface
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
    public function createStateMachineTimeout(): TimeoutInterface
    {
        return new Timeout(
            $this->createStateMachinePersistence()
        );
    }

    /**
     * @return \StateMachine\Business\Logger\TransitionLogInterface
     */
    public function createLoggerTransitionLog(): TransitionLogInterface
    {
        return new TransitionLog(
            $this->createStateMachineTransitionLogsTable()
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\PersistenceInterface
     */
    public function createStateMachinePersistence(): PersistenceInterface
    {
        return new Persistence(
            $this->getQueryContainer(),
            $this->createStateMachineItemStateHistoryTable(),
            $this->createStateMachineProcessesTable(),
            $this->createStateMachineItemStatesTable(),
            $this->createStateMachineTimeoutsTable()
        );
    }

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function createProcessEvent(): EventInterface
    {
        return new Event();
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function createProcessState(): StateInterface
    {
        return new State();
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    public function createProcessTransition(): TransitionInterface
    {
        return new Transition();
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function createProcessProcess(): ProcessInterface
    {
        return new Process();
    }

    /**
     * @return \StateMachine\Business\Logger\PathFinder
     */
    protected function createPathFinder(): PathFinder
    {
        return new PathFinder();
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Graph\DrawerInterface
     */
    public function createGraphDrawer(string $stateMachineName): DrawerInterface
    {
        return new Drawer(
            Graph::create(StateMachineConfig::GRAPH_NAME, $this->getConfig()->getGraphDefaults(), true, false),
            $this->createHandlerResolver()->get($stateMachineName)
        );
    }

    /**
     * @return \StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createHandlerResolver(): HandlerResolverInterface
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
    public function getConfig(): StateMachineConfig
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
     * @return \StateMachine\Model\Table\StateMachineItemsTable
     */
    public function createStateMachineItemsTable(): StateMachineItemsTable
    {
        return $this->getTableLocator()->get('StateMachine.StateMachineItems');
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
