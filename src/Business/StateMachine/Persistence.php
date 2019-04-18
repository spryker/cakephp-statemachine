<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use Cake\I18n\FrozenTime;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\Model\Entity\StateMachineItemState;
use StateMachine\Model\Entity\StateMachineItemStateHistory;
use StateMachine\Model\Entity\StateMachineProcess;
use StateMachine\Model\Entity\StateMachineTimeout;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;

class Persistence implements PersistenceInterface
{
    /**
     * @var \StateMachine\Model\Entity\StateMachineProcess[]
     */
    protected $processEntityBuffer = [];

    /**
     * @var \StateMachine\Model\Entity\StateMachineItemState[]
     */
    protected $persistedStates;

    /**
     * @var \StateMachine\Model\QueryContainerInterface $stateMachineQueryContainer
     */
    protected $stateMachineQueryContainer;

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStateHistoryTable
     */
    protected $stateMachineItemStateHistoryTable;

    /**
     * @var \StateMachine\Model\Table\StateMachineProcessesTable
     */
    protected $stateMachineProcessesTable;

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStatesTable
     */
    protected $stateMachineItemStatesTable;

    /**
     * @var \StateMachine\Model\Table\StateMachineTimeoutsTable
     */
    protected $stateMachineTimeoutsTable;

    /**
     * @param \StateMachine\Model\QueryContainerInterface $stateMachineQueryContainer
     * @param \StateMachine\Model\Table\StateMachineItemStateHistoryTable $stateMachineItemStateHistoryTable
     * @param \StateMachine\Model\Table\StateMachineProcessesTable $stateMachineProcessesTable
     * @param \StateMachine\Model\Table\StateMachineItemStatesTable $stateMachineItemStatesTable
     * @param \StateMachine\Model\Table\StateMachineTimeoutsTable $stateMachineTimeoutsTable
     */
    public function __construct(
        QueryContainerInterface $stateMachineQueryContainer,
        StateMachineItemStateHistoryTable $stateMachineItemStateHistoryTable,
        StateMachineProcessesTable $stateMachineProcessesTable,
        StateMachineItemStatesTable $stateMachineItemStatesTable,
        StateMachineTimeoutsTable $stateMachineTimeoutsTable
    ) {
        $this->stateMachineQueryContainer = $stateMachineQueryContainer;
        $this->stateMachineItemStateHistoryTable = $stateMachineItemStateHistoryTable;
        $this->stateMachineProcessesTable = $stateMachineProcessesTable;
        $this->stateMachineItemStatesTable = $stateMachineItemStatesTable;
        $this->stateMachineTimeoutsTable = $stateMachineTimeoutsTable;
    }

    /**
     * @param int $itemIdentifier
     * @param int $idStateMachineProcess
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateHistoryByStateItemIdentifier(int $itemIdentifier, int $idStateMachineProcess): array
    {
        /** @var \StateMachine\Model\Entity\StateMachineItemStateHistory[] $stateMachineHistoryItems */
        $stateMachineHistoryItems = $this->stateMachineQueryContainer
            ->queryItemHistoryByStateItemIdentifier($itemIdentifier, $idStateMachineProcess)
            ->all();

        $stateMachineItems = [];
        foreach ($stateMachineHistoryItems as $stateMachineHistoryItemEntity) {
            $itemDto = $this->createItemTransferForStateHistory(
                $itemIdentifier,
                $stateMachineHistoryItemEntity
            );

            $stateMachineItems[] = $itemDto;
        }

