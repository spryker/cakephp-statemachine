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
     * @param string $itemIdentifier
     * @param int $idStateMachineProcess
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateHistoryByStateItemIdentifier(string $itemIdentifier, int $idStateMachineProcess): array
    {
        /** @var \StateMachine\Model\Entity\StateMachineItemStateHistory[] $stateMachineHistoryItems */
        $stateMachineHistoryItems = $this->stateMachineQueryContainer
            ->queryItemHistoryByStateItemIdentifier($itemIdentifier, $idStateMachineProcess)
            ->all();

        $stateMachineItems = [];
        foreach ($stateMachineHistoryItems as $stateMachineHistoryItemEntity) {
            $stateMachineItemTransfer = $this->createItemTransferForStateHistory(
                $itemIdentifier,
                $stateMachineHistoryItemEntity
            );

            $stateMachineItems[] = $stateMachineItemTransfer;
        }

        return $stateMachineItems;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     *
     * @return int
     */
    public function getProcessId(ProcessDto $stateMachineProcessTransfer): int
    {
        $processName = $stateMachineProcessTransfer->getProcessNameOrFail();
        if (array_key_exists($processName, $this->processEntityBuffer)) {
            return $this->processEntityBuffer[$processName]->id;
        }

        /** @var \StateMachine\Model\Entity\StateMachineProcess|null $stateMachineProcessEntity */
        $stateMachineProcessEntity = $this->stateMachineQueryContainer
            ->queryProcessByProcessName(
                $processName
            )->first();

        if ($stateMachineProcessEntity === null) {
            $stateMachineProcessEntity = $this->saveStateMachineProcess($stateMachineProcessTransfer);
        }

        $this->processEntityBuffer[$processName] = $stateMachineProcessEntity;

        return $stateMachineProcessEntity->id;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return int
     */
    public function getInitialStateIdByStateName(ItemDto $stateMachineItemTransfer, string $stateName): int
    {
        $stateMachineItemTransfer = $this->saveStateMachineItem($stateMachineItemTransfer, $stateName);

        return $stateMachineItemTransfer->getIdItemState();
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function saveStateMachineItem(ItemDto $stateMachineItemTransfer, string $stateName): ItemDto
    {
        if (isset($this->persistedStates[$stateName])) {
            $stateMachineItemStateEntity = $this->persistedStates[$stateName];
        } else {
            /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryItemStateByIdProcessAndStateName(
                    $stateMachineItemTransfer->getIdStateMachineProcessOrFail(),
                    $stateName
                )->first();

            if ($stateMachineItemStateEntity === null) {
                $this->saveStateMachineItemEntity($stateMachineItemTransfer, $stateName);
                /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
                $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                    ->queryItemStateByIdProcessAndStateName(
                        $stateMachineItemTransfer->getIdStateMachineProcessOrFail(),
                        $stateName
                    )->first();
            }
            $this->persistedStates[$stateName] = $stateMachineItemStateEntity;
        }

        $stateMachineItemTransfer->setIdItemState($stateMachineItemStateEntity->id);
        $stateMachineItemTransfer->setStateName($stateMachineItemStateEntity->name);
        $stateMachineItemTransfer->setStateMachineName(
            $stateMachineItemStateEntity->state_machine_process->state_machine
        );

        return $stateMachineItemTransfer;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return void
     */
    public function saveItemStateHistory(ItemDto $stateMachineItemTransfer): void
    {
        $stateMachineItemStateHistory = $this->stateMachineItemStateHistoryTable->newEntity();
        $stateMachineItemStateHistory->identifier = $stateMachineItemTransfer->getIdentifier();
        $stateMachineItemStateHistory->state_machine_item_state_id = $stateMachineItemTransfer->getIdItemState();
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
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryStateByIdState(
                    $stateMachineItemTransfer->getIdItemState()
                )->first();

            if ($stateMachineItemStateEntity === null) {
                continue;
            }

            $updatedItemDto = $this->hydrateItemTransferFromEntity($stateMachineItemStateEntity);

            $updatedStateMachineItems[] = $stateMachineItemTransfer->fromArray(
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
        $stateMachineItemTransfer = new ItemDto();
        $stateMachineItemTransfer->setStateName($stateMachineItemStateEntity->name);
        $stateMachineItemTransfer->setIdItemState($stateMachineItemStateEntity->id);
        $stateMachineItemTransfer->setIdStateMachineProcess(
            $stateMachineProcessEntity->id
        );
        $stateMachineItemTransfer->setProcessName($stateMachineProcessEntity->name);
        $stateMachineItemTransfer->setStateMachineName($stateMachineProcessEntity->state_machine);

        return $stateMachineItemTransfer;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getProcessedStateMachineItems(array $stateMachineItems): array
    {
        $updatedStateMachineItems = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $updatedItemDto = $this->getProcessedItemDto($stateMachineItemTransfer);
            $updatedStateMachineItems[] = $stateMachineItemTransfer->fromArray(
                $updatedItemDto->touchedToArray(),
                true
            );
        }

        return $updatedStateMachineItems;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getProcessedItemDto(ItemDto $stateMachineItemTransfer): ItemDto
    {
        /** @var \StateMachine\Model\Entity\StateMachineItemState|null $stateMachineItemStateEntity */
        $stateMachineItemStateEntity = $this->stateMachineQueryContainer
            ->queryItemsWithExistingHistory($stateMachineItemTransfer)
            ->first();

        if ($stateMachineItemStateEntity === null) {
            throw new StateMachineException('State machine item not found.');
        }

        $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;

        $updatedItemDto = new ItemDto();
        $updatedItemDto->setIdentifier($stateMachineItemTransfer->getIdentifierOrFail());
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
    public function getItemsWithExpiredTimeouts($stateMachineName): array
    {
        /** @var \StateMachine\Model\Entity\StateMachineTimeout[] $stateMachineExpiredItems */
        $stateMachineExpiredItems = $this->stateMachineQueryContainer
            ->queryItemsWithExpiredTimeout(
                new FrozenTime('now'),
                $stateMachineName
            )->all();

        $expiredStateMachineItemsTransfer = [];
        foreach ($stateMachineExpiredItems as $stateMachineEventTimeoutEntity) {
            $stateMachineItemTransfer = new ItemDto();
            $stateMachineItemTransfer->setEventName($stateMachineEventTimeoutEntity->event);
            $stateMachineItemTransfer->setIdentifier($stateMachineEventTimeoutEntity->identifier);

            $stateMachineItemStateEntity = $stateMachineEventTimeoutEntity->state_machine_item_state;
            $stateMachineItemTransfer->setIdItemState($stateMachineItemStateEntity->id);
            $stateMachineItemTransfer->setStateName($stateMachineItemStateEntity->name);

            $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;
            $stateMachineItemTransfer->setProcessName($stateMachineProcessEntity->name);
            $stateMachineItemTransfer->setIdStateMachineProcess($stateMachineProcessEntity->id);
            $stateMachineItemTransfer->setStateMachineName($stateMachineProcessEntity->state_machine);

            $expiredStateMachineItemsTransfer[] = $stateMachineItemTransfer;
        }

        return $expiredStateMachineItemsTransfer;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param \Cake\I18n\FrozenTime $timeoutDate
     * @param string $eventName
     *
     * @return \StateMachine\Model\Entity\StateMachineTimeout
     */
    public function saveStateMachineItemTimeout(
        ItemDto $stateMachineItemTransfer,
        FrozenTime $timeoutDate,
        string $eventName
    ): StateMachineTimeout {
        $stateMachineItemTimeoutEntity = $this->stateMachineTimeoutsTable->newEntity();
        $stateMachineItemTimeoutEntity->timeout = $timeoutDate;
        $stateMachineItemTimeoutEntity->identifier = $stateMachineItemTransfer->getIdentifier();
        $stateMachineItemTimeoutEntity->state_machine_item_state_id = $stateMachineItemTransfer->getIdItemState();
        $stateMachineItemTimeoutEntity->state_machine_process_id = $stateMachineItemTransfer->getIdStateMachineProcess();
        $stateMachineItemTimeoutEntity->event = $eventName;

        $this->stateMachineTimeoutsTable->saveOrFail($stateMachineItemTimeoutEntity);

        return $stateMachineItemTimeoutEntity;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return void
     */
    public function dropTimeoutByItem(ItemDto $stateMachineItemTransfer): void
    {
        $this->stateMachineQueryContainer
            ->queryEventTimeoutByIdentifierAndFkProcess(
                $stateMachineItemTransfer->getIdentifier(),
                $stateMachineItemTransfer->getIdStateMachineProcess()
            )->delete();
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     *
     * @return \StateMachine\Model\Entity\StateMachineProcess
     */
    protected function saveStateMachineProcess(ProcessDto $stateMachineProcessTransfer): StateMachineProcess
    {
        $stateMachineProcessEntity = $this->stateMachineProcessesTable->newEntity();
        $stateMachineProcessEntity->name = $stateMachineProcessTransfer->getProcessName();
        $stateMachineProcessEntity->state_machine = $stateMachineProcessTransfer->getStateMachineName();

        $this->stateMachineProcessesTable->saveOrFail($stateMachineProcessEntity);

        return $stateMachineProcessEntity;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \StateMachine\Model\Entity\StateMachineItemState
     */
    protected function saveStateMachineItemEntity(ItemDto $stateMachineItemTransfer, string $stateName): StateMachineItemState
    {
        $stateMachineItemStateEntity = $this->stateMachineItemStatesTable->newEntity();
        $stateMachineItemStateEntity->name = $stateName;
        $stateMachineItemStateEntity->state_machine_process_id = $stateMachineItemTransfer->getIdStateMachineProcess();

        $this->stateMachineItemStatesTable->saveOrFail($stateMachineItemStateEntity);

        return $stateMachineItemStateEntity;
    }

    /**
     * @param string $itemIdentifier
     * @param \StateMachine\Model\Entity\StateMachineItemStateHistory $stateMachineItemHistoryEntity
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    protected function createItemTransferForStateHistory(
        string $itemIdentifier,
        StateMachineItemStateHistory $stateMachineItemHistoryEntity
    ): ItemDto {
        $itemStateEntity = $stateMachineItemHistoryEntity->state_machine_item_state;
        $processEntity = $itemStateEntity->state_machine_process;

        $stateMachineItemTransfer = new ItemDto();
        $stateMachineItemTransfer->setIdentifier($itemIdentifier);
        $stateMachineItemTransfer->setStateName($itemStateEntity->name);
        $stateMachineItemTransfer->setIdItemState($itemStateEntity->id);
        $stateMachineItemTransfer->setIdStateMachineProcess($processEntity->id);
        $stateMachineItemTransfer->setStateMachineName($processEntity->state_machine);
        $stateMachineItemTransfer->setCreatedAt($stateMachineItemHistoryEntity->created);

        return $stateMachineItemTransfer;
    }
}
