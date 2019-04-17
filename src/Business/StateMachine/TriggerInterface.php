<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Dto\StateMachine\ProcessDto;

interface TriggerInterface
{
    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $stateMachineProcessTransfer
     * @param string $identifier
     *
     * @return int
     */
    public function triggerForNewStateMachineItem(
        ProcessDto $stateMachineProcessTransfer,
        string $identifier
    ): int;

    /**
     * @param string $eventName
     * @param array $items
     *
     * @return int
     */
    public function triggerEvent(string $eventName, array $items): int;

    /**
     * @param string $stateMachineName
     *
     * @return int
     */
    public function triggerConditionsWithoutEvent(string $stateMachineName): int;

    /**
     * @param string $stateMachineName
     *
     * @return int
     */
    public function triggerForTimeoutExpiredItems(string $stateMachineName): int;
}
