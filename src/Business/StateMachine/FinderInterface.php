<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

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
     * @return \StateMachine\Transfer\StateMachineProcessTransfer[]
     */
    public function getProcesses($stateMachineName);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return array
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return array
     */
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer);

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItemTransfer
     */
    public function getItemsWithFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag);

    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItemTransfer
     */
    public function getItemsWithoutFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param array $sourceStates
     *
     * @return \StateMachine\Transfer\StateMachineItemTransfer[]
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function findProcessesForItems(array $stateMachineItems);
}
