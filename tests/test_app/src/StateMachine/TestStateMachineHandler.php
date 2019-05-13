<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine;

use App\StateMachine\Command\TestCommand;
use App\StateMachine\Command\TestErrorCommand;
use App\StateMachine\Condition\TestErrorCondition;
use App\StateMachine\Condition\TestTrueCondition;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;

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
     * @var \StateMachine\Dto\StateMachine\ItemDto[]
     */
    protected static $stateMachineItemsByStateIds = [];

    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        return [
            'Test/Command' => TestCommand::class,
            'Test/ErrorCommand' => TestErrorCommand::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        return [
            'Test/Condition' => TestTrueCondition::class,
            'Test/ErrorCondition' => TestErrorCondition::class,
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
     * @return string[]
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
     * @param int[] $stateIds
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
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
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItemsByStateIds
     *
     * @return void
     */
    public function setStateMachineItemsByStateIds(array $stateMachineItemsByStateIds): void
    {
        static::$stateMachineItemsByStateIds = $stateMachineItemsByStateIds;
    }
}
