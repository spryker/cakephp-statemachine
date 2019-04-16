<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine;

use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

/**
 * Mocked version
 */
class TestStateMachineHandler implements StateMachineHandlerInterface
{
    /**
     * @var \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected static $itemStateUpdated;

    /**
     * @var \StateMachine\Transfer\StateMachineItemTransfer[]
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
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function itemStateUpdated(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        static::$itemStateUpdated = $stateMachineItemTransfer;

        return true;
    }

    /**
     * @param array $stateIds
     *
     * @return array
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        $result = [];
        foreach (static::$stateMachineItemsByStateIds as $stateMachineItemTransfer) {
            if (in_array($stateMachineItemTransfer->getIdItemState(), $stateIds)) {
                $result[] = $stateMachineItemTransfer;
            }
        }

        return $result;
    }

    /**
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    public function getItemStateUpdated(): StateMachineItemTransfer
    {
        return static::$itemStateUpdated;
    }

    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer[] $stateMachineItemsByStateIds
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
             * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
             *
             * @return bool
             */
            public function run(StateMachineItemTransfer $stateMachineItemTransfer): bool
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
             * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
             *
             * @return bool
             */
            public function check(StateMachineItemTransfer $stateMachineItemTransfer): bool
            {
                return true;
            }
        };
    }
}
