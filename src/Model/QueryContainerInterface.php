<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use StateMachine\Dto\StateMachine\ItemDto;

/**
 * @method \StateMachine\PluginFactory getFactory()
 */
interface QueryContainerInterface
{
    /**
     * @param int $idState
     *
     * @return \Cake\ORM\Query
     */
    public function queryStateByIdState(int $idState): Query;

    /**
     * @param string $state
     * @param string $process
     *
     * @return \Cake\ORM\Query
     */
    public function queryStateByNameAndProcess(string $state, string $process): Query;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExistingHistory(ItemDto $itemDto): Query;

    /**
     * @param \Cake\I18n\FrozenTime $expirationDate
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExpiredTimeout(FrozenTime $expirationDate, string $stateMachineName): Query;

    /**
     * @param int $identifier
     * @param int $idStateMachineProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemHistoryByStateItemIdentifier(int $identifier, int $idStateMachineProcess): Query;

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \Cake\ORM\Query
     */
    public function queryProcessByStateMachineAndProcessName(string $stateMachineName, string $processName): Query;

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
    ): Query;

    /**
     * @param int $idProcess
     * @param string $stateName
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemStateByIdProcessAndStateName(int $idProcess, string $stateName): Query;

    /**
     * @param \Cake\I18n\FrozenTime $expirationDate
     * @param bool $delete
     *
     * @return \Cake\ORM\Query|\Cake\ORM\Query|\Cake\ORM\Query\DeleteQuery
     */
    public function queryLockedItemsByExpirationDate(FrozenTime $expirationDate, bool $delete = false);

    /**
     * @param string $identifier
     * @param bool $delete
     *
     * @return \Cake\ORM\Query|\Cake\ORM\Query|\Cake\ORM\Query\DeleteQuery
     */
    public function queryLockItemsByIdentifier(string $identifier, bool $delete = false);

    /**
     * @param string $processName
     *
     * @return \Cake\ORM\Query
     */
    public function queryProcessByProcessName(string $processName): Query;

    /**
     * @param int $identifier
     * @param int $idProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryEventTimeoutByIdentifierAndFkProcess(int $identifier, int $idProcess): Query;

    /**
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query
     */
    public function queryMatrix(string $stateMachineName): Query;
}
