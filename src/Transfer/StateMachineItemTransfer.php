<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Transfer;

/**
 * !!! THIS FILE IS AUTO-GENERATED, EVERY CHANGE WILL BE LOST WITH THE NEXT RUN OF TRANSFER GENERATOR
 * !!! DO NOT CHANGE ANYTHING IN THIS FILE
 */
class StateMachineItemTransfer extends AbstractTransfer
{
    public const IDENTIFIER = 'identifier';

    public const ID_STATE_MACHINE_PROCESS = 'idStateMachineProcess';

    public const ID_ITEM_STATE = 'idItemState';

    public const PROCESS_NAME = 'processName';

    public const STATE_MACHINE_NAME = 'stateMachineName';

    public const STATE_NAME = 'stateName';

    public const EVENT_NAME = 'eventName';

    public const CREATED_AT = 'createdAt';

    /**
     * @var int
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $idStateMachineProcess;

    /**
     * @var string
     */
    protected $idItemState;

    /**
     * @var string
     */
    protected $processName;

    /**
     * @var string
     */
    protected $stateMachineName;

    /**
     * @var string
     */
    protected $stateName;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var array
     */
    protected $transferPropertyNameMap = [
        'identifier' => 'identifier',
        'Identifier' => 'identifier',
        'id_state_machine_process' => 'idStateMachineProcess',
        'idStateMachineProcess' => 'idStateMachineProcess',
        'IdStateMachineProcess' => 'idStateMachineProcess',
        'id_item_state' => 'idItemState',
        'idItemState' => 'idItemState',
        'IdItemState' => 'idItemState',
        'process_name' => 'processName',
        'processName' => 'processName',
        'ProcessName' => 'processName',
        'state_machine_name' => 'stateMachineName',
        'stateMachineName' => 'stateMachineName',
        'StateMachineName' => 'stateMachineName',
        'state_name' => 'stateName',
        'stateName' => 'stateName',
        'StateName' => 'stateName',
        'event_name' => 'eventName',
        'eventName' => 'eventName',
        'EventName' => 'eventName',
        'created_at' => 'createdAt',
        'createdAt' => 'createdAt',
        'CreatedAt' => 'createdAt',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::IDENTIFIER => [
            'type' => 'int',
            'name_underscore' => 'identifier',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::ID_STATE_MACHINE_PROCESS => [
            'type' => 'string',
            'name_underscore' => 'id_state_machine_process',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::ID_ITEM_STATE => [
            'type' => 'string',
            'name_underscore' => 'id_item_state',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::PROCESS_NAME => [
            'type' => 'string',
            'name_underscore' => 'process_name',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::STATE_MACHINE_NAME => [
            'type' => 'string',
            'name_underscore' => 'state_machine_name',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::STATE_NAME => [
            'type' => 'string',
            'name_underscore' => 'state_name',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::EVENT_NAME => [
            'type' => 'string',
            'name_underscore' => 'event_name',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::CREATED_AT => [
            'type' => 'string',
            'name_underscore' => 'created_at',
            'is_collection' => false,
            'is_transfer' => false,
        ],
    ];

    /**
     * @module StateMachine
     *
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        $this->modifiedProperties[self::IDENTIFIER] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireIdentifier()
    {
        $this->assertPropertyIsSet(self::IDENTIFIER);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param int $idStateMachineProcess
     *
     * @return $this
     */
    public function setIdStateMachineProcess($idStateMachineProcess)
    {
        $this->idStateMachineProcess = $idStateMachineProcess;
        $this->modifiedProperties[self::ID_STATE_MACHINE_PROCESS] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return int
     */
    public function getIdStateMachineProcess()
    {
        return $this->idStateMachineProcess;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireIdStateMachineProcess()
    {
        $this->assertPropertyIsSet(self::ID_STATE_MACHINE_PROCESS);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param int $idItemState
     *
     * @return $this
     */
    public function setIdItemState($idItemState)
    {
        $this->idItemState = $idItemState;
        $this->modifiedProperties[self::ID_ITEM_STATE] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return int
     */
    public function getIdItemState()
    {
        return $this->idItemState;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireIdItemState()
    {
        $this->assertPropertyIsSet(self::ID_ITEM_STATE);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param string $processName
     *
     * @return $this
     */
    public function setProcessName($processName)
    {
        $this->processName = $processName;
        $this->modifiedProperties[self::PROCESS_NAME] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return string
     */
    public function getProcessName()
    {
        return $this->processName;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireProcessName()
    {
        $this->assertPropertyIsSet(self::PROCESS_NAME);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param string $stateMachineName
     *
     * @return $this
     */
    public function setStateMachineName($stateMachineName)
    {
        $this->stateMachineName = $stateMachineName;
        $this->modifiedProperties[self::STATE_MACHINE_NAME] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return string
     */
    public function getStateMachineName()
    {
        return $this->stateMachineName;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireStateMachineName()
    {
        $this->assertPropertyIsSet(self::STATE_MACHINE_NAME);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param string $stateName
     *
     * @return $this
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;
        $this->modifiedProperties[self::STATE_NAME] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireStateName()
    {
        $this->assertPropertyIsSet(self::STATE_NAME);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param string $eventName
     *
     * @return $this
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
        $this->modifiedProperties[self::EVENT_NAME] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireEventName()
    {
        $this->assertPropertyIsSet(self::EVENT_NAME);

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        $this->modifiedProperties[self::CREATED_AT] = true;

        return $this;
    }

    /**
     * @module StateMachine
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @module StateMachine
     *
     * @return $this
     */
    public function requireCreatedAt()
    {
        $this->assertPropertyIsSet(self::CREATED_AT);

        return $this;
    }
}
