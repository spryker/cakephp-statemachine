<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
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
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;

class TimeoutTest extends TestCase
{
    protected const STATE_WITH_TIMEOUT = 'State with timeout';
    protected const IDENTIFIER = 1;
    protected const EVENT_NAME = 'Timeout event';

    /**
     * @var \StateMachine\Model\Table\StateMachineItemStateHistoryTable
     */
    protected $StateMachineItemStateHistory;

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
    public $fixtures = [
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTimeouts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStateHistory') ? [] : ['className' => StateMachineItemStateHistoryTable::class];
        $this->StateMachineItemStateHistory = TableRegistry::getTableLocator()->get('StateMachineItemStateHistory', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineProcesses') ? [] : ['className' => StateMachineProcessesTable::class];
        $this->StateMachineProcesses = TableRegistry::getTableLocator()->get('StateMachineProcesses', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStates') ? [] : ['className' => StateMachineItemStatesTable::class];
        $this->StateMachineItemStates = TableRegistry::getTableLocator()->get('StateMachineItemStates', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineTimeouts') ? [] : ['className' => StateMachineTimeoutsTable::class];
        $this->StateMachineTimeouts = TableRegistry::getTableLocator()->get('StateMachineTimeouts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStateHistory, $this->StateMachineProcesses, $this->StateMachineItemStates, $this->StateMachineTimeouts);

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
            ->setIdentifier(static::IDENTIFIER)
            ->setIdItemState(1)
            ->setIdStateMachineProcess(1);

        return $itemDto;
    }

    /**
     * @return \StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected function createTimeout(): TimeoutInterface
    {
        return new Timeout($this->createPersistence());
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
            $this->StateMachineItemStateHistory,
            $this->StateMachineProcesses,
            $this->StateMachineItemStates,
            $this->StateMachineTimeouts
        );
    }
}
