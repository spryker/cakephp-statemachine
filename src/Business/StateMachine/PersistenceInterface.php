<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use DateTime;
use StateMachine\Model\Entity\StateMachineTimeout;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

interface PersistenceInterface
{
    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return int
     */
    public function getProcessId(StateMachineProcessTransfer $stateMachineProcessTransfer): int;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return int
     */
    public function getInitialStateIdByStateName(StateMachineItemTransfer $stateMachineItemTransfer, string $stateName): int;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    public function saveStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer, string $stateName): StateMachineItemTransfer;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function updateStateMachineItemsFromPersistence(array $stateMachineItems): array;

    /**
     * @param string $itemIdentifier
     * @param int $idStateMachineProcess
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getStateHistoryByStateItemIdentifier(string $itemIdentifier, int $idStateMachineProcess): array;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    public function getProcessedStateMachineItemTransfer(StateMachineItemTransfer $stateMachineItemTransfer): StateMachineItemTransfer;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
     */
    public function getProcessedStateMachineItems(array $stateMachineItems): array;

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
    ): array;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function saveItemStateHistory(StateMachineItemTransfer $stateMachineItemTransfer);

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
        string $eventName
    ): StateMachineTimeout;

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function dropTimeoutByItem(StateMachineItemTransfer $stateMachineItemTransfer): void;

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[] $expiredStateMachineItemsTransfer
     */
    public function getItemsWithExpiredTimeouts($stateMachineName): array;
}
