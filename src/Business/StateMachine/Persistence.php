<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use DateTime;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineEventTimeout;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistory;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Model\Entity\StateMachineItemState;
use StateMachine\Model\Entity\StateMachineItemStateHistory;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class Persistence implements PersistenceInterface
{
    /**
     * @var \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess[]
     */
    protected $processEntityBuffer = [];

    /**
     * @var \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState[]
     */
    protected $persistedStates;

    /**
     * @var \StateMachine\Model\QueryContainerInterface $stateMachineQueryContainer
     */
    protected $stateMachineQueryContainer;

    /**
     * @param \StateMachine\Model\QueryContainerInterface $stateMachineQueryContainer
     */
    public function __construct(QueryContainerInterface $stateMachineQueryContainer)
    {
        $this->stateMachineQueryContainer = $stateMachineQueryContainer;
    }

    /**
     * @param int $itemIdentifier
     * @param int $idStateMachineProcess
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getStateHistoryByStateItemIdentifier($itemIdentifier, $idStateMachineProcess)
    {
        /**
         * @var $stateMachineHistoryItems \StateMachine\Model\Entity\StateMachineItemStateHistory[]
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
    public function getProcessId(StateMachineProcessTransfer $stateMachineProcessTransfer)
    {
        $stateMachineProcessTransfer->requireProcessName();

        if (array_key_exists($stateMachineProcessTransfer->getProcessName(), $this->processEntityBuffer)) {
            return $this->processEntityBuffer[$stateMachineProcessTransfer->getProcessName()]
                ->getIdStateMachineProcess();
        }

        $stateMachineProcessEntity = $this->stateMachineQueryContainer
            ->queryProcessByProcessName(
                $stateMachineProcessTransfer->getProcessName()
            )->findOne();

        if ($stateMachineProcessEntity === null) {
            $stateMachineProcessEntity = $this->saveStateMachineProcess($stateMachineProcessTransfer);
        }

        $this->processEntityBuffer[$stateMachineProcessTransfer->getProcessName()] = $stateMachineProcessEntity;

        return $stateMachineProcessEntity->getIdStateMachineProcess();
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return mixed
     */
    public function getInitialStateIdByStateName(StateMachineItemTransfer $stateMachineItemTransfer, $stateName)
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
    public function saveStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer, $stateName)
    {
        if (isset($this->persistedStates[$stateName])) {
            $stateMachineItemStateEntity = $this->persistedStates[$stateName];
        } else {
            $stateMachineItemTransfer->requireIdStateMachineProcess();

            /**
             * @var $stateMachineItemStateEntity \StateMachine\Model\Entity\StateMachineItemState
             */
            $stateMachineItemStateEntity = $this->stateMachineQueryContainer
                ->queryItemStateByIdProcessAndStateName(
                    $stateMachineItemTransfer->getIdStateMachineProcess(),
                    $stateName
                )->first();

            if ($stateMachineItemStateEntity === null) {
                $stateMachineItemStateEntity = $this->saveStateMachineItemEntity($stateMachineItemTransfer, $stateName);
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
    public function saveItemStateHistory(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineItemStateHistory = new SpyStateMachineItemStateHistory();
        $stateMachineItemStateHistory->setIdentifier($stateMachineItemTransfer->getIdentifier());
        $stateMachineItemStateHistory->setFkStateMachineItemState($stateMachineItemTransfer->getIdItemState());
        $stateMachineItemStateHistory->save();
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function updateStateMachineItemsFromPersistence(array $stateMachineItems)
    {
        $updatedStateMachineItems = [];
        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $stateMachineItemTransfer->requireIdentifier()
                ->requireIdItemState();

            /**
             * @var $stateMachineItemStateEntity \StateMachine\Model\Entity\StateMachineItemState
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
    public function getProcessedStateMachineItems(array $stateMachineItems)
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
    public function getProcessedStateMachineItemTransfer(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineItemTransfer->requireIdentifier()
            ->requireIdItemState();

        /**
         * @var $stateMachineItemStateEntity \StateMachine\Model\Entity\StateMachineItemState
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
    ) {
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
         * @var $stateMachineItemEntity \StateMachine\Model\Entity\StateMachineItemState
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
    public function getItemsWithExpiredTimeouts($stateMachineName)
    {
        /**
         * @var $stateMachineExpiredItems \StateMachine\Model\Entity\StateMachineTimeout[]
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
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineEventTimeout
     */
    public function saveStateMachineItemTimeout(
        StateMachineItemTransfer $stateMachineItemTransfer,
        DateTime $timeoutDate,
        $eventName
    ) {

        $stateMachineItemTimeoutEntity = new SpyStateMachineEventTimeout();
        $stateMachineItemTimeoutEntity
            ->setTimeout($timeoutDate)
            ->setIdentifier($stateMachineItemTransfer->getIdentifier())
            ->setFkStateMachineItemState($stateMachineItemTransfer->getIdItemState())
            ->setFkStateMachineProcess($stateMachineItemTransfer->getIdStateMachineProcess())
            ->setEvent($eventName)
            ->save();

        return $stateMachineItemTimeoutEntity;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function dropTimeoutByItem(StateMachineItemTransfer $stateMachineItemTransfer)
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
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcess
     */
    protected function saveStateMachineProcess(StateMachineProcessTransfer $stateMachineProcessTransfer)
    {
        $stateMachineProcessEntity = new SpyStateMachineProcess();
        $stateMachineProcessEntity->setName($stateMachineProcessTransfer->getProcessName());
        $stateMachineProcessEntity->setStateMachineName($stateMachineProcessTransfer->getStateMachineName());
        $stateMachineProcessEntity->save();

        return $stateMachineProcessEntity;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemState
     */
    protected function saveStateMachineItemEntity(StateMachineItemTransfer $stateMachineItemTransfer, $stateName)
    {
        $stateMachineItemStateEntity = new SpyStateMachineItemState();
        $stateMachineItemStateEntity->setName($stateName);
        $stateMachineItemStateEntity->setFkStateMachineProcess($stateMachineItemTransfer->getIdStateMachineProcess());
        $stateMachineItemStateEntity->save();

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
