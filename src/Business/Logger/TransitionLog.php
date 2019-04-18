<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Logger;

use RuntimeException;
use StateMachine\Business\Process\EventInterface;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\Entity\StateMachineTransitionLog;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;

class TransitionLog implements TransitionLogInterface
{
    public const QUERY_STRING = 'QUERY_STRING';

    /**
     * @var \StateMachine\Model\Entity\StateMachineTransitionLog[]
     */
    protected $logEntities;

    /**
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected $stateMachineTransitionLogsTable;

    /**
     * @param \StateMachine\Model\Table\StateMachineTransitionLogsTable $stateMachineTransitionLogsTable
     */
    public function __construct(
        StateMachineTransitionLogsTable $stateMachineTransitionLogsTable
    ) {
        $this->stateMachineTransitionLogsTable = $stateMachineTransitionLogsTable;
    }

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return void
     */
    public function setEvent(EventInterface $event): void
    {
        $nameEvent = $event->getName();
        $nameEvent .= $event->getEventTypeLabel();

        foreach ($this->logEntities as $logEntity) {
            $logEntity->event = $nameEvent;
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return void
     */
    public function init(array $stateMachineItems): void
    {
        $this->logEntities = [];
        foreach ($stateMachineItems as $stateMachineItem) {
            $logEntity = $this->initEntity($stateMachineItem);
            $this->logEntities[$stateMachineItem->getIdentifierOrFail()] = $logEntity;
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param \StateMachine\Dependency\CommandPluginInterface $command
     *
     * @return void
     */
    public function addCommand(ItemDto $itemDto, CommandPluginInterface $command): void
    {
        $this->logEntities[$itemDto->getIdentifierOrFail()]->command = get_class($command);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param \StateMachine\Dependency\ConditionPluginInterface $condition
     *
     * @return void
     */
    public function addCondition(ItemDto $itemDto, ConditionPluginInterface $condition): void
    {
        $this->logEntities[$itemDto->getIdentifierOrFail()]->condition = get_class($condition);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $stateName
     *
     * @return void
     */
    public function addSourceState(ItemDto $itemDto, string $stateName): void
    {
        $this->logEntities[$itemDto->getIdentifierOrFail()]->source_state = $stateName;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $stateName
     *
     * @return void
     */
    public function addTargetState(ItemDto $itemDto, string $stateName): void
    {
        $this->logEntities[$itemDto->getIdentifierOrFail()]->target_state = $stateName;
    }

    /**
     * @param bool $error
     *
     * @return void
     */
    public function setIsError(bool $error): void
    {
        foreach ($this->logEntities as $logEntity) {
            $logEntity->is_error = $error;
        }
    }

    /**
     * @param string $errorMessage
     *
     * @return void
     */
    public function setErrorMessage(string $errorMessage): void
    {
        foreach ($this->logEntities as $logEntity) {
            $logEntity->error_message = $errorMessage;
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @throws \RuntimeException
     *
     * @return \StateMachine\Model\Entity\StateMachineTransitionLog
     */
    protected function initEntity(ItemDto $itemDto): StateMachineTransitionLog
    {
        $stateMachineTransitionLogEntity = $this->createStateMachineTransitionLogEntity();
        $stateMachineTransitionLogEntity->identifier = $itemDto->getIdentifierOrFail();
        $stateMachineTransitionLogEntity->state_machine_process_id = $itemDto->getIdStateMachineProcessOrFail();

        $params = [];
        if (!empty($_SERVER[static::QUERY_STRING])) {
            $params = $this->getParamsFromQueryString($_SERVER[static::QUERY_STRING]);
        }

        $encodedString = json_encode($params);
        if ($encodedString === false) {
            throw new RuntimeException('JSON encoding failed: ' . print_r($params, true));
        }
        $stateMachineTransitionLogEntity->params = $encodedString;

        return $stateMachineTransitionLogEntity;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    public function save(ItemDto $itemDto): void
    {
        $this->stateMachineTransitionLogsTable->save($this->logEntities[$itemDto->getIdentifierOrFail()]);
    }

    /**
     * @return void
     */
    public function saveAll(): void
    {
        foreach ($this->logEntities as $logEntity) {
            if ($logEntity->isDirty()) {
                $this->stateMachineTransitionLogsTable->save($logEntity);
            }
        }
        $this->logEntities = [];
    }

    /**
     * @return \StateMachine\Model\Entity\StateMachineTransitionLog
     */
    protected function createStateMachineTransitionLogEntity(): StateMachineTransitionLog
    {
        return $this->stateMachineTransitionLogsTable->newEntity();
    }

    /**
     * @param string $queryString
     *
     * @return string[]
     */
    protected function getParamsFromQueryString(string $queryString)
    {
        return explode('&', $queryString);
    }
}
