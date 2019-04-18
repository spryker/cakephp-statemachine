<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use Cake\ORM\ResultSet;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\Model\Entity\StateMachineItemState;
use StateMachine\Model\Entity\StateMachineProcess;
use StateMachine\Model\QueryContainerInterface;

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
    public function hasHandler(string $stateMachineName): bool
    {
        return $this->stateMachineHandlerResolver->find($stateMachineName) !== null;
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto[]
     */
    public function getProcesses(string $stateMachineName): array
    {
        $processes = [];
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);
        foreach ($stateMachineHandler->getActiveProcesses() as $processName) {
            $processes[$processName] = $this->createProcessDto($stateMachineName, $processName);
        }

        return $processes;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string[][]
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems): array
    {
        $itemsWithManualEvents = [];
        foreach ($stateMachineItems as $itemDto) {
            $manualEvents = $this->getManualEventsForStateMachineItem($itemDto);

            if (count($manualEvents) > 0) {
                $itemsWithManualEvents[$itemDto->getIdentifierOrFail()] = $manualEvents;
            }
        }

        return $itemsWithManualEvents;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return string[]
     */
    public function getManualEventsForStateMachineItem(ItemDto $itemDto): array
    {
        $processName = $itemDto->getProcessNameOrFail();

        $processBuilder = clone $this->builder;

        $processDto = $this->createProcessDto(
            $itemDto->getStateMachineNameOrFail(),
            $processName
        );

        $process = $processBuilder->createProcess($processDto);
        $manualEvents = $process->getManuallyExecutableEventsBySource();
        $stateName = $itemDto->getStateNameOrFail();

        if (!isset($manualEvents[$stateName])) {
            return [];
        }

        return $manualEvents[$stateName];
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $itemDto
     */
    public function getItemsWithFlag(ProcessDto $processDto, string $flag): array
    {
        return $this->getItemsByFlag($processDto, $flag, true);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $itemDto
     */
    public function getItemsWithoutFlag(ProcessDto $processDto, string $flag): array
    {
        return $this->getItemsByFlag($processDto, $flag, false);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flagName
     * @param bool $hasFlag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    protected function getItemsByFlag(ProcessDto $processDto, string $flagName, bool $hasFlag): array
    {
        $statesByFlag = $this->getStatesByFlag($processDto, $flagName, $hasFlag);
        if (count($statesByFlag) === 0) {
            return [];
        }

        $stateMachineProcessEntity = $this->findStateMachineProcessEntity($processDto);
        if ($stateMachineProcessEntity === null) {
            return [];
        }

        $stateMachineItems = $this->getFlaggedStateMachineItems(
            $processDto,
            array_keys($statesByFlag)
        );

        $stateMachineItemsWithFlag = [];
        foreach ($stateMachineItems as $stateMachineItemEntity) {
            $itemDto = $this->createStateMachineHistoryItemTransfer(
                $processDto,
                $stateMachineItemEntity,
                $stateMachineProcessEntity
            );

            $stateMachineItemsWithFlag[] = $itemDto;
        }

        return $stateMachineItemsWithFlag;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flag
     * @param bool $hasFlag
     *
     * @return \StateMachine\Business\Process\StateInterface[]
     */
    protected function getStatesByFlag(ProcessDto $processDto, string $flag, bool $hasFlag): array
    {
        $selectedStates = [];

        $processStateList = $this->builder->createProcess($processDto)->getAllStates();

        foreach ($processStateList as $state) {
            if ($hasFlag !== $state->hasFlag($flag)) {
                continue;
            }
            $selectedStates[$state->getName()] = $state;
        }

        return $selectedStates;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param array $sourceStates
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[][]
     */
    public function filterItemsWithOnEnterEvent(
        array $stateMachineItems,
        array $processes,
        array $sourceStates = []
    ): array {
        $itemsWithOnEnterEvent = [];
        foreach ($stateMachineItems as $itemDto) {
            $stateName = $itemDto->getStateNameOrFail();
            $processName = $itemDto->getProcessNameOrFail();

            $this->assertProcessExists($processes, $processName);

            $process = $processes[$processName];
            $targetState = $process->getStateFromAllProcesses($stateName);

            if (isset($sourceStates[$itemDto->getIdentifierOrFail()])) {
                $sourceState = $sourceStates[$itemDto->getIdentifierOrFail()];
            } else {
                $sourceState = $process->getStateFromAllProcesses($itemDto->getStateName());
            }

            if ($sourceState !== $targetState->getName() && $targetState->hasOnEnterEvent()) {
                $eventName = $targetState->getOnEnterEvent()->getName();
                if (array_key_exists($eventName, $itemsWithOnEnterEvent) === false) {
                    $itemsWithOnEnterEvent[$eventName] = [];
                }
                $itemsWithOnEnterEvent[$eventName][] = $itemDto;
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
    public function findProcessByStateMachineAndProcessName(string $stateMachineName, string $processName): ProcessInterface
    {
        $processDto = $this->createProcessDto($stateMachineName, $processName);
        return $this->builder->createProcess($processDto);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function findProcessesForItems(array $stateMachineItems): array
    {
        $processes = [];
        foreach ($stateMachineItems as $itemDto) {
            $processName = $itemDto->getProcessNameOrFail();
            if (isset($processes[$processName])) {
                continue;
            }

            $processes[$itemDto->getProcessName()] = $this->findProcessByStateMachineAndProcessName(
                $itemDto->getStateMachineName(),
                $itemDto->getProcessName()
            );
        }

        return $processes;
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto
     */
    protected function createProcessDto(string $stateMachineName, string $processName): ProcessDto
    {
        $processDto = new ProcessDto();
        $processDto->setStateMachineName($stateMachineName);
        $processDto->setProcessName($processName);

        return $processDto;
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string $processName
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function assertProcessExists(array $processes, string $processName): void
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
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemEntity
     * @param \StateMachine\Model\Entity\StateMachineProcess $stateMachineProcessEntity
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    protected function createStateMachineHistoryItemTransfer(
        ProcessDto $processDto,
        StateMachineItemState $stateMachineItemEntity,
        StateMachineProcess $stateMachineProcessEntity
    ): ItemDto {
        $itemDto = new ItemDto();
        $itemDto->setProcessName($processDto->getProcessName());
        $itemDto->setIdItemState($stateMachineItemEntity->id);
        $itemDto->setIdStateMachineProcess($stateMachineProcessEntity->id);
        $itemDto->setStateName($stateMachineItemEntity->name);
        $itemDto->setStateMachineName($stateMachineProcessEntity->state_machine);

        $stateMachineItemHistoryEntities = $stateMachineItemEntity->state_machine_item_state_history;
        if (count($stateMachineItemHistoryEntities) > 0) {
            $itemIdentifier = $stateMachineItemHistoryEntities[0]->identifier;
            $itemDto->setIdentifier($itemIdentifier);
        }

        return $itemDto;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return \StateMachine\Model\Entity\StateMachineProcess|null
     */
    protected function findStateMachineProcessEntity(ProcessDto $processDto): ?StateMachineProcess
    {
        /** @var \StateMachine\Model\Entity\StateMachineProcess|null $stateMachineProcess */
        $stateMachineProcess = $this->queryContainer->queryProcessByStateMachineAndProcessName(
            $processDto->getStateMachineName(),
            $processDto->getProcessName()
        )->first();

        return $stateMachineProcess;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param array $statesByFlag
     *
     * @return \StateMachine\Model\Entity\StateMachineItemState[]|\Cake\ORM\ResultSet
     */
    protected function getFlaggedStateMachineItems(ProcessDto $processDto, array $statesByFlag): ResultSet
    {
        /** @var \StateMachine\Model\Entity\StateMachineItemState[]|\Cake\ORM\ResultSet $itemStateCollection */
        $itemStateCollection = $this->queryContainer->queryItemsByIdStateMachineProcessAndItemStates(
            $processDto->getStateMachineName(),
            $processDto->getProcessName(),
            $statesByFlag
        )->all();

        return $itemStateCollection;
    }
}
