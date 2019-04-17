<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine;

use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
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
            'Test/Command' => $this->createCommandPlugin(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        return [
            'Test/Condition' => $this->createConditionPlugin(),
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
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return bool
     */
    public function itemStateUpdated(ItemDto $stateMachineItemTransfer): bool
    {
        static::$itemStateUpdated = $stateMachineItemTransfer;

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
        foreach (static::$stateMachineItemsByStateIds as $stateMachineItemTransfer) {
            if (in_array($stateMachineItemTransfer->getIdItemStateOrFail(), $stateIds)) {
                $result[] = $stateMachineItemTransfer;
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

    /**
     * @return \StateMachine\Dependency\CommandPluginInterface
     */
    protected function createCommandPlugin(): CommandPluginInterface
    {
        return new class implements CommandPluginInterface
        {
            /**
             * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
             *
             * @return bool
             */
            public function run(ItemDto $stateMachineItemTransfer): bool
            {
                return true;
            }
        };
    }

    /**
     * @return \StateMachine\Dependency\ConditionPluginInterface
     */
    protected function createConditionPlugin(): ConditionPluginInterface
    {
        return new class implements ConditionPluginInterface
        {
            /**
             * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
             *
             * @return bool
             */
            public function check(ItemDto $stateMachineItemTransfer): bool
            {
                return true;
            }
        };
    }
}
