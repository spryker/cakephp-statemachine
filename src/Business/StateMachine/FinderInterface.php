<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;

interface FinderInterface
{
    /**
     * @param string $stateMachineName
     *
     * @return bool
     */
    public function hasHandler($stateMachineName);

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto[]
     */
    public function getProcesses($stateMachineName);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string[][]
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return array
     */
    public function getManualEventsForStateMachineItem(ItemDto $stateMachineItemTransfer);

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string $flag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItemTransfer
     */
    public function getItemsWithFlag(ProcessDto $stateMachineProcessTransfer, $flag);

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string $flag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItemTransfer
     */
    public function getItemsWithoutFlag(ProcessDto $stateMachineProcessTransfer, $flag);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param array $sourceStates
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[][]
     */
    public function filterItemsWithOnEnterEvent(
        array $stateMachineItems,
        array $processes,
        array $sourceStates = []
    );

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function findProcessByStateMachineAndProcessName($stateMachineName, $processName);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function findProcessesForItems(array $stateMachineItems);
}
