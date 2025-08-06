<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\FactoryTrait;

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
            ->contain($this->getFactory()->createStateMachineProcessesTable()->getAlias())
            ->where([$stateMachineItemStatesTable->aliasField('id') => $idState]);
    }

    /**
     * @param string $state
     * @param string $process
     *
     * @return \Cake\ORM\Query
     */
    public function queryStateByNameAndProcess(string $state, string $process): Query
    {
        $stateMachineItemStatesTable = $this->getFactory()
            ->createStateMachineItemStatesTable();

        return $stateMachineItemStatesTable
            ->find()
            ->contain('StateMachineProcesses')
            ->where(['StateMachineProcesses.name' => $process, 'StateMachineItemStates.name' => $state]);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExistingHistory(ItemDto $itemDto): Query
    {
        $stateMachineItemStatesTable = $this->getFactory()
            ->createStateMachineItemStatesTable();

        return $stateMachineItemStatesTable
            ->find()
            ->contain($this->getFactory()->createStateMachineProcessesTable()->getAlias())
            ->contain($this->getFactory()->createStateMachineItemStateLogsTable()->getAlias(), function (Query $query) use ($itemDto) {
                return $query->where(['identifier' => $itemDto->getIdentifierOrFail()])->orderDesc('id');
            })
            ->where([$stateMachineItemStatesTable->aliasField('id') => $itemDto->getIdItemStateOrFail()]);
    }

    /**
     * @param \Cake\I18n\FrozenTime $expirationDate
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExpiredTimeout(FrozenTime $expirationDate, string $stateMachineName): Query
    {
        $stateMachineTimeoutsTable = $this->getFactory()->createStateMachineTimeoutsTable();
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        return $stateMachineTimeoutsTable
            ->find()
            ->contain($this->getFactory()->createStateMachineItemStatesTable()->getAlias(), function (Query $query) use ($stateMachineProcessesTable) {
                return $query->contain($stateMachineProcessesTable->getAlias());
            })
            ->where([
                $stateMachineTimeoutsTable->aliasField('timeout') . ' < ' => $expirationDate->format('Y-m-d H:i:s'),
                $stateMachineProcessesTable->aliasField('state_machine') => $stateMachineName,
            ]);
    }

    /**
     * @param int $identifier
     * @param int $idStateMachineProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemHistoryByStateItemIdentifier(int $identifier, int $idStateMachineProcess): Query
    {
        $stateMachineItemStateLogsTable = $this->getFactory()->createStateMachineItemStateLogsTable();
        $stateMachineItemStateTable = $this->getFactory()->createStateMachineItemStatesTable();
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        return $stateMachineItemStateLogsTable
            ->find()
            ->contain($stateMachineItemStateTable->getAlias(), function (Query $query) use ($idStateMachineProcess, $stateMachineProcessesTable) {
                return $query
                    ->contain($stateMachineProcessesTable->getAlias())
                    ->where(['state_machine_process_id' => $idStateMachineProcess]);
            })
            ->where([
                $stateMachineItemStateLogsTable->aliasField('identifier') => $identifier,
            ])
            ->order([
                $stateMachineItemStateLogsTable->aliasField('created') => 'ASC',
                $stateMachineItemStateLogsTable->aliasField('id') => 'ASC',
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
        $stateMachineItemStateLogsTable = $this->getFactory()->createStateMachineItemStateLogsTable();
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        $states = $states ?: [-1];

        return $stateMachineItemStatesTable
            ->find()
            ->contain($stateMachineItemStateLogsTable->getAlias())
            ->contain($stateMachineProcessesTable->getAlias(), function (Query $query) use ($stateMachineName, $processName, $stateMachineProcessesTable) {
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
        $stateMachineProcessesTable = $this->getFactory()->createStateMachineProcessesTable();

        return $stateMachineItemStatesTable
            ->find()
            ->contain($stateMachineProcessesTable->getAlias())
            ->where([
                $stateMachineItemStatesTable->aliasField('name') => $stateName,
                $stateMachineItemStatesTable->aliasField('state_machine_process_id') => $idProcess,
            ]);
    }

    /**
     * @param \Cake\I18n\FrozenTime $expirationDate
     * @param bool $delete
     *
     * @return \Cake\ORM\Query
     */
    public function queryLockedItemsByExpirationDate(FrozenTime $expirationDate, bool $delete = false): Query
    {
        $stateMachineLocksTable = $this->getFactory()->createStateMachineLocksTable();

        $query = $delete ? $stateMachineLocksTable->deleteQuery() : $stateMachineLocksTable->find();
        return $query
            ->where([
                $stateMachineLocksTable->aliasField('expires') . ' <= ' => $expirationDate,
            ]);
    }

    /**
     * @param string $identifier
     * @param bool $delete
     *
     * @return \Cake\ORM\Query
     */
    public function queryLockItemsByIdentifier(string $identifier, bool $delete = true): Query
    {
        $stateMachineLocksTable = $this->getFactory()->createStateMachineLocksTable();

        $query = $delete ? $stateMachineLocksTable->deleteQuery() : $stateMachineLocksTable->find();
        return $query
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
     * @param int $identifier
     * @param int $idProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryEventTimeoutByIdentifierAndFkProcess(int $identifier, int $idProcess): Query
    {
        $stateMachineTimeoutsTable = $this->getFactory()->createStateMachineTimeoutsTable();

        return $stateMachineTimeoutsTable
            ->find()
            ->where([
                $stateMachineTimeoutsTable->aliasField('identifier') => $identifier,
                $stateMachineTimeoutsTable->aliasField('state_machine_process_id') => $idProcess,
            ]);
    }

    /**
     * @param string $stateMachineName
     * @param array $stateBlackList
     *
     * @return \Cake\ORM\Query
     */
    public function queryMatrix(string $stateMachineName, array $stateBlackList = []): Query
    {
        /** @var \Cake\ORM\Query $query */
        $query = $this->getFactory()->createStateMachineItemsTable()
            ->find();

        $query = $query
            ->select(['state', 'count' => $query->func()->count('*')])
            ->group('state')
            ->where(['state_machine' => $stateMachineName]);
        if ($stateBlackList) {
            $query = $query->whereNotInList('state', $stateBlackList);
        }

        return $query;
    }
}
