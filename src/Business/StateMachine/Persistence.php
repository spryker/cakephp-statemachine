<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use DateTime;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Model\Entity\StateMachineItemState;
use StateMachine\Model\Entity\StateMachineItemStateHistory;
use StateMachine\Model\Entity\StateMachineProcess;
use StateMachine\Model\Entity\StateMachineTimeout;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

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
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getStateHistoryByStateItemIdentifier(string $itemIdentifier, int $idStateMachineProcess): array
    {
        /**
         * @var \StateMachine\Model\Entity\StateMachineItemStateHistory[] $stateMachineHistoryItems
         */
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
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return int
     */
    public function getProcessId(StateMachineProcessTransfer $stateMachineProcessTransfer): int
    {
        $stateMachineProcessTransfer->requireProcessName();

        if (array_key_exists($stateMachineProcessTransfer->getProcessName(), $this->processEntityBuffer)) {
            return $this->processEntityBuffer[$stateMachineProcessTransfer->getProcessName()]->id;
        }

        $stateMachineProcessEntity = $this->stateMachineQueryContainer
            ->queryProcessByProcessName(
                $stateMachineProcessTransfer->getProcessName()
            )->first();

        if ($stateMachineProcessEntity === null) {
            $stateMachineProcessEntity = $this->saveStateMachineProcess($stateMachineProcessTransfer);
        }

        $this->processEntityBuffer[$stateMachineProcessTransfer->getProcessName()] = $stateMachineProcessEntity;

        return $stateMachineProcessEntity->id;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return int
     */
    public function getInitialStateIdByStateName(StateMachineItemTransfer $stateMachineItemTransfer, string $stateName): int
    {
        $stateMachineItemTransfer = $this->saveStateMachineItem($stateMachineItemTransfer, $stateName);

        return $stateMachineItemTransfer->getIdItemState();
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    public function saveStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer, string $stateName): StateMachineItemTransfer
    {
        if (isset($this->persistedStates[$stateName])) {
            $stateMachineItemStateEntity = $this->persistedStates[$stateName];
        } else {
            $stateMachineItemTransfer->requireIdStateMachineProcess();

            /** @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemStateEntity */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryItemStateByIdProcessAndStateName(
                    $stateMachineItemTransfer->getIdStateMachineProcess(),
                    $stateName
                )->first();

            if ($stateMachineItemStateEntity === null) {
                $this->saveStateMachineItemEntity($stateMachineItemTransfer, $stateName);
                /** @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemStateEntity */
                $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                    ->queryItemStateByIdProcessAndStateName(
                        $stateMachineItemTransfer->getIdStateMachineProcess(),
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function saveItemStateHistory(StateMachineItemTransfer $stateMachineItemTransfer): void
    {
        $stateMachineItemStateHistory = $this->stateMachineItemStateHistoryTable->newEntity();
        $stateMachineItemStateHistory->identifier = $stateMachineItemTransfer->getIdentifier();
        $stateMachineItemStateHistory->state_machine_item_state_id = $stateMachineItemTransfer->getIdItemState();
        $this->stateMachineItemStateHistoryTable->saveOrFail($stateMachineItemStateHistory);
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function updateStateMachineItemsFromPersistence(array $stateMachineItems): array
    {
        $updatedStateMachineItems = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $stateMachineItemTransfer->requireIdentifier()
                ->requireIdItemState();

            /**
             * @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemStateEntity
             */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryStateByIdState(
                    $stateMachineItemTransfer->getIdItemState()
                )->first();

            if ($stateMachineItemStateEntity === null) {
                continue;
            }

            $updatedStateMachineItemTransfer = $this->hydrateItemTransferFromEntity($stateMachineItemStateEntity);

            $updatedStateMachineItems[] = $stateMachineItemTransfer->fromArray(
                $updatedStateMachineItemTransfer->modifiedToArray(),
                true
            );
        }

        return $updatedStateMachineItems;
    }

    /**
     * @param \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemStateEntity
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected function hydrateItemTransferFromEntity(StateMachineItemState $stateMachineItemStateEntity): StateMachineItemTransfer
    {
        $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;
        $stateMachineItemTransfer = new StateMachineItemTransfer();
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getProcessedStateMachineItems(array $stateMachineItems): array
    {
        $updatedStateMachineItems = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $stateMachineItemTransfer->requireIdItemState()
                ->requireIdentifier();

            $updatedStateMachineItemTransfer = $this->getProcessedStateMachineItemTransfer($stateMachineItemTransfer);
            $updatedStateMachineItems[] = $stateMachineItemTransfer->fromArray(
                $updatedStateMachineItemTransfer->modifiedToArray(),
                true
            );
        }

        return $updatedStateMachineItems;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    public function getProcessedStateMachineItemTransfer(StateMachineItemTransfer $stateMachineItemTransfer): StateMachineItemTransfer
    {
        $stateMachineItemTransfer->requireIdentifier()
            ->requireIdItemState();

        /**
         * @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemStateEntity
         */
        $stateMachineItemStateEntity = $this->stateMachineQueryContainer
            ->queryItemsWithExistingHistory($stateMachineItemTransfer)
            ->first();

        if ($stateMachineItemStateEntity === null) {
            throw new StateMachineException('State machine item not found.');
        }

        $stateMachineProcessEntity = $stateMachineItemStateEntity->state_machine_process;

        $updatedStateMachineItemTransfer = new StateMachineItemTransfer();
        $updatedStateMachineItemTransfer->setIdentifier($stateMachineItemTransfer->getIdentifier());
        $updatedStateMachineItemTransfer->setStateName($stateMachineItemStateEntity->name);
        $updatedStateMachineItemTransfer->setIdItemState($stateMachineItemStateEntity->id);
        $updatedStateMachineItemTransfer->setIdStateMachineProcess($stateMachineProcessEntity->id);
        $updatedStateMachineItemTransfer->setProcessName($stateMachineProcessEntity->name);
        $updatedStateMachineItemTransfer->setStateMachineName($stateMachineProcessEntity->state_machine);

        return $updatedStateMachineItemTransfer;
    }

    /**
     * @param string $processName
     * @param string $stateMachineName
     * @param string[] $states
     *
     * @return int[]
     */
    public function getStateMachineItemIdsByStatesProcessAndStateMachineName(
        $processName,
        $stateMachineName,
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
        /**
         * @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemEntity
         */
        foreach ($stateMachineStateItems as $stateMachineItemEntity) {
            $stateMachineItemStateIds[] = $stateMachineItemEntity->id;
        }

        return $stateMachineItemStateIds;
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[] $expiredStateMachineItemsTransfer
     */
    public function getItemsWithExpiredTimeouts($stateMachineName): array
    {
        /**
         * @var \StateMachine\Model\Entity\StateMachineTimeout[] $stateMachineExpiredItems
         */
        $stateMachineExpiredItems = $this->stateMachineQueryContainer
            ->queryItemsWithExpiredTimeout(
                new DateTime('now'),
                $stateMachineName
            )->all();

        $expiredStateMachineItemsTransfer = [];
        foreach ($stateMachineExpiredItems as $stateMachineEventTimeoutEntity) {
            $stateMachineItemTransfer = new StateMachineItemTransfer();
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param \DateTime $timeoutDate
     * @param string $eventName
     *
     * @return \StateMachine\Model\Entity\StateMachineTimeout
     */
    public function saveStateMachineItemTimeout(
        StateMachineItemTransfer $stateMachineItemTransfer,
        DateTime $timeoutDate,
        $eventName
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function dropTimeoutByItem(StateMachineItemTransfer $stateMachineItemTransfer): void
    {
        $this->stateMachineQueryContainer
            ->queryEventTimeoutByIdentifierAndFkProcess(
                $stateMachineItemTransfer->getIdentifier(),
                $stateMachineItemTransfer->getIdStateMachineProcess()
            )->delete();
    }

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return \StateMachine\Model\Entity\StateMachineProcess
     */
    protected function saveStateMachineProcess(StateMachineProcessTransfer $stateMachineProcessTransfer): StateMachineProcess
    {
        $stateMachineProcessEntity = $this->stateMachineProcessesTable->newEntity();
        $stateMachineProcessEntity->name = $stateMachineProcessTransfer->getProcessName();
        $stateMachineProcessEntity->state_machine = $stateMachineProcessTransfer->getStateMachineName();

        $this->stateMachineProcessesTable->saveOrFail($stateMachineProcessEntity);

        return $stateMachineProcessEntity;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \StateMachine\Model\Entity\StateMachineItemState
     */
    protected function saveStateMachineItemEntity(StateMachineItemTransfer $stateMachineItemTransfer, string $stateName): StateMachineItemState
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
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected function createItemTransferForStateHistory(
        string $itemIdentifier,
        StateMachineItemStateHistory $stateMachineItemHistoryEntity
    ): StateMachineItemTransfer {
        $itemStateEntity = $stateMachineItemHistoryEntity->state_machine_item_state;
        $processEntity = $itemStateEntity->state_machine_process;

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setIdentifier($itemIdentifier);
        $stateMachineItemTransfer->setStateName($itemStateEntity->name);
        $stateMachineItemTransfer->setIdItemState($itemStateEntity->id);
        $stateMachineItemTransfer->setIdStateMachineProcess($processEntity->id);
        $stateMachineItemTransfer->setStateMachineName($processEntity->state_machine);
        $stateMachineItemTransfer->setCreatedAt($stateMachineItemHistoryEntity->created);

        return $stateMachineItemTransfer;
    }
}
