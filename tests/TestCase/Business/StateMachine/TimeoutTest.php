<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\Core\Configure;
use Cake\Event\Event as CakeEvent;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Business\Logger\TransitionLog;
use StateMachine\Business\Logger\TransitionLogInterface;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\EventInterface;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\Process\TransitionInterface;
use StateMachine\Business\StateMachine\Persistence;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\Timeout;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Model\QueryContainer;
use StateMachine\Model\QueryContainerInterface;
use StateMachine\Model\Table\StateMachineItemStateLogsTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;

class TimeoutTest extends TestCase
{
    /**
     * @var string
     */
    protected const STATE_WITH_TIMEOUT = 'State with timeout';
    /**
     * @var int
     */
    protected const IDENTIFIER = 1;
    /**
     * @var string
     */
    protected const EVENT_NAME = 'Timeout event';
    /**
     * @var string
     */
    protected const PROCESS_NAME = 'Process name';

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStateLogsTable
     */
    protected $StateMachineItemStateLogs;

    /**
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected $StateMachineTransitionLogs;

    /**
     * @var \StateMachine\Model\Table\StateMachineProcessesTable
     */
    protected $StateMachineProcesses;

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStatesTable
     */
    protected $StateMachineItemStates;

    /**
     * @var \StateMachine\Model\Table\StateMachineTimeoutsTable
     */
    protected $StateMachineTimeouts;

    /**
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineTransitionLogs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStateLogs') ? [] : ['className' => StateMachineItemStateLogsTable::class];
        $this->StateMachineItemStateLogs = TableRegistry::getTableLocator()->get('StateMachineItemStateLogs', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineTransitionLogs') ? [] : ['className' => StateMachineTransitionLogsTable::class];
        $this->StateMachineTransitionLogs = TableRegistry::getTableLocator()->get('StateMachineTransitionLogs', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineProcesses') ? [] : ['className' => StateMachineProcessesTable::class];
        $this->StateMachineProcesses = TableRegistry::getTableLocator()->get('StateMachineProcesses', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStates') ? [] : ['className' => StateMachineItemStatesTable::class];
        $this->StateMachineItemStates = TableRegistry::getTableLocator()->get('StateMachineItemStates', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineTimeouts') ? [] : ['className' => StateMachineTimeoutsTable::class];
        $this->StateMachineTimeouts = TableRegistry::getTableLocator()->get('StateMachineTimeouts', $config);

        Configure::write('StateMachine.eventRepeatAction', 2);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStateLogs, $this->StateMachineTransitionLogs, $this->StateMachineProcesses, $this->StateMachineItemStates, $this->StateMachineTimeouts);
        Configure::delete('StateMachine.eventRepeatAction');

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testSetTimeoutShouldStoreNewTimeout(): void
    {
        $timeout = $this->createTimeout();
        $timeout->setNewTimeout(
            $this->createProcess(),
            $this->createItemDto()
        );

        $timeout = $this->StateMachineTimeouts->find()->where(['identifier' => static::IDENTIFIER])->first();

        $this->assertNotNull($timeout);
    }

    /**
     * @return void
     */
    public function testDropOldTimeoutShouldRemoveExpiredTimeoutsFromPersistence(): void
    {
        $currentTimeout = $this->StateMachineTimeouts->find()->where(['identifier' => static::IDENTIFIER])->first();
        $this->assertNotNull($currentTimeout);

        $timeout = $this->createTimeout();
        $timeout->dropOldTimeout(
            $this->createProcess(),
            static::STATE_WITH_TIMEOUT,
            $this->createItemDto()
        );

        $timeout = $this->StateMachineTimeouts->find()->where(['identifier' => static::IDENTIFIER])->first();

        $this->assertNull($timeout);
    }

    /**
     * @return void
     */
    public function testSetTimeoutShouldAlertIfRepeatActionIsSet(): void
    {
        $log = $this->StateMachineTransitionLogs->newEntity([
            'event' => static::EVENT_NAME . ' (timeout)',
            'source_state' => 'foo',
            'target_state' => 'bar',
            'identifier' => static::IDENTIFIER,
            'state_machine_process' => [
                'name' => static::PROCESS_NAME,
                'state_machine' => 'TestingSm',
            ],
            'locked' => false,
            'is_error' => false,
        ]);
        $log->state_machine_item_id = 1;
        $this->StateMachineTransitionLogs->saveOrFail($log);

        // This now will be the modulo number and triggers the event
        $log = $this->StateMachineTransitionLogs->newEntity([
            'event' => static::EVENT_NAME . ' (timeout)',
            'source_state' => 'foo',
            'target_state' => 'bar',
            'identifier' => static::IDENTIFIER,
            'state_machine_process_id' => $log->state_machine_process_id,
            'locked' => false,
            'is_error' => false,
        ]);
        $log->state_machine_item_id = 1;
        $this->StateMachineTransitionLogs->saveOrFail($log);

        $timeout = $this->createTimeout();
        $dispatched = false;
        $timeout->getEventManager()->on(
            'StateMachine.eventRepeatAction',
            function (CakeEvent $cakeEvent, Event $event, int $count, ItemDto $itemDto) use (&$dispatched) {
                $this->assertSame(static::EVENT_NAME, $event->getName());
                $dispatched = true;
            }
        );

        $timeout->setNewTimeout(
            $this->createProcess(),
            $this->createItemDto()
        );

        $this->assertTrue($dispatched);
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    protected function createProcess(): ProcessInterface
    {
        $process = new Process();
        $process->addState($this->createState());

        return $process;
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function createState(): StateInterface
    {
        $state = new State();
        $state->setName(static::STATE_WITH_TIMEOUT);
        $state->addOutgoingTransition($this->createTransition());

        return $state;
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    protected function createTransition(): TransitionInterface
    {
        $transition = new Transition();
        $transition->setEvent($this->createEvent());

        return $transition;
    }

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    protected function createEvent(): EventInterface
    {
        $event = new Event();
        $event->setName(static::EVENT_NAME);
        $event->setTimeout('1 DAY');

        return $event;
    }

    /**
     * @return \StateMachine\Dto\StateMachine\ItemDto
     */
    protected function createItemDto(): ItemDto
    {
        $itemDto = new ItemDto();
        $itemDto
            ->setStateName(static::STATE_WITH_TIMEOUT)
            ->setProcessName(static::PROCESS_NAME)
            ->setIdentifier(static::IDENTIFIER)
            ->setIdItemState(1)
            ->setIdStateMachineProcess(1);

        return $itemDto;
    }

    /**
     * @return \StateMachine\Business\StateMachine\TimeoutInterface|\Cake\Event\EventDispatcherInterface
     */
    protected function createTimeout(): TimeoutInterface
    {
        return new Timeout($this->createPersistence(), $this->createTransitionLog());
    }

    /**
     * @return \StateMachine\Model\QueryContainerInterface
     */
    protected function createQueryContainer(): QueryContainerInterface
    {
        return new QueryContainer();
    }

    /**
     * @return \StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistence(): PersistenceInterface
    {
        return new Persistence(
            $this->createQueryContainer(),
            $this->StateMachineItemStateLogs,
            $this->StateMachineProcesses,
            $this->StateMachineItemStates,
            $this->StateMachineTimeouts
        );
    }

    /**
     * @return \StateMachine\Business\Logger\TransitionLogInterface
     */
    protected function createTransitionLog(): TransitionLogInterface
    {
        return new TransitionLog($this->StateMachineTransitionLogs);
    }
}
