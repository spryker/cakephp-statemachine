<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Logger;

use StateMachine\Business\Process\EventInterface;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;

interface TransitionLogInterface
{
    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return void
     */
    public function setEvent(EventInterface $event);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return void
     */
    public function init(array $stateMachineItems);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param \StateMachine\Dependency\CommandPluginInterface $command
     *
     * @return void
     */
    public function addCommand(ItemDto $stateMachineItem, CommandPluginInterface $command);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param \StateMachine\Dependency\ConditionPluginInterface $condition
     *
     * @return void
     */
    public function addCondition(ItemDto $stateMachineItem, ConditionPluginInterface $condition);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param string $stateName
     *
     * @return void
     */
    public function addSourceState(ItemDto $stateMachineItem, $stateName);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param string $stateName
     *
     * @return void
     */
    public function addTargetState(ItemDto $stateMachineItem, $stateName);

    /**
     * @param bool $error
     *
     * @return void
     */
    public function setIsError($error);

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     *
     * @return void
     */
    public function save(ItemDto $stateMachineItem);

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
