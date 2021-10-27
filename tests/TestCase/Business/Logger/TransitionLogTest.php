<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Logger;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Logger\TransitionLog;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\Event;
use StateMachine\Dependency\StateMachineCommandInterface;
use StateMachine\Dependency\StateMachineConditionInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;

class TransitionLogTest extends TestCase
{
    /**
     * @var string
     */
    protected const SOURCE_STATE = 'source state';
    /**
     * @var string
     */
    protected const TARGET_STATE = 'target state';
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'Failure';
    /**
     * @var string
     */
    protected const EVENT_NAME = 'Event';
    /**
     * @var string
     */
    protected const STATE_NAME = 'state';
    /**
     * @var string
     */
    protected const PROCESS_NAME = 'process';

    /**
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected $StateMachineTransitionLogs;

    /**
     * @var \StateMachine\Model\Table\StateMachineItemsTable
     */
    protected $StateMachineItems;

    /**
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineTransitionLogs',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItems',
    ];

    /**
     * @var array
     */
    protected const QUERY_DATA = [
        ['foo' => 'bar'],
        ['param' => 'value'],
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
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
    public function tearDown(): void
    {
        unset($this->StateMachineTransitionLogs);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testLoggerPersistsAllProvidedData(): void
    {
        $itemDto = $this->createItemTransfer();

        $transitionLog = $this->createTransitionLog();
        $transitionLog->init([$itemDto]);

        $commandMock = $this->createCommandMock();
        $transitionLog->addCommand($itemDto, $commandMock);

        $conditionMock = $this->createConditionPluginMock();
        $transitionLog->addCondition($itemDto, $conditionMock);

        $transitionLog->addSourceState($itemDto, static::SOURCE_STATE);

        $transitionLog->addTargetState($itemDto, static::TARGET_STATE);

        $transitionLog->setErrorMessage(static::ERROR_MESSAGE);
        $transitionLog->setIsError(true);

        $event = new Event();
        $event->setName(static::EVENT_NAME);

        $transitionLog->setEvent($event);
        $transitionLog->save($itemDto);
        $transitionLog->saveAll();

        $stateMachineTransitionLogEntity = $this->StateMachineTransitionLogs->find()->last();

        $this->assertSame(get_class($commandMock), $stateMachineTransitionLogEntity->command);
        $this->assertSame(get_class($conditionMock), $stateMachineTransitionLogEntity->condition);
        $this->assertSame(static::SOURCE_STATE, $stateMachineTransitionLogEntity->source_state);
        $this->assertSame(static::TARGET_STATE, $stateMachineTransitionLogEntity->target_state);
        $this->assertSame($event->getName(), $stateMachineTransitionLogEntity->event);

        $this->StateMachineItems = TableRegistry::getTableLocator()->get('StateMachine.StateMachineItems');
        /** @var \StateMachine\Model\Entity\StateMachineItem $itemEntity */
        $itemEntity = $this->StateMachineItems->find()->last();
        $this->assertSame($itemDto->getIdentifierOrFail(), $itemEntity->identifier);
        $this->assertSame($itemDto->getStateMachineNameOrFail(), $itemEntity->state_machine);
        $this->assertSame($itemDto->getProcessNameOrFail(), $itemEntity->process);
        $this->assertSame(static::TARGET_STATE, $itemEntity->state);
        $this->assertSame($stateMachineTransitionLogEntity->id, $itemEntity->state_machine_transition_log_id);
    }

    /**
     * @return void
     */
    public function testWhenNonCliRequestUsedShouldExtractOutputParamsAndPersist(): void
    {
        $_SERVER[TransitionLog::QUERY_STRING] = $this->createQueryString(array_merge(static::QUERY_DATA[0], static::QUERY_DATA[1]));
        $itemDto = $this->createItemTransfer();

        $transitionLog = $this->createTransitionLog();
        $transitionLog->init([$itemDto]);

        $transitionLog->save($itemDto);
        $stateMachineTransitionLogEntity = $this->StateMachineTransitionLogs->find()->last();
        $storedParams = json_decode($stateMachineTransitionLogEntity->params);
        $this->assertSame($this->createQueryString(static::QUERY_DATA[0]), $storedParams[0]);
        $this->assertSame($this->createQueryString(static::QUERY_DATA[1]), $storedParams[1]);
    }

    /**
     * @return \StateMachine\Business\Logger\TransitionLogInterface
     */
    protected function createTransitionLog(): TransitionLogInterface
    {
        return new TransitionLog($this->StateMachineTransitionLogs);
    }

    /**
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    protected function createItemTransfer(): ItemDto
    {
        $itemDto = new ItemDto();
        $itemDto->setIdentifier(1);
        $itemDto->setEventName(static::EVENT_NAME);
        $itemDto->setIdItemState(1);
        $itemDto->setStateName(static::STATE_NAME);
        $itemDto->setProcessName(static::PROCESS_NAME);
        $itemDto->setIdStateMachineProcess(1);
        $itemDto->setStateMachineName(static::PROCESS_NAME);

        return $itemDto;
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineCommandInterface
     */
    protected function createCommandMock(): StateMachineCommandInterface
    {
        $commandMock = $this->getMockBuilder(StateMachineCommandInterface::class)->getMock();

        return $commandMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineConditionInterface
     */
    protected function createConditionPluginMock(): StateMachineConditionInterface
    {
        $conditionPluginMock = $this->getMockBuilder(StateMachineConditionInterface::class)->getMock();

        return $conditionPluginMock;
    }
}
