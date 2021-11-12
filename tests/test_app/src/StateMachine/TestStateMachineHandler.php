<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace TestApp\StateMachine;

use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use TestApp\StateMachine\Command\TestErrorStateMachineCommand;
use TestApp\StateMachine\Command\TestStateMachineCommand;
use TestApp\StateMachine\Condition\TestErrorStateMachineCondition;
use TestApp\StateMachine\Condition\TestTrueStateMachineCondition;

/**
 * Mocked version
 */
class TestStateMachineHandler implements StateMachineHandlerInterface
{
    /**
     * @var \StateMachine\Dto\StateMachine\ItemDto
     */
    protected static $itemStateUpdated;

    /**
     * @var array<\StateMachine\Dto\StateMachine\ItemDto>
     */
    protected static $stateMachineItemsByStateIds = [];

    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        return [
            'Test/Command' => TestStateMachineCommand::class,
            'Test/ErrorCommand' => TestErrorStateMachineCommand::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        return [
            'Test/Condition' => TestTrueStateMachineCondition::class,
            'Test/ErrorCondition' => TestErrorStateMachineCondition::class,
        ];
    }

    /**
     * @return string
     */
    public function getStateMachineName(): string
    {
        return 'TestingSm';
    }

    /**
     * @return array<string>
     */
    public function getActiveProcesses(): array
    {
        return [
            'TestProcess',
        ];
    }

    /**
     * @param string $processName
     *
     * @return string
     */
    public function getInitialStateForProcess(string $processName): string
    {
        return 'new';
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function itemStateUpdated(ItemDto $itemDto): bool
    {
        static::$itemStateUpdated = $itemDto;

        return true;
    }

    /**
     * @param array<int> $stateIds
     *
     * @return array<\StateMachine\Dto\StateMachine\ItemDto>
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        $result = [];
        foreach (static::$stateMachineItemsByStateIds as $itemDto) {
            if (in_array($itemDto->getIdItemStateOrFail(), $stateIds, true)) {
                $result[] = $itemDto;
            }
        }

        return $result;
    }

    /**
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    public function getItemStateUpdated(): ItemDto
    {
        return static::$itemStateUpdated;
    }

    /**
     * @param array<\StateMachine\Dto\StateMachine\ItemDto> $stateMachineItemsByStateIds
     *
     * @return void
     */
    public function setStateMachineItemsByStateIds(array $stateMachineItemsByStateIds): void
    {
        static::$stateMachineItemsByStateIds = $stateMachineItemsByStateIds;
    }
}