        return $stateMachineItems;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return int
     */
    public function getProcessId(ProcessDto $processDto): int
    {
        $processName = $processDto->getProcessNameOrFail();
        if (array_key_exists($processName, $this->processEntityBuffer)) {
            return $this->processEntityBuffer[$processName]->id;
        }

        /** @var \StateMachine\Model\Entity\StateMachineProcess|null $stateMachineProcessEntity */
        $stateMachineProcessEntity = $this->stateMachineQueryContainer
            ->queryProcessByProcessName(
                $processName
            )->first();

        if ($stateMachineProcessEntity === null) {
            $stateMachineProcessEntity = $this->saveStateMachineProcess($processDto);
        }

        $this->processEntityBuffer[$processName] = $stateMachineProcessEntity;

        return $stateMachineProcessEntity->id;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $stateName
     *
     * @return int
     */
    public function getInitialStateIdByStateName(ItemDto $itemDto, string $stateName): int
    {
        $itemDto = $this->saveStateMachineItem($itemDto, $stateName);

        return $itemDto->getIdItemState();
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $stateName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function saveStateMachineItem(ItemDto $itemDto, string $stateName): ItemDto
    {
        if (isset($this->persistedStates[$stateName])) {
            $stateMachineItemStateEntity = $this->persistedStates[$stateName];
        } else {
            /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryItemStateByIdProcessAndStateName(
                    $itemDto->getIdStateMachineProcessOrFail(),
                    $stateName
                )->first();

            if ($stateMachineItemStateEntity === null) {
                $this->saveStateMachineItemEntity($itemDto, $stateName);
                /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
                $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                    ->queryItemStateByIdProcessAndStateName(
                        $itemDto->getIdStateMachineProcessOrFail(),
                        $stateName
                    )->first();
            }
            $this->persistedStates[$stateName] = $stateMachineItemStateEntity;
        }

        $itemDto->setIdItemState($stateMachineItemStateEntity->id);
        $itemDto->setStateName($stateMachineItemStateEntity->name);
        $itemDto->setStateMachineName(
            $stateMachineItemStateEntity->state_machine_process->state_machine
        );

        return $itemDto;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    public function saveItemStateHistory(ItemDto $itemDto): void
    {
        $stateMachineItemStateHistory = $this->stateMachineItemStateHistoryTable->newEntity();
        $stateMachineItemStateHistory->identifier = $itemDto->getIdentifier();
        $stateMachineItemStateHistory->state_machine_item_state_id = $itemDto->getIdItemState();
        $this->stateMachineItemStateHistoryTable->saveOrFail($stateMachineItemStateHistory);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function updateStateMachineItemsFromPersistence(array $stateMachineItems): array
    {
        $updatedStateMachineItems = [];
        foreach ($stateMachineItems as $itemDto) {
            /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryStateByIdState(
                    $itemDto->getIdItemState()
                )->first();

            if ($stateMachineItemStateEntity === null) {
                continue;
            }

            $updatedItemDto = $this->hydrateItemTransferFromEntity($stateMachineItemStateEntity);

            $updatedStateMachineItems[] = $itemDto->fromArray(
                $updatedItemDto->touchedToArray(),
                true
            );
        }

        return $updatedStateMachineItems;
    }

    /**
     * @param \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemStateEntity
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    protected function hydrateItemTransferFromEntity(StateMachineItemState $stateMachineItemStateEntity): ItemDto
    {
        $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;
        $itemDto = new ItemDto();
        $itemDto->setStateName($stateMachineItemStateEntity->name);
        $itemDto->setIdItemState($stateMachineItemStateEntity->id);
        $itemDto->setIdStateMachineProcess(
            $stateMachineProcessEntity->id
        );
        $itemDto->setProcessName($stateMachineProcessEntity->name);
        $itemDto->setStateMachineName($stateMachineProcessEntity->state_machine);

        return $itemDto;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getProcessedStateMachineItems(array $stateMachineItems): array
    {
        $updatedStateMachineItems = [];
        foreach ($stateMachineItems as $itemDto) {
            $updatedItemDto = $this->getProcessedItemDto($itemDto);
            $updatedStateMachineItems[] = $itemDto->fromArray(
                $updatedItemDto->touchedToArray(),
                true
            );
        }

        return $updatedStateMachineItems;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getProcessedItemDto(ItemDto $itemDto): ItemDto
    {
        /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
        $stateMachineItemStateEntity = $this->stateMachineQueryContainer
            ->queryItemsWithExistingHistory($itemDto)
            ->first();

        if ($stateMachineItemStateEntity === null) {
            throw new StateMachineException('State machine item not found.');
        }

        $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;

        $updatedItemDto = new ItemDto();
        $updatedItemDto->setIdentifier($itemDto->getIdentifierOrFail());
        $updatedItemDto->setStateName($stateMachineItemStateEntity->name);
        $updatedItemDto->setIdItemState($stateMachineItemStateEntity->id);
        $updatedItemDto->setIdStateMachineProcess($stateMachineProcessEntity->id);
        $updatedItemDto->setProcessName($stateMachineProcessEntity->name);
        $updatedItemDto->setStateMachineName($stateMachineProcessEntity->state_machine);

        return $updatedItemDto;
    }

    /**
     * @param string $processName
     * @param string $stateMachineName
     * @param string[] $states
     *
     * @return int[]
     */
    public function getStateMachineItemIdsByStatesProcessAndStateMachineName(
        string $processName,
        string $stateMachineName,
        array $states
    ): array {
        $stateMachineStateItems = $this->stateMachineQueryContainer
            ->queryItemsByIdStateMachineProcessAndItemStates(
                $stateMachineName,
                $processName,
                $states
            )->all();

        if ($stateMachineStateItems->count() === 0) {
            return [];
        }

        $stateMachineItemStateIds = [];
        /** @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemEntity */
        foreach ($stateMachineStateItems as $stateMachineItemEntity) {
            $stateMachineItemStateIds[] = $stateMachineItemEntity->id;
        }

        return $stateMachineItemStateIds;
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $expiredStateMachineItemsTransfer
     */
    public function getItemsWithExpiredTimeouts(string $stateMachineName): array
    {
        /** @var \StateMachine\Model\Entity\StateMachineTimeout[] $stateMachineExpiredItems */
        $stateMachineExpiredItems = $this->stateMachineQueryContainer
            ->queryItemsWithExpiredTimeout(
                new FrozenTime('now'),
                $stateMachineName
            )->all();

        $expiredStateMachineItemsTransfer = [];
        foreach ($stateMachineExpiredItems as $stateMachineEventTimeoutEntity) {
            $itemDto = new ItemDto();
            $itemDto->setEventName($stateMachineEventTimeoutEntity->event);
            $itemDto->setIdentifier($stateMachineEventTimeoutEntity->identifier);

            $stateMachineItemStateEntity = $stateMachineEventTimeoutEntity->state_machine_item_state;
            $itemDto->setIdItemState($stateMachineItemStateEntity->id);
            $itemDto->setStateName($stateMachineItemStateEntity->name);

            $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;
            $itemDto->setProcessName($stateMachineProcessEntity->name);
            $itemDto->setIdStateMachineProcess($stateMachineProcessEntity->id);
            $itemDto->setStateMachineName($stateMachineProcessEntity->state_machine);

            $expiredStateMachineItemsTransfer[] = $itemDto;
        }

        return $expiredStateMachineItemsTransfer;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param \Cake\I18n\FrozenTime $timeoutDate
     * @param string $eventName
     *
     * @return \StateMachine\Model\Entity\StateMachineTimeout
     */
    public function saveStateMachineItemTimeout(
        ItemDto $itemDto,
        FrozenTime $timeoutDate,
        string $eventName
    ): StateMachineTimeout {
        $stateMachineItemTimeoutEntity = $this->stateMachineTimeoutsTable->newEntity();
        $stateMachineItemTimeoutEntity->timeout = $timeoutDate;
        $stateMachineItemTimeoutEntity->identifier = $itemDto->getIdentifier();
        $stateMachineItemTimeoutEntity->state_machine_item_state_id = $itemDto->getIdItemState();
        $stateMachineItemTimeoutEntity->state_machine_process_id = $itemDto->getIdStateMachineProcess();
        $stateMachineItemTimeoutEntity->event = $eventName;

        $this->stateMachineTimeoutsTable->saveOrFail($stateMachineItemTimeoutEntity);

        return $stateMachineItemTimeoutEntity;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    public function dropTimeoutByItem(ItemDto $itemDto): void
    {
        /*
        //FIXME
        $this->stateMachineQueryContainer
            ->queryEventTimeoutByIdentifierAndFkProcess(
                $itemDto->getIdentifier(),
                $itemDto->getIdStateMachineProcess()
            )->delete();
        */

        /** @var \StateMachine\Model\Entity\StateMachineTimeout[] $stateMachineTimeouts */
        $stateMachineTimeouts = $this->stateMachineQueryContainer
            ->queryEventTimeoutByIdentifierAndFkProcess(
                $itemDto->getIdentifier(),
                $itemDto->getIdStateMachineProcess()
            )->all();
        foreach ($stateMachineTimeouts as $stateMachineTimeout) {
            $this->stateMachineQueryContainer->getFactory()->createStateMachineTimeoutsTable()->deleteOrFail($stateMachineTimeout);
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return \StateMachine\Model\Entity\StateMachineProcess
     */
    protected function saveStateMachineProcess(ProcessDto $processDto): StateMachineProcess
    {
        $stateMachineProcessEntity = $this->stateMachineProcessesTable->newEntity();
        $stateMachineProcessEntity->name = $processDto->getProcessName();
        $stateMachineProcessEntity->state_machine = $processDto->getStateMachineName();

        $this->stateMachineProcessesTable->saveOrFail($stateMachineProcessEntity);

        return $stateMachineProcessEntity;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $stateName
     *
     * @return \StateMachine\Model\Entity\StateMachineItemState
     */
    protected function saveStateMachineItemEntity(ItemDto $itemDto, string $stateName): StateMachineItemState
    {
        $stateMachineItemStateEntity = $this->stateMachineItemStatesTable->newEntity();
        $stateMachineItemStateEntity->name = $stateName;
        $stateMachineItemStateEntity->state_machine_process_id = $itemDto->getIdStateMachineProcess();

        $this->stateMachineItemStatesTable->saveOrFail($stateMachineItemStateEntity);

        return $stateMachineItemStateEntity;
    }

    /**
     * @param int $itemIdentifier
     * @param \StateMachine\Model\Entity\StateMachineItemStateHistory $stateMachineItemHistoryEntity
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    protected function createItemTransferForStateHistory(
        int $itemIdentifier,
        StateMachineItemStateHistory $stateMachineItemHistoryEntity
    ): ItemDto {
        $itemStateEntity = $stateMachineItemHistoryEntity->state_machine_item_state;
        $processEntity = $itemStateEntity->state_machine_process;

        $itemDto = new ItemDto();
        $itemDto->setIdentifier($itemIdentifier);
        $itemDto->setStateName($itemStateEntity->name);
        $itemDto->setIdItemState($itemStateEntity->id);
        $itemDto->setIdStateMachineProcess($processEntity->id);
        $itemDto->setStateMachineName($processEntity->state_machine);
        $itemDto->setCreatedAt($stateMachineItemHistoryEntity->created);

        return $itemDto;
    }
}
