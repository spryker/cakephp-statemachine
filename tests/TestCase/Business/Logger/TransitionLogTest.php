<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Logger;

use StateMachine\Business\Logger\TransitionLog;
use StateMachine\Business\Process\Event;
use StateMachine\Model\Entity\StateMachineTransitionLog;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;
use StateMachine\Test\TestCase\Mocks\StateMachineMocks;
use StateMachine\Transfer\StateMachineItemTransfer;

class TransitionLogTest extends StateMachineMocks
{
    protected const SOURCE_STATE = 'source state';
    protected const TARGET_STATE = 'target state';
    protected const ERROR_MESSAGE = 'Failure';
    protected const EVENT_NAME = 'Event';
    protected const STATE_NAME = 'state';
    protected const PROCESS_NAME = 'process';

    protected const QUERY_DATA = [
        ['foo' => 'bar'],
        ['param' => 'value'],
    ];

    /**
     * @return void
     */
    public function testLoggerPersistsAllProvidedData()
    {
        $stateMachineTransitionLogEntityMock = $this->createTransitionLogEntityMock();
        $stateMachineTransitionLogsTable = $this->createTransitionLogTableMock();
        $stateMachineTransitionLogsTable
            ->method('save')
            ->willReturn($stateMachineTransitionLogEntityMock);

        $stateMachineItemTransfer = $this->createItemTransfer();

        $transitionLog = $this->createTransitionLog($stateMachineTransitionLogEntityMock);
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

        $this->assertEquals(get_class($commandMock), $stateMachineTransitionLogEntityMock->command);
        $this->assertEquals(get_class($conditionMock), $stateMachineTransitionLogEntityMock->condition);
        $this->assertEquals(static::SOURCE_STATE, $stateMachineTransitionLogEntityMock->source_state);
        $this->assertEquals(static::TARGET_STATE, $stateMachineTransitionLogEntityMock->target_state);
        $this->assertEquals($event->getName(), $stateMachineTransitionLogEntityMock->event);
    }

    /**
     * @return void
     */
    public function testWhenNonCliRequestUsedShouldExtractOutputParamsAndPersist()
    {
        $_SERVER[TransitionLog::QUERY_STRING] = $this->createQueryString([static::QUERY_DATA[0], static::QUERY_DATA[1]]);
        $stateMachineTransitionLogEntityMock = $this->createTransitionLogEntityMock();
        $stateMachineItemTransfer = $this->createItemTransfer();

        $transitionLog = $this->createTransitionLog($stateMachineTransitionLogEntityMock);
        $transitionLog->init([$stateMachineItemTransfer]);

        $storedParams = $stateMachineTransitionLogEntityMock->params;

        $this->assertEquals($this->createQueryString(static::QUERY_DATA[0]), $storedParams[0]);
        $this->assertEquals($this->createQueryString(static::QUERY_DATA[1]), $storedParams[1]);
    }

    /**
     * @param \StateMachine\Model\Entity\StateMachineTransitionLog $stateMachineTransitionLogEntityMock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Logger\TransitionLog
     */
    protected function createTransitionLog(StateMachineTransitionLog $stateMachineTransitionLogEntityMock)
    {
        $partialTransitionLogMock = $this->getMockBuilder(TransitionLog::class)
            ->setMethods(['createStateMachineTransitionLogEntity'])
            ->setConstructorArgs([$this->createTransitionLogTableMock()])
            ->getMock();

        $partialTransitionLogMock->method('createStateMachineTransitionLogEntity')
            ->willReturn($stateMachineTransitionLogEntityMock);

        return $partialTransitionLogMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Model\Entity\StateMachineTransitionLog
     */
    protected function createTransitionLogEntityMock()
    {
        return $this->getMockBuilder(StateMachineTransitionLog::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected function createTransitionLogTableMock()
    {
        return $this->getMockBuilder(StateMachineTransitionLogsTable::class)->getMock();
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
}
