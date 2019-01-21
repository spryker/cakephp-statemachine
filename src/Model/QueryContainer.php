<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

use DateTime;

class QueryContainer implements QueryContainerInterface
{
    /**
     * @api
     *
     * @param int $idState
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery
     */
    public function queryStateByIdState($idState)
    {
        // TODO: Implement queryStateByIdState() method.
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery
     */
    public function queryItemsWithExistingHistory(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        // TODO: Implement queryItemsWithExistingHistory() method.
    }

    /**
     * @api
     *
     * @param \DateTime $expirationDate
     * @param string $stateMachineName
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineEventTimeoutQuery
     */
    public function queryItemsWithExpiredTimeout(DateTime $expirationDate, $stateMachineName)
    {
        // TODO: Implement queryItemsWithExpiredTimeout() method.
    }

    /**
     * @api
     *
     * @param int $identifier
     * @param int $idStateMachineProcess
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistoryQuery
     */
    public function queryItemHistoryByStateItemIdentifier($identifier, $idStateMachineProcess)
    {
        // TODO: Implement queryItemHistoryByStateItemIdentifier() method.
    }

    /**
     * @api
     *
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcessQuery
     */
    public function queryProcessByStateMachineAndProcessName($stateMachineName, $processName)
    {
        // TODO: Implement queryProcessByStateMachineAndProcessName() method.
    }

    /**
     * @api
     *
     * @param string $stateMachineName
     * @param string $processName
     * @param string[] $states
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery
     */
    public function queryItemsByIdStateMachineProcessAndItemStates(
        $stateMachineName,
        $processName,
        array $states
    ) {
        // TODO: Implement queryItemsByIdStateMachineProcessAndItemStates() method.
    }

    /**
     * @api
     *
     * @param int $idProcess
     * @param string $stateName
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery
     */
    public function queryItemStateByIdProcessAndStateName($idProcess, $stateName)
    {
        // TODO: Implement queryItemStateByIdProcessAndStateName() method.
    }

    /**
     * @api
     *
     * @deprecated Not used, will be removed in the next major release.
     *
     * @param string $identifier
     * @param \DateTime $expirationDate
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineLockQuery
     */
    public function queryLockedItemsByIdentifierAndExpirationDate($identifier, DateTime $expirationDate)
    {
        // TODO: Implement queryLockedItemsByIdentifierAndExpirationDate() method.
    }

    /**
     * @api
     *
     * @param \DateTime $expirationDate
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineLockQuery
     */
    public function queryLockedItemsByExpirationDate(DateTime $expirationDate)
    {
        // TODO: Implement queryLockedItemsByExpirationDate() method.
    }

    /**
     * @api
     *
     * @param string $identifier
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineLockQuery
     */
    public function queryLockItemsByIdentifier($identifier)
    {
        // TODO: Implement queryLockItemsByIdentifier() method.
    }

    /**
     * @api
     *
     * @param string $processName
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcessQuery
     */
    public function queryProcessByProcessName($processName)
    {
        // TODO: Implement queryProcessByProcessName() method.
    }

    /**
     * @api
     *
     * @param int $identifier
     * @param int $fkProcess
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineEventTimeoutQuery
     */
    public function queryEventTimeoutByIdentifierAndFkProcess($identifier, $fkProcess)
    {
        // TODO: Implement queryEventTimeoutByIdentifierAndFkProcess() method.
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param int $transitionToIdState
     *
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistoryQuery
     */
    public function queryLastHistoryItem(StateMachineItemTransfer $stateMachineItemTransfer, $transitionToIdState)
    {
        // TODO: Implement queryLastHistoryItem() method.
    }
}
