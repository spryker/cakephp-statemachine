<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use StateMachine\Transfer\StateMachineItemTransfer;

interface QueryContainerInterface
{
    /**
     * @param int $idState
     *
     * @return \Cake\ORM\Query
     */
    public function queryStateByIdState(int $idState): Query;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExistingHistory(StateMachineItemTransfer $stateMachineItemTransfer): Query;

    /**
     * @param \Cake\I18n\FrozenTime $expirationDate
     * @param string $stateMachineName
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemsWithExpiredTimeout(FrozenTime $expirationDate, string $stateMachineName): Query;

    /**
     * @param string $identifier
     * @param int $idStateMachineProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryItemHistoryByStateItemIdentifier(string $identifier, int $idStateMachineProcess): Query;

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
     *
     * @return \Cake\ORM\Query
     */
    public function queryLockedItemsByExpirationDate(FrozenTime $expirationDate): Query;

    /**
     * @param string $identifier
     *
     * @return \Cake\ORM\Query
     */
    public function queryLockItemsByIdentifier(string $identifier): Query;

    /**
     * @param string $processName
     *
     * @return \Cake\ORM\Query
     */
    public function queryProcessByProcessName(string $processName): Query;

    /**
     * @param string $identifier
     * @param int $idProcess
     *
     * @return \Cake\ORM\Query
     */
    public function queryEventTimeoutByIdentifierAndFkProcess(string $identifier, int $idProcess): Query;
}
