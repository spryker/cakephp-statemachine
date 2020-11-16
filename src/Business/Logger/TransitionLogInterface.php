<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Logger;

use StateMachine\Business\Process\EventInterface;
use StateMachine\Dependency\StateMachineCommandInterface;
use StateMachine\Dependency\StateMachineConditionInterface;
use StateMachine\Dto\StateMachine\ItemDto;

interface TransitionLogInterface
{
    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return void
     */
    public function setEvent(EventInterface $event): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $eventName
     *
     * @return void
     */
    public function setEventName(ItemDto $itemDto, string $eventName): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return void
     */
    public function init(array $stateMachineItems): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param \StateMachine\Dependency\StateMachineCommandInterface $command
     *
     * @return void
     */
    public function addCommand(ItemDto $stateMachineItem, StateMachineCommandInterface $command): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param \StateMachine\Dependency\StateMachineConditionInterface $condition
     *
     * @return void
     */
    public function addCondition(ItemDto $stateMachineItem, StateMachineConditionInterface $condition): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param string $stateName
     *
     * @return void
     */
    public function addSourceState(ItemDto $stateMachineItem, string $stateName): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     * @param string $stateName
     *
     * @return void
     */
    public function addTargetState(ItemDto $stateMachineItem, string $stateName): void;

    /**
     * @param bool $error
     *
     * @return void
     */
    public function setIsError(bool $error): void;

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItem
     *
     * @return void
     */
    public function save(ItemDto $stateMachineItem): void;

    /**
     * @param string $errorMessage
     *
     * @return void
     */
    public function setErrorMessage(string $errorMessage): void;

    /**
     * @return void
     */
    public function saveAll(): void;

    /**
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return int
     */
    public function getEventCount(string $eventName, array $stateMachineItems = []): int;
}
