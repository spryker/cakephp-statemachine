<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Dto\StateMachine\ItemDto;

interface ConditionInterface
{
    /**
     * @param \StateMachine\Business\Process\TransitionInterface[] $transitions
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     * @param \StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     *
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getTargetStatesFromTransitions(
        array $transitions,
        ItemDto $stateMachineItemTransfer,
        StateInterface $sourceState,
        TransitionLogInterface $transactionLogger
    );

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[][] $itemsWithOnEnterEvent
     */
    public function getOnEnterEventsForStatesWithoutTransition($stateMachineName, $processName);
}
