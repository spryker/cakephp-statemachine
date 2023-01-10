<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use StateMachine\Dto\StateMachine\ItemDto;

/**
 * @method \StateMachine\PluginFactory getFactory()
 */
interface QueryContainerInterface
{
    /**
     * @param int $idState
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryStateByIdState(int $idState): SelectQuery;

    /**
     * @param string $state
     * @param string $process
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryStateByNameAndProcess(string $state, string $process): SelectQuery;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryItemsWithExistingHistory(ItemDto $itemDto): SelectQuery;

    /**
     * @param \Cake\I18n\DateTime $expirationDate
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryItemsWithExpiredTimeout(DateTime $expirationDate, string $stateMachineName): SelectQuery;

    /**
     * @param int $identifier
     * @param int $idStateMachineProcess
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryItemHistoryByStateItemIdentifier(int $identifier, int $idStateMachineProcess): SelectQuery;

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryProcessByStateMachineAndProcessName(string $stateMachineName, string $processName): SelectQuery;

    /**
     * @param string $stateMachineName
     * @param string $processName
     * @param array $states
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryItemsByIdStateMachineProcessAndItemStates(
        string $stateMachineName,
        string $processName,
        array $states
    ): SelectQuery;

    /**
     * @param int $idProcess
     * @param string $stateName
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryItemStateByIdProcessAndStateName(int $idProcess, string $stateName): SelectQuery;

    /**
     * @param \Cake\I18n\DateTime $expirationDate
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryLockedItemsByExpirationDate(DateTime $expirationDate): SelectQuery;

    /**
     * @param string $identifier
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryLockItemsByIdentifier(string $identifier): SelectQuery;

    /**
     * @param string $processName
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryProcessByProcessName(string $processName): SelectQuery;

    /**
     * @param int $identifier
     * @param int $idProcess
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryEventTimeoutByIdentifierAndFkProcess(int $identifier, int $idProcess): SelectQuery;

    /**
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryMatrix(string $stateMachineName): SelectQuery;
}
