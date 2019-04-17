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
    public function setEvent(EventInterface $event)
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
    public function init(array $stateMachineItems)
    {
        $this->logEntities = [];
        foreach ($stateMachineItems as $stateMachineItem) {
            $logEntity = $this->initEntity($stateMachineItem);
            $this->logEntities[$stateMachineItem->getIdentifier()] = $logEntity;
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param \StateMachine\Dependency\CommandPluginInterface $command
     *
     * @return void
     */
    public function addCommand(ItemDto $stateMachineItemTransfer, CommandPluginInterface $command)
    {
        $this->logEntities[$stateMachineItemTransfer->getIdentifierOrFail()]->command = get_class($command);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param \StateMachine\Dependency\ConditionPluginInterface $condition
     *
     * @return void
     */
    public function addCondition(ItemDto $stateMachineItemTransfer, ConditionPluginInterface $condition)
    {
        $this->logEntities[$stateMachineItemTransfer->getIdentifierOrFail()]->condition = get_class($condition);
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return void
     */
    public function addSourceState(ItemDto $stateMachineItemTransfer, $stateName)
    {
        $this->logEntities[$stateMachineItemTransfer->getIdentifierOrFail()]->source_state = $stateName;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     * @param string $stateName
     *
     * @return void
     */
    public function addTargetState(ItemDto $stateMachineItemTransfer, $stateName)
    {
        $this->logEntities[$stateMachineItemTransfer->getIdentifierOrFail()]->target_state = $stateName;
    }

    /**
     * @param bool $error
     *
     * @return void
     */
    public function setIsError($error)
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
    public function setErrorMessage($errorMessage)
    {
        foreach ($this->logEntities as $logEntity) {
            $logEntity->error_message = $errorMessage;
        }
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @throws \RuntimeException
     *
     * @return \StateMachine\Model\Entity\StateMachineTransitionLog
     */
    protected function initEntity(ItemDto $stateMachineItemTransfer)
    {
        $stateMachineTransitionLogEntity = $this->createStateMachineTransitionLogEntity();
        $stateMachineTransitionLogEntity->identifier = $stateMachineItemTransfer->getIdentifierOrFail();
        $stateMachineTransitionLogEntity->state_machine_process_id = $stateMachineItemTransfer->getIdStateMachineProcessOrFail();

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
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return void
     */
    public function save(ItemDto $stateMachineItemTransfer)
    {
        //var_dump($this->logEntities[$stateMachineItemTransfer->getIdentifier()]);die;
        $this->stateMachineTransitionLogsTable->save($this->logEntities[$stateMachineItemTransfer->getIdentifier()]);
    }

    /**
     * @return void
     */
    public function saveAll()
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
    protected function getParamsFromQueryString($queryString)
    {
        return explode('&', $queryString);
    }
}
