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
class StateMachineProcessTransfer extends AbstractTransfer
{
    public const PROCESS_NAME = 'processName';

    public const STATE_MACHINE_NAME = 'stateMachineName';

    /**
     * @var string
     */
    protected $processName;

    /**
     * @var string
     */
    protected $stateMachineName;

    /**
     * @var array
     */
    protected $transferPropertyNameMap = [
        'process_name' => 'processName',
        'processName' => 'processName',
        'ProcessName' => 'processName',
        'state_machine_name' => 'stateMachineName',
        'stateMachineName' => 'stateMachineName',
        'StateMachineName' => 'stateMachineName',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
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
    ];

    /**
     * @param string|null $processName
     *
     * @return $this
     */
    public function setProcessName(?string $processName)
    {
        $this->processName = $processName;
        $this->modifiedProperties[self::PROCESS_NAME] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProcessName(): ?string
    {
        return $this->processName;
    }

    /**
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
}
