<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;

interface FinderInterface
{
    /**
     * @param string $stateMachineName
     *
     * @return bool
     */
    public function hasHandler(string $stateMachineName): bool;

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto[]
     */
    public function getProcesses(string $stateMachineName): array;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return string[][]
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems): array;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return string[]
     */
    public function getManualEventsForStateMachineItem(ItemDto $itemDto): array;

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $itemDto
     */
    public function getItemsWithFlag(ProcessDto $processDto, string $flag): array;

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     * @param string $flag
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[] $itemDto
     */
    public function getItemsWithoutFlag(ProcessDto $processDto, string $flag): array;

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
    ): array;

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function findProcessByStateMachineAndProcessName(string $stateMachineName, string $processName): ProcessInterface;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function findProcessesForItems(array $stateMachineItems): array;

    /**
     * @param string $stateMachineName
     *
     * @return array
     */
    public function getItemMatrix(string $stateMachineName): array;
}
