<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use Cake\Log\Log;
use Exception;
use StateMachine\Business\Exception\ConditionNotFoundException;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Dependency\StateMachineConditionInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class Condition implements ConditionInterface
{
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
     * @param array<\StateMachine\Business\Process\TransitionInterface> $transitions
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     * @param \StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getTargetStatesFromTransitions(
        array $transitions,
        ItemDto $itemDto,
        StateInterface $sourceState,
        TransitionLogInterface $transactionLogger
    ): StateInterface {
        $possibleTransitions = [];
        foreach ($transitions as $transition) {
            if ($transition->hasCondition()) {
                $isValidCondition = $this->checkCondition(
                    $itemDto,
                    $transactionLogger,
                    $transition->getCondition(),
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
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param \StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     * @param string $conditionName
     *
     * @throws \Exception
     *
     * @return bool
     */
    protected function checkCondition(
        ItemDto $itemDto,
        TransitionLogInterface $transactionLogger,
        string $conditionName
    ): bool {
        $conditionPlugin = $this->getCondition(
            $conditionName,
            $itemDto->getStateMachineNameOrFail(),
        );

        try {
            $conditionCheck = $conditionPlugin->check($itemDto);
        } catch (Exception $e) {
            $errorMessage = get_class($conditionPlugin) . ' - ' . $e->getMessage();
            $transactionLogger->setIsError(true);
            $transactionLogger->setErrorMessage($errorMessage);
            $transactionLogger->saveAll();

            Log::write('debug', $errorMessage . PHP_EOL . $e->getTraceAsString(), ['scope' => 'statemachine']);

            throw $e;
        }

        if ($conditionCheck === true) {
            $transactionLogger->addCondition($itemDto, $conditionPlugin);

            return true;
        }

        return false;
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     * @param array<\StateMachine\Business\Process\TransitionInterface> $possibleTransitions
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function findTargetState(StateInterface $sourceState, array $possibleTransitions): StateInterface
    {
        $targetState = $sourceState;
        if (count($possibleTransitions) > 0) {
            /** @var \StateMachine\Business\Process\TransitionInterface $selectedTransition */
            $selectedTransition = array_shift($possibleTransitions);
            $targetState = $selectedTransition->getTargetState();
        }

        return $targetState;
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return array<array<\StateMachine\Dto\StateMachine\ItemDto>>
     */
    public function getOnEnterEventsForStatesWithoutTransition(string $stateMachineName, string $processName): array
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
            $sourceStates,
        );

        $itemsWithOnEnterEvent = $this->finder->filterItemsWithOnEnterEvent(
            $stateMachineItems,
            $processes,
            $sourceStates,
        );

        return $itemsWithOnEnterEvent;
    }

    /**
     * @param string $stateMachineName
     * @param array<string> $states
     * @param \StateMachine\Business\Process\ProcessInterface $process

     * @return array<\StateMachine\Dto\StateMachine\ItemDto>
     */
    protected function getItemsByStatesAndProcessName(
        string $stateMachineName,
        array $states,
        ProcessInterface $process
    ): array {
        $stateMachineItemStateIds = $this->stateMachinePersistence->getStateMachineItemIdsByStatesProcessAndStateMachineName(
            $process->getName(),
            $stateMachineName,
            array_keys($states),
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
     * @param array<\StateMachine\Dto\StateMachine\ItemDto> $stateMachineItems
     *
     * @return void
     */
    protected function persistAffectedStates(
        string $stateMachineName,
        array $states,
        array $stateMachineItems
    ): void {
        $targetStateMap = [];
        foreach ($stateMachineItems as $i => $itemDto) {
            $stateName = $itemDto->getStateNameOrFail();

            $process = $this->finder->findProcessByStateMachineAndProcessName(
                $stateMachineName,
                $itemDto->getProcessNameOrFail(),
            );

            $sourceState = $process->getStateFromAllProcesses($stateName);

            $this->transitionLog->addSourceState($itemDto, $sourceState->getName());

            $transitions = $states[$itemDto->getStateNameOrFail()];

            $targetState = $sourceState;
            if (count($transitions) > 0) {
                $targetState = $this->getTargetStatesFromTransitions(
                    $transitions,
                    $itemDto,
                    $sourceState,
                    $this->transitionLog,
                );
            }

            $this->transitionLog->addTargetState($itemDto, $targetState->getName());

            $targetStateMap[$i] = $targetState->getName();
        }

        foreach ($stateMachineItems as $i => $itemDto) {
            $this->stateMachinePersistence->saveStateMachineItem($stateMachineItems[$i], $targetStateMap[$i]);
        }
    }

    /**
     * @param array $transitions
     *
     * @return array
     */
    protected function createStateToTransitionMap(array $transitions): array
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
     * @return \StateMachine\Dependency\StateMachineConditionInterface
     */
    protected function getCondition(string $conditionString, string $stateMachineName): StateMachineConditionInterface
    {
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);

        $this->assertConditionIsSet($conditionString, $stateMachineHandler);

        /** @var \StateMachine\Dependency\StateMachineConditionInterface $condition */
        $condition = $stateMachineHandler->getConditions()[$conditionString];

        return new $condition();
    }

    /**
     * @param string $conditionString
     * @param \StateMachine\Dependency\StateMachineHandlerInterface $stateMachineHandler
     *
     * @throws \StateMachine\Business\Exception\ConditionNotFoundException
     *
     * @return void
     */
    protected function assertConditionIsSet(string $conditionString, StateMachineHandlerInterface $stateMachineHandler): void
    {
        if (!isset($stateMachineHandler->getConditions()[$conditionString])) {
            throw new ConditionNotFoundException(
                sprintf(
                    'Condition "%s" not registered in "%s" class. Please add it to getConditions() method.',
                    $conditionString,
                    get_class($this->stateMachineHandlerResolver),
                ),
            );
        }
    }

    /**
     * @param array<\StateMachine\Dto\StateMachine\ItemDto> $stateMachineItems
     *
     * @return array<string>
     */
    protected function createStateMap(array $stateMachineItems): array
    {
        $sourceStates = [];
        foreach ($stateMachineItems as $itemDto) {
            $sourceStates[$itemDto->getIdentifierOrFail()] = $itemDto->getStateNameOrFail();
        }

        return $sourceStates;
    }
}
