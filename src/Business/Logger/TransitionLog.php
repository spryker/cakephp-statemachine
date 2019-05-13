<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Logger;

use Cake\ORM\TableRegistry;
use RuntimeException;
use StateMachine\Business\Process\EventInterface;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\Entity\StateMachineItem;
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
        $eventName = $event->getName();
        $eventName .= $event->getEventTypeLabel();

        foreach ($this->logEntities as $logEntity) {
            $logEntity->event = $eventName;
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     * @param string $eventName
     *
     * @return void
     */
    public function setEventName(ItemDto $itemDto, string $eventName): void
    {
        foreach ($this->logEntities as $logEntity) {
            $logEntity->event = $eventName;
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
        $stateMachineItemEntity = $this->initStateMachineItemEntity($itemDto);

        $stateMachineTransitionLogEntity = $this->createStateMachineTransitionLogEntity();
        $stateMachineTransitionLogEntity->identifier = $itemDto->getIdentifierOrFail();
        $stateMachineTransitionLogEntity->state_machine_item_id = $stateMachineItemEntity->id;
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
        $this->stateMachineTransitionLogsTable->saveOrFail($this->logEntities[$itemDto->getIdentifierOrFail()]);
    }

    /**
     * @return void
     */
    public function saveAll(): void
    {
        foreach ($this->logEntities as $logEntity) {
            if ($logEntity->isDirty()) {
                $this->stateMachineTransitionLogsTable->saveOrFail($logEntity);
            }
        }
        $this->logEntities = [];
    }

    /**
     * @param string $eventName
     * @param \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems
     *
     * @return int
     */
    public function getEventCount(string $eventName, array $stateMachineItems = []): int
    {
        if (!$stateMachineItems) {
            return 0;
        }

        $conditions = [];
        foreach ($stateMachineItems as $stateMachineItem) {
            $conditions[] = [
                'event' => $eventName,
                'source_state IS NOT' => null,
                'target_state IS NOT' => null,
                'identifier' => $stateMachineItem->getIdentifierOrFail(),
                'StateMachineProcesses.name' => $stateMachineItem->getProcessNameOrFail()
            ];
        }

        $count = $this->stateMachineTransitionLogsTable->find()
            ->contain(['StateMachineProcesses'])
            ->where(['OR' => $conditions])
            ->count();
        if ($count && count($conditions) > 1) {
            $count = (int)ceil($count / count($conditions));
        }

        return $count;
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
    protected function getParamsFromQueryString(string $queryString): array
    {
        return explode('&', $queryString);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return \StateMachine\Model\Entity\StateMachineItem
     */
    protected function initStateMachineItemEntity(ItemDto $itemDto): StateMachineItem
    {
        $stateMachineItemsTable = TableRegistry::get('StateMachine.StateMachineItems');

        /** @var \StateMachine\Model\Entity\StateMachineItem $stateMachineItem */
        $stateMachineItem = $stateMachineItemsTable->findOrCreate([
            'state_machine' => $itemDto->getStateMachineNameOrFail(),
            'identifier' => $itemDto->getIdentifierOrFail(),
        ]);
        if (!$stateMachineItem->process) {
            $stateMachineItem->process = $itemDto->getProcessNameOrFail();
        }
        if (!$stateMachineItem->state && $itemDto->hasStateName()) {
            $stateMachineItem->state = $itemDto->getStateNameOrFail();
        }

        if ($stateMachineItem->isNew() || $stateMachineItem->isDirty()) {
            $stateMachineItemsTable->saveOrFail($stateMachineItem);
        }

        return $stateMachineItem;
    }
}
