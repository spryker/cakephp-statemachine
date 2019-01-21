<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Logger;

use StateMachine\Business\Process\EventInterface;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

interface TransitionLogInterface
{
    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return void
     */
    public function setEvent(EventInterface $event);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItems
     *
     * @return void
     */
    public function init(array $stateMachineItems);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItem
     * @param \StateMachine\Dependency\CommandPluginInterface $command
     *
     * @return void
     */
    public function addCommand(StateMachineItemTransfer $stateMachineItem, CommandPluginInterface $command);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItem
     * @param \StateMachine\Dependency\ConditionPluginInterface $condition
     *
     * @return void
     */
    public function addCondition(StateMachineItemTransfer $stateMachineItem, ConditionPluginInterface $condition);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItem
     * @param string $stateName
     *
     * @return void
     */
    public function addSourceState(StateMachineItemTransfer $stateMachineItem, $stateName);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItem
     * @param string $stateName
     *
     * @return void
     */
    public function addTargetState(StateMachineItemTransfer $stateMachineItem, $stateName);

    /**
     * @param bool $error
     *
     * @return void
     */
    public function setIsError($error);

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItem
     *
     * @return void
     */
    public function save(StateMachineItemTransfer $stateMachineItem);

    /**
     * @param string $errorMessage
     *
     * @return void
     */
    public function setErrorMessage($errorMessage);

    /**
     * @return void
     */
    public function saveAll();
}
