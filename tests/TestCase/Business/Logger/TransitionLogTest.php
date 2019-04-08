<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Logger;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Logger\TransitionLog;
use StateMachine\Business\Process\Event;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;
use StateMachine\Transfer\StateMachineItemTransfer;

class TransitionLogTest extends TestCase
{
    protected const SOURCE_STATE = 'source state';
    protected const TARGET_STATE = 'target state';
    protected const ERROR_MESSAGE = 'Failure';
    protected const EVENT_NAME = 'Event';
    protected const STATE_NAME = 'state';
    protected const PROCESS_NAME = 'process';

    /**
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    public $StateMachineTransitionLogs;

    /**
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineTransitionLogs',
        'plugin.StateMachine.StateMachineProcesses',
    ];

    protected const QUERY_DATA = [
        ['foo' => 'bar'],
        ['param' => 'value'],
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineTransitionLogs') ? [] : ['className' => StateMachineTransitionLogsTable::class];
        $this->StateMachineTransitionLogs = TableRegistry::getTableLocator()->get('StateMachineTransitionLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->StateMachineTransitionLogs);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testLoggerPersistsAllProvidedData()
    {
        $stateMachineItemTransfer = $this->createItemTransfer();

        $transitionLog = $this->createTransitionLog();
        $transitionLog->init([$stateMachineItemTransfer]);

        $commandMock = $this->createCommandMock();
        $transitionLog->addCommand($stateMachineItemTransfer, $commandMock);

        $conditionMock = $this->createConditionPluginMock();
        $transitionLog->addCondition($stateMachineItemTransfer, $conditionMock);

        $transitionLog->addSourceState($stateMachineItemTransfer, static::SOURCE_STATE);

        $transitionLog->addTargetState($stateMachineItemTransfer, static::TARGET_STATE);

        $transitionLog->setErrorMessage(static::ERROR_MESSAGE);
        $transitionLog->setIsError(true);

        $event = new Event();
        $event->setName(static::EVENT_NAME);

        $transitionLog->setEvent($event);
        $transitionLog->save($stateMachineItemTransfer);
        $transitionLog->saveAll();

        $stateMachineTransitionLogEntity = $this->StateMachineTransitionLogs->find()->last();

        $this->assertEquals(get_class($commandMock), $stateMachineTransitionLogEntity->command);
        $this->assertEquals(get_class($conditionMock), $stateMachineTransitionLogEntity->condition);
        $this->assertEquals(static::SOURCE_STATE, $stateMachineTransitionLogEntity->source_state);
        $this->assertEquals(static::TARGET_STATE, $stateMachineTransitionLogEntity->target_state);
        $this->assertEquals($event->getName(), $stateMachineTransitionLogEntity->event);
    }

    /**
     * @return void
     */
    public function testWhenNonCliRequestUsedShouldExtractOutputParamsAndPersist()
    {
        $_SERVER[TransitionLog::QUERY_STRING] = $this->createQueryString(array_merge(static::QUERY_DATA[0], static::QUERY_DATA[1]));
        $stateMachineTransitionLogEntity = $this->StateMachineTransitionLogs->newEntity();
        $stateMachineItemTransfer = $this->createItemTransfer();

        $transitionLog = $this->createTransitionLog();
        $transitionLog->init([$stateMachineItemTransfer]);

        $storedParams = $stateMachineTransitionLogEntity->params;

        $this->assertEquals($this->createQueryString(static::QUERY_DATA[0]), $storedParams[0]);
        $this->assertEquals($this->createQueryString(static::QUERY_DATA[1]), $storedParams[1]);
    }

    /**
     * @param \StateMachine\Model\Entity\StateMachineTransitionLog $stateMachineTransitionLogEntityMock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Logger\TransitionLog
     */
    protected function createTransitionLog()
    {
        return new TransitionLog($this->StateMachineTransitionLogs);
    }

    /**
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected function createItemTransfer(): StateMachineItemTransfer
    {
        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setIdentifier($this->createIdentifier());
        $stateMachineItemTransfer->setEventName(static::EVENT_NAME);
        $stateMachineItemTransfer->setIdItemState(1);
        $stateMachineItemTransfer->setStateName(static::STATE_NAME);
        $stateMachineItemTransfer->setProcessName(static::PROCESS_NAME);
        $stateMachineItemTransfer->setIdStateMachineProcess(1);

        return $stateMachineItemTransfer;
    }

    /**
     * @param array $queryParts
     *
     * @return string
     */
    protected function createQueryString(array $queryParts): string
    {
        return http_build_query($queryParts);
    }

    /**
     * @return string
     */
    protected function createIdentifier(): string
    {
        return sha1(1);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\CommandPluginInterface
     */
    protected function createCommandMock()
    {
        $commandMock = $this->getMockBuilder(CommandPluginInterface::class)->getMock();

        return $commandMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\ConditionPluginInterface
     */
    protected function createConditionPluginMock()
    {
        $conditionPluginMock = $this->getMockBuilder(ConditionPluginInterface::class)->getMock();

        return $conditionPluginMock;
    }
}
