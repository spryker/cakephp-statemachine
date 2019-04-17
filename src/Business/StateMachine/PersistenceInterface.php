<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use Cake\I18n\FrozenTime;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\Model\Entity\StateMachineTimeout;

interface PersistenceInterface
{
    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     *
     * @return int
     */
    public function getProcessId(ProcessDto $stateMachineProcessTransfer): int;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return int
     */
    public function getInitialStateIdByStateName(ItemDto $stateMachineItemTransfer, string $stateName): int;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function saveStateMachineItem(ItemDto $stateMachineItemTransfer, string $stateName): ItemDto;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function updateStateMachineItemsFromPersistence(array $stateMachineItems): array;

    /**
     * @param string $itemIdentifier
     * @param int $idStateMachineProcess
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateHistoryByStateItemIdentifier(string $itemIdentifier, int $idStateMachineProcess): array;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getProcessedItemDto(ItemDto $stateMachineItemTransfer): ItemDto;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
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
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return void
     */
    public function saveItemStateHistory(ItemDto $stateMachineItemTransfer);

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
    ): StateMachineTimeout;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return void
     */
    public function dropTimeoutByItem(ItemDto $stateMachineItemTransfer): void;

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $expiredStateMachineItemsTransfer
     */
    public function getItemsWithExpiredTimeouts($stateMachineName): array;
}
