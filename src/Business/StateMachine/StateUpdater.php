<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

class StateUpdater implements StateUpdaterInterface
{
    /**
     * @var \StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected $timeout;

    /**
     * @var \StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @var \StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected $stateMachinePersistence;

    /**
     * @var \StateMachine\Model\QueryContainerInterface
     */
    protected $stateMachineQueryContainer;

    /**
     * @param \StateMachine\Business\StateMachine\TimeoutInterface $timeout
     * @param \StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     * @param \StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     * @param \StateMachine\Model\QueryContainerInterface $stateMachineQueryContainer
     */
    public function __construct(
        TimeoutInterface $timeout,
        HandlerResolverInterface $stateMachineHandlerResolver,
        PersistenceInterface $stateMachinePersistence,
        QueryContainerInterface $stateMachineQueryContainer
    ) {
        $this->timeout = $timeout;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->stateMachineQueryContainer = $stateMachineQueryContainer;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string[] $sourceStates
     *
     * @return void
     */
    public function updateStateMachineItemState(
        array $stateMachineItems,
        array $processes,
        array $sourceStates
    ) {

        if (count($stateMachineItems) === 0) {
            return;
        }

        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $this->executeUpdateItemStateTransaction($processes, $sourceStates, $stateMachineItemTransfer);
        }
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string[] $sourceStates
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function executeUpdateItemStateTransaction(
        array $processes,
        array $sourceStates,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        $this->assertStateMachineItemHaveRequiredData($stateMachineItemTransfer);

        $process = $processes[$stateMachineItemTransfer->getProcessName()];

        $this->assertSourceStateExists($sourceStates, $stateMachineItemTransfer);

        $sourceState = $sourceStates[$stateMachineItemTransfer->getIdentifier()];
        $targetState = $stateMachineItemTransfer->getStateName();

        $this->transitionState($sourceState, $targetState, $stateMachineItemTransfer, $process);
    }

    /**
     * @param array $sourceStateBuffer
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function assertSourceStateExists(
        array $sourceStateBuffer,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        if (!isset($sourceStateBuffer[$stateMachineItemTransfer->getIdentifier()])) {
            throw new StateMachineException(
                sprintf('Could not update state, source state not found.')
            );
        }
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function assertStateMachineItemHaveRequiredData(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineItemTransfer->requireProcessName()
            ->requireStateMachineName()
            ->requireIdentifier()
            ->requireStateName();
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function notifyHandlerStateChanged(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineItemTransfer->getStateMachineName());

        $stateMachineHandler->itemStateUpdated($stateMachineItemTransfer);
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string $sourceState
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function updateTimeouts(
        ProcessInterface $process,
        $sourceState,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        $this->timeout->dropOldTimeout($process, $sourceState, $stateMachineItemTransfer);
        $this->timeout->setNewTimeout($process, $stateMachineItemTransfer);
    }

    /**
     * @param string $sourceState
     * @param string $targetState
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return void
     */
    protected function transitionState(
        $sourceState,
        $targetState,
        StateMachineItemTransfer $stateMachineItemTransfer,
        ProcessInterface $process
    ) {
        if ($sourceState === $targetState) {
            return;
        }
        $this->updateTimeouts($process, $sourceState, $stateMachineItemTransfer);
        $this->notifyHandlerStateChanged($stateMachineItemTransfer);
        $this->stateMachinePersistence->saveItemStateHistory($stateMachineItemTransfer);
    }
}
