<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

interface StateUpdaterInterface
{
    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     * @param \StateMachine\Business\Process\ProcessInterface[] $processes
     * @param array $sourceStates
     *
     * @return void
     */
    public function updateStateMachineItemState(array $stateMachineItems, array $processes, array $sourceStates);
}
