<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

interface StateUpdaterInterface
{
    /**
     * @param array<\StateMachine\Dto\StateMachine\ItemDto> $stateMachineItems
     * @param array<\StateMachine\Business\Process\ProcessInterface> $processes
     * @param array<string> $sourceStates
     *
     * @return void
     */
    public function updateStateMachineItemState(array $stateMachineItems, array $processes, array $sourceStates): void;
}
