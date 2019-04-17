<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use Exception;
use StateMachine\Business\Exception\ConditionNotFoundException;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class Condition implements ConditionInterface
{
    /**
     * @var array
     */
    protected $eventCounter = [];

    /**
     * @var array
     */
    protected $processBuffer = [];

    /**
     * @var \StateMachine\Business\Logger\TransitionLogInterface
     */
    protected $transitionLog;

    /**
     * @var \StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @var \StateMachine\Business\StateMachine\FinderInterface
     */
    protected $finder;

    /**
     * @var \StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected $stateMachinePersistence;

    /**
     * @var \StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    protected $stateUpdater;

    /**
     * @param \StateMachine\Business\Logger\TransitionLogInterface $transitionLog
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     * @param \StateMachine\Business\StateMachine\FinderInterface $finder
     * @param \StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     * @param \StateMachine\Business\StateMachine\StateUpdaterInterface $stateUpdate
     */
    public function __construct(
        TransitionLogInterface $transitionLog,
        HandlerResolverInterface $stateMachineHandlerResolver,
        FinderInterface $finder,
        PersistenceInterface $stateMachinePersistence,
        StateUpdaterInterface $stateUpdate
    ) {
        $this->transitionLog = $transitionLog;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
        $this->finder = $finder;
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->stateUpdater = $stateUpdate;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface[] $transitions
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     * @param \StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getTargetStatesFromTransitions(
        array $transitions,
        ItemDto $stateMachineItemTransfer,
        StateInterface $sourceState,
        TransitionLogInterface $transactionLogger
    ) {
        $possibleTransitions = [];
        foreach ($transitions as $transition) {
            if ($transition->hasCondition()) {
                $isValidCondition = $this->checkCondition(
                    $stateMachineItemTransfer,
                    $transactionLogger,
                    $transition->getCondition()
                );

                if ($isValidCondition) {
                    array_push($possibleTransitions, $transition);
                }
            } else {
                array_push($possibleTransitions, $transition);
            }
        }

        return $this->findTargetState($sourceState, $possibleTransitions);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param \StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     * @param string $conditionName
     *
     * @throws \Exception
     *
     * @return bool
     */
    protected function checkCondition(
        ItemDto $stateMachineItemTransfer,
        TransitionLogInterface $transactionLogger,
        $conditionName
    ) {
        $conditionPlugin = $this->getConditionPlugin(
            $conditionName,
            $stateMachineItemTransfer->getStateMachineNameOrFail()
        );

        try {
            $conditionCheck = $conditionPlugin->check($stateMachineItemTransfer);
        } catch (Exception $e) {
            $transactionLogger->setIsError(true);
            $transactionLogger->setErrorMessage(get_class($conditionPlugin) . ' - ' . $e->getMessage());
            $transactionLogger->saveAll();
            throw $e;
        }

        if ($conditionCheck === true) {
            $transactionLogger->addCondition($stateMachineItemTransfer, $conditionPlugin);
            return true;
        }

        return false;
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     * @param \StateMachine\Business\Process\TransitionInterface[] $possibleTransitions
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function findTargetState(StateInterface $sourceState, array $possibleTransitions)
    {
        $targetState = $sourceState;
        if (count($possibleTransitions) > 0) {
            $selectedTransition = array_shift($possibleTransitions);
            $targetState = $selectedTransition->getTargetState();
        }
        return $targetState;
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[][] $itemsWithOnEnterEvent
     */
    public function getOnEnterEventsForStatesWithoutTransition($stateMachineName, $processName)
    {
        $process = $this->finder->findProcessByStateMachineAndProcessName($stateMachineName, $processName);
        $transitions = $process->getAllTransitionsWithoutEvent();

        $stateToTransitionsMap = $this->createStateToTransitionMap($transitions);

        $stateMachineItems = $this->getItemsByStatesAndProcessName($stateMachineName, $stateToTransitionsMap, $process);

        $this->transitionLog->init($stateMachineItems);
        $sourceStates = $this->createStateMap($stateMachineItems);

        $this->persistAffectedStates($stateMachineName, $stateToTransitionsMap, $stateMachineItems);

        $processes = [$process->getName() => $process];

        $this->stateUpdater->updateStateMachineItemState(
            $stateMachineItems,
            $processes,
            $sourceStates
        );

        $itemsWithOnEnterEvent = $this->finder->filterItemsWithOnEnterEvent(
            $stateMachineItems,
            $processes,
            $sourceStates
        );

        return $itemsWithOnEnterEvent;
    }

    /**
     * @param string $stateMachineName
     * @param string[] $states
     * @param \StateMachine\Business\Process\ProcessInterface $process

     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    protected function getItemsByStatesAndProcessName(
        $stateMachineName,
        array $states,
        ProcessInterface $process
    ) {
        $stateMachineItemStateIds = $this->stateMachinePersistence->getStateMachineItemIdsByStatesProcessAndStateMachineName(
            $process->getName(),
            $stateMachineName,
            array_keys($states)
        );

        $stateMachineItems = $this->stateMachineHandlerResolver
            ->get($stateMachineName)
            ->getStateMachineItemsByStateIds($stateMachineItemStateIds);

        $stateMachineItems = $this->stateMachinePersistence
            ->updateStateMachineItemsFromPersistence($stateMachineItems);

        return $stateMachineItems;
    }

    /**
     * @param string $stateMachineName
     * @param array $states Keys are state names, values are collections of TransitionInterface.
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return void
     */
    protected function persistAffectedStates(
        $stateMachineName,
        array $states,
        array $stateMachineItems
    ) {
        $targetStateMap = [];
        foreach ($stateMachineItems as $i => $stateMachineItemTransfer) {
            $stateName = $stateMachineItemTransfer->getStateNameOrFail();

            $process = $this->finder->findProcessByStateMachineAndProcessName(
                $stateMachineName,
                $stateMachineItemTransfer->getProcessNameOrFail()
            );

            $sourceState = $process->getStateFromAllProcesses($stateName);

            $this->transitionLog->addSourceState($stateMachineItemTransfer, $sourceState->getName());

            $transitions = $states[$stateMachineItemTransfer->getStateNameOrFail()];

            $targetState = $sourceState;
            if (count($transitions) > 0) {
                $targetState = $this->getTargetStatesFromTransitions(
                    $transitions,
                    $stateMachineItemTransfer,
                    $sourceState,
                    $this->transitionLog
                );
            }

            $this->transitionLog->addTargetState($stateMachineItemTransfer, $targetState->getName());

            $targetStateMap[$i] = $targetState->getName();
        }

        foreach ($stateMachineItems as $i => $stateMachineItemTransfer) {
            $this->stateMachinePersistence->saveStateMachineItem($stateMachineItems[$i], $targetStateMap[$i]);
        }
    }

    /**
     * @param array $transitions
     *
     * @return array
     */
    protected function createStateToTransitionMap(array $transitions)
    {
        $stateToTransitionsMap = [];
        foreach ($transitions as $transition) {
            $sourceStateName = $transition->getSourceState()->getName();
            if (array_key_exists($sourceStateName, $stateToTransitionsMap) === false) {
                $stateToTransitionsMap[$sourceStateName] = [];
            }
            $stateToTransitionsMap[$sourceStateName][] = $transition;
        }

        return $stateToTransitionsMap;
    }

    /**
     * @param string $conditionString
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dependency\ConditionPluginInterface
     */
    protected function getConditionPlugin($conditionString, $stateMachineName)
    {
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);

        $this->assertConditionIsSet($conditionString, $stateMachineHandler);

        return $stateMachineHandler->getConditions()[$conditionString];
    }

    /**
     * @param string $conditionString
     * @param \StateMachine\Dependency\StateMachineHandlerInterface $stateMachineHandler
     *
     * @throws \StateMachine\Business\Exception\ConditionNotFoundException
     *
     * @return void
     */
    protected function assertConditionIsSet($conditionString, StateMachineHandlerInterface $stateMachineHandler)
    {
        if (!isset($stateMachineHandler->getConditions()[$conditionString])) {
            throw new ConditionNotFoundException(
                sprintf(
                    'Condition "%s" not registered in "%s" class. Please add it to getConditions() method.',
                    $conditionString,
                    get_class($this->stateMachineHandlerResolver)
                )
            );
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string[]
     */
    protected function createStateMap(array $stateMachineItems)
    {
        $sourceStates = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $sourceStates[$stateMachineItemTransfer->getIdentifier()] = $stateMachineItemTransfer->getStateName();
        }
        return $sourceStates;
    }
}
