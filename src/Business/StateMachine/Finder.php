<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class Finder implements FinderInterface
{
    /**
     * @var \StateMachine\Business\StateMachine\BuilderInterface
     */
    protected $builder;

    /**
     * @var \StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @var \StateMachine\Model\QueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \StateMachine\Business\StateMachine\BuilderInterface $builder
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     * @param \StateMachine\Model\QueryContainerInterface $queryContainer
     */
    public function __construct(
        BuilderInterface $builder,
        HandlerResolverInterface $stateMachineHandlerResolver,
        QueryContainerInterface $queryContainer
    ) {
        $this->builder = $builder;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param string $stateMachineName
     *
     * @return bool
     */
    public function hasHandler($stateMachineName)
    {
        return $this->stateMachineHandlerResolver->find($stateMachineName) !== null;
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Transfer\StateMachineProcessTransfer[]
     */
    public function getProcesses($stateMachineName)
    {
        $processes = [];
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);
        foreach ($stateMachineHandler->getActiveProcesses() as $processName) {
            $processes[$processName] = $this->createStateMachineProcessTransfer($stateMachineName, $processName);
        }

        return $processes;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return string[]
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems)
    {
        $itemsWithManualEvents = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $manualEvents = $this->getManualEventsForStateMachineItem($stateMachineItemTransfer);

            if (count($manualEvents) > 0) {
                $itemsWithManualEvents[$stateMachineItemTransfer->getIdentifier()] = $manualEvents;
            }
        }

        return $itemsWithManualEvents;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return array
     */
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineItemTransfer->requireProcessName();

        $processName = $stateMachineItemTransfer->getProcessName();

        $processBuilder = clone $this->builder;

        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer(
            $stateMachineItemTransfer->getStateMachineName(),
            $processName
        );

        $process = $processBuilder->createProcess($stateMachineProcessTransfer);
        $manualEvents = $process->getManuallyExecutableEventsBySource();

        $stateName = $stateMachineItemTransfer->getStateName();
        if (isset($manualEvents[$stateName])) {
            return $manualEvents[$stateName];
        }

        return [];
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItemTransfer
     */
    public function getItemsWithFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag)
    {
        return $this->getItemsByFlag($stateMachineProcessTransfer, $flag, true);
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItemTransfer
     */
    public function getItemsWithoutFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag)
    {
        return $this->getItemsByFlag($stateMachineProcessTransfer, $flag, false);
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flagName
     * @param bool $hasFlag
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    protected function getItemsByFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flagName, $hasFlag)
    {
        $stateMachineProcessTransfer->requireProcessName()->requireStateMachineName();

        $statesByFlag = $this->getStatesByFlag($stateMachineProcessTransfer, $flagName, $hasFlag);
        if (count($statesByFlag) === 0) {
            return [];
        }

        $stateMachineProcessEntity = $this->getStateMachineProcessEntity($stateMachineProcessTransfer);
        if ($stateMachineProcessEntity === null) {
            return [];
        }

        $stateMachineItems = $this->getFlaggedStateMachineItems(
            $stateMachineProcessTransfer,
            array_keys($statesByFlag)
        );

        $stateMachineItemsWithFlag = [];
        foreach ($stateMachineItems as $stateMachineItemEntity) {
            $stateMachineItemTransfer = $this->createStateMachineHistoryItemTransfer(
                $stateMachineProcessTransfer,
                $stateMachineItemEntity,
                $stateMachineProcessEntity
            );

            $stateMachineItemsWithFlag[] = $stateMachineItemTransfer;
        }

        return $stateMachineItemsWithFlag;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     * @param bool $hasFlag
     *
     * @return \StateMachine\Business\Process\StateInterface[]
     */
    protected function getStatesByFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag, $hasFlag)
    {
        $selectedStates = [];

        $processStateList = $this->builder->createProcess($stateMachineProcessTransfer)->getAllStates();

        foreach ($processStateList as $state) {
            if ($hasFlag !== $state->hasFlag($flag)) {
                continue;
            }
            $selectedStates[$state->getName()] = $state;
        }

        return $selectedStates;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param array $sourceStates
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function filterItemsWithOnEnterEvent(
        array $stateMachineItems,
        array $processes,
        array $sourceStates = []
    ) {
        $itemsWithOnEnterEvent = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $stateName = $stateMachineItemTransfer->requireStateName()->getStateName();
            $processName = $stateMachineItemTransfer->requireProcessName()->getProcessName();

            $this->assertProcessExists($processes, $processName);

            $process = $processes[$processName];
            $targetState = $process->getStateFromAllProcesses($stateName);

            if (isset($sourceStates[$stateMachineItemTransfer->getIdentifier()])) {
                $sourceState = $sourceStates[$stateMachineItemTransfer->getIdentifier()];
            } else {
                $sourceState = $process->getStateFromAllProcesses($stateMachineItemTransfer->getStateName());
            }

            if ($sourceState !== $targetState->getName() && $targetState->hasOnEnterEvent()) {
                $eventName = $targetState->getOnEnterEvent()->getName();
                if (array_key_exists($eventName, $itemsWithOnEnterEvent) === false) {
                    $itemsWithOnEnterEvent[$eventName] = [];
                }
                $itemsWithOnEnterEvent[$eventName][] = $stateMachineItemTransfer;
            }
        }

        return $itemsWithOnEnterEvent;
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function findProcessByStateMachineAndProcessName($stateMachineName, $processName)
    {
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer($stateMachineName, $processName);
        return $this->builder->createProcess($stateMachineProcessTransfer);
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function findProcessesForItems(array $stateMachineItems)
    {
        $processes = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $processName = $stateMachineItemTransfer->requireProcessName()->getProcessName();
            if (isset($processes[$processName])) {
                continue;
            }

            $processes[$stateMachineItemTransfer->getProcessName()] = $this->findProcessByStateMachineAndProcessName(
                $stateMachineItemTransfer->getStateMachineName(),
                $stateMachineItemTransfer->getProcessName()
            );
        }

        return $processes;
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Transfer\StateMachineProcessTransfer
     */
    protected function createStateMachineProcessTransfer($stateMachineName, $processName)
    {
        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);
        $stateMachineProcessTransfer->setProcessName($processName);

        return $stateMachineProcessTransfer;
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string $processName
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function assertProcessExists(array $processes, $processName)
    {
        if (!isset($processes[$processName])) {
            throw new StateMachineException(
                sprintf(
                    'Unknown process "%s" for state machine "%s".',
                    $processName,
                    'SM'
                )
            );
        }
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState $stateMachineItemEntity
     * @param \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess $stateMachineProcessEntity
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected function createStateMachineHistoryItemTransfer(
        StateMachineProcessTransfer $stateMachineProcessTransfer,
        SpyStateMachineItemState $stateMachineItemEntity,
        SpyStateMachineProcess $stateMachineProcessEntity
    ) {

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName($stateMachineProcessTransfer->getProcessName());
        $stateMachineItemTransfer->setIdItemState($stateMachineItemEntity->getIdStateMachineItemState());
        $stateMachineItemTransfer->setIdStateMachineProcess($stateMachineProcessEntity->getIdStateMachineProcess());
        $stateMachineItemTransfer->setStateName($stateMachineItemEntity->getName());
        $stateMachineItemTransfer->setStateMachineName($stateMachineProcessEntity->getStateMachineProcesses());

        $stateMachineItemHistory = $stateMachineItemEntity->getStateHistories();
        if (count($stateMachineItemHistory) > 0) {
            $itemIdentifier = $stateMachineItemHistory->getFirst()->getIdentifier();
            $stateMachineItemTransfer->setIdentifier($itemIdentifier);
        }

        return $stateMachineItemTransfer;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess|null
     */
    protected function getStateMachineProcessEntity(StateMachineProcessTransfer $stateMachineProcessTransfer)
    {
        return $this->queryContainer->queryProcessByStateMachineAndProcessName(
            $stateMachineProcessTransfer->getStateMachineName(),
            $stateMachineProcessTransfer->getProcessName()
        )->findOne();
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param array $statesByFlag
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState[]|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function getFlaggedStateMachineItems(StateMachineProcessTransfer $stateMachineProcessTransfer, array $statesByFlag)
    {
        $itemStateCollection = $this->queryContainer->queryItemsByIdStateMachineProcessAndItemStates(
            $stateMachineProcessTransfer->getStateMachineName(),
            $stateMachineProcessTransfer->getProcessName(),
            $statesByFlag
        )->find();

        return $itemStateCollection;
    }
}
