<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

use Cake\ORM\Query;
use DateTime;
use StateMachine\FactoryTrait;
use StateMachine\Transfer\StateMachineItemTransfer;

class QueryContainer implements QueryContainerInterface
{
    use FactoryTrait;

    /**
     * @param int $idState
     *
     * @return \Cake\ORM\Query
     */
    public function queryStateByIdState(int $idState): Query
    {
        $stateMachineItemStatesTable = $this->getFactory()
            ->createStateMachineItemStatesTable();

        return $stateMachineItemStatesTable
            ->find()
            ->matching($this->getFactory()->createStateMachineProcessesTable()->getAlias())
            ->where([$stateMachineItemStatesTable->aliasField('id') => $idState]);
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExistingHistory(StateMachineItemTransfer $stateMachineItemTransfer): Query
    {
        $stateMachineItemStatesTable = $this->getFactory()
            ->createStateMachineItemStatesTable();

        return $stateMachineItemStatesTable
            ->find()
            ->matching($this->getFactory()->createStateMachineProcessesTable()->getAlias())
            ->matching($this->getFactory()->createStateMachineItemStateHistoryTable()->getAlias(), function (Query $query) use ($stateMachineItemTransfer) {
                return $query->where(['identifier' => $stateMachineItemTransfer->getIdentifier()]);
            })
            ->where([$stateMachineItemStatesTable->aliasField('id') => $stateMachineItemTransfer->getIdItemState()]);
    }

    /**
     * @param \DateTime $expirationDate
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExpiredTimeout(DateTime $expirationDate, string $stateMachineName): Query
    {
        $stateMachineTimeoutsTable = $this->getFactory()->createStateMachineTimeoutsTable();
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        return $stateMachineTimeoutsTable
            ->find()
            ->matching($this->getFactory()->createStateMachineItemStatesTable()->getAlias())
            ->matching($stateMachineProcessesTable->getAlias())
            ->where([
                $stateMachineTimeoutsTable->aliasField('timeout') . ' < ' => $expirationDate->format('Y-m-d H:i:s'),
                $stateMachineProcessesTable->aliasField('state_machine') => $stateMachineName,
            ]);
    }

    /**
     * @param string $identifier
     * @param int $idStateMachineProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemHistoryByStateItemIdentifier(string $identifier, int $idStateMachineProcess): Query
    {
        $stateMachineItemStateHistoryTable = $this->getFactory()->createStateMachineItemStateHistoryTable();
        $stateMachineItemStateTable = $this->getFactory()->createStateMachineItemStatesTable();

        return $stateMachineItemStateHistoryTable
            ->find()
            ->matching($stateMachineItemStateTable->getAlias(), function (Query $query) use ($idStateMachineProcess) {
                return $query->where(['state_machine_process_id' => $idStateMachineProcess]);
            })
            ->where([
                $stateMachineItemStateHistoryTable->aliasField('identifier') => $identifier,
            ])
            ->order([
                $stateMachineItemStateHistoryTable->aliasField('created') => 'ASC',
                $stateMachineItemStateHistoryTable->aliasField('id') => 'ASC',
            ]);
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \Cake\ORM\Query
     */
    public function queryProcessByStateMachineAndProcessName(string $stateMachineName, string $processName): Query
    {
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        return $stateMachineProcessesTable
            ->find()
            ->where([
                $stateMachineProcessesTable->aliasField('name') => $processName,
                $stateMachineProcessesTable->aliasField('state_machine') => $stateMachineName,
            ]);
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     * @param array $states
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsByIdStateMachineProcessAndItemStates(
        string $stateMachineName,
        string $processName,
        array $states
    ): Query {
        $stateMachineItemStatesTable = $this->getFactory()->createStateMachineItemStatesTable();
        $stateMachineItemStateHistoryTable = $this->getFactory()->createStateMachineItemStateHistoryTable();
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        $states = $states ?: [-1];
        return $stateMachineItemStatesTable
            ->find()
            ->matching($stateMachineItemStateHistoryTable->getAlias())
            ->matching($stateMachineProcessesTable->getAlias(), function (Query $query) use ($stateMachineName, $processName, $stateMachineProcessesTable) {
                return $query->where([
                    $stateMachineProcessesTable->aliasField('state_machine') => $stateMachineName,
                    $stateMachineProcessesTable->aliasField('name') => $processName,
                ]);
            })
            ->where([
                $stateMachineItemStatesTable->aliasField('name') . ' IN ' => $states,
            ])
            ->order([
                $stateMachineItemStatesTable->aliasField('id') => 'ASC',
            ]);
    }

    /**
     * @param int $idProcess
     * @param string $stateName
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemStateByIdProcessAndStateName(int $idProcess, string $stateName): Query
    {
        $stateMachineItemStatesTable = $this->getFactory()->createStateMachineItemStatesTable();

        return $stateMachineItemStatesTable
            ->find()
            ->where([
                $stateMachineItemStatesTable->aliasField('name') => $stateName,
                $stateMachineItemStatesTable->aliasField('state_machine_process_id') => $idProcess,
            ]);
    }

    /**
     * @param \DateTime $expirationDate
     *
     * @return \Cake\ORM\Query
     */
    public function queryLockedItemsByExpirationDate(DateTime $expirationDate): Query
    {
        $stateMachineLocksTable = $this->getFactory()->createStateMachineLocksTable();

        return $stateMachineLocksTable
            ->find()
            ->where([
                $stateMachineLocksTable->aliasField('expires') . ' <= ' => $expirationDate,
            ]);
    }

    /**
     * @param string $identifier
     *
     * @return \Cake\ORM\Query
     */
    public function queryLockItemsByIdentifier(string $identifier): Query
    {
        $stateMachineLocksTable = $this->getFactory()->createStateMachineLocksTable();

        return $stateMachineLocksTable
            ->find()
            ->where([
                $stateMachineLocksTable->aliasField('identifier') => $identifier,
            ]);
    }

    /**
     * @param string $processName
     *
     * @return \Cake\ORM\Query
     */
    public function queryProcessByProcessName(string $processName): Query
    {
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        return $stateMachineProcessesTable
            ->find()
            ->where([
                $stateMachineProcessesTable->aliasField('name') => $processName,
            ]);
    }

    /**
     * @param string $identifier
     * @param int $idProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryEventTimeoutByIdentifierAndFkProcess(string $identifier, int $idProcess): Query
    {
        $stateMachineTimeoutsTable = $this->getFactory()->createStateMachineTimeoutsTable();

        return $stateMachineTimeoutsTable
            ->find()
            ->where([
                $stateMachineTimeoutsTable->aliasField('identifier') => $identifier,
                $stateMachineTimeoutsTable->aliasField('state_machine_process_id') => $idProcess,
            ]);
    }
}
