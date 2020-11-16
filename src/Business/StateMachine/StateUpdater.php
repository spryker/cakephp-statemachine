<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\QueryContainerInterface;

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
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string[] $sourceStates
     *
     * @return void
     */
    public function updateStateMachineItemState(
        array $stateMachineItems,
        array $processes,
        array $sourceStates
    ): void {
        if (count($stateMachineItems) === 0) {
            return;
        }

        foreach ($stateMachineItems as $itemDto) {
            $this->executeUpdateItemStateTransaction($processes, $sourceStates, $itemDto);
        }
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string[] $sourceStates
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    protected function executeUpdateItemStateTransaction(
        array $processes,
        array $sourceStates,
        ItemDto $itemDto
    ): void {
        $process = $processes[$itemDto->getProcessNameOrFail()];

        $this->assertSourceStateExists($sourceStates, $itemDto);

        $sourceState = $sourceStates[$itemDto->getIdentifierOrFail()];
        $targetState = $itemDto->getStateNameOrFail();

        $this->transitionState($sourceState, $targetState, $itemDto, $process);
    }

    /**
     * @param array $sourceStateBuffer
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function assertSourceStateExists(
        array $sourceStateBuffer,
        ItemDto $itemDto
    ): void {
        if (!isset($sourceStateBuffer[$itemDto->getIdentifierOrFail()])) {
            throw new StateMachineException(
                sprintf('Could not update state, source state not found for identifier ' . $itemDto->getIdentifierOrFail() . '.')
            );
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    protected function notifyHandlerStateChanged(ItemDto $itemDto): void
    {
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($itemDto->getStateMachineNameOrFail());

        $stateMachineHandler->itemStateUpdated($itemDto);
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string $sourceState
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    protected function updateTimeouts(
        ProcessInterface $process,
        string $sourceState,
        ItemDto $itemDto
    ): void {
        $this->timeout->dropOldTimeout($process, $sourceState, $itemDto);
        $this->timeout->setNewTimeout($process, $itemDto);
    }

    /**
     * @param string $sourceState
     * @param string $targetState
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return void
     */
    protected function transitionState(
        string $sourceState,
        string $targetState,
        ItemDto $itemDto,
        ProcessInterface $process
    ): void {
        if ($sourceState === $targetState) {
            return;
        }
        $this->updateTimeouts($process, $sourceState, $itemDto);
        $this->notifyHandlerStateChanged($itemDto);
        $this->stateMachinePersistence->saveItemStateHistory($itemDto);
    }
}
