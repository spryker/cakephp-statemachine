<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Business;

use App\StateMachine\TestStateMachineHandler;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;
use StateMachine\Business\StateMachineFacade;
use StateMachine\Business\StateMachineFacadeInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\Model\Table\StateMachineItemsTable;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;
use StateMachine\Model\Table\StateMachineItemStatesTable;
use StateMachine\Model\Table\StateMachineProcessesTable;
use StateMachine\Model\Table\StateMachineTimeoutsTable;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;

class StateMachineFacadeTest extends TestCase
{
    protected const TESTING_SM = 'TestingSm';
    protected const TEST_PROCESS_NAME = 'TestProcess';
    protected const TEST_PROCESS_WITH_LOOP_NAME = 'TestProcessWithLoop';
    protected const TEST_PROCESS_WITH_ERROR_NAME = 'TestProcessWithError';
    protected const TEST_PROCESS_WITH_COMMAND_ERROR_NAME = 'TestProcessWithCommandError';

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
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected $StateMachineTransitionLogs;

    /**
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineLocks',
        'plugin.StateMachine.StateMachineTransitionLogs',
        'plugin.StateMachine.StateMachineItems',
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

        $config = TableRegistry::getTableLocator()->exists('StateMachineTransitionLogs') ? [] : ['className' => StateMachineTransitionLogsTable::class];
        $this->StateMachineTransitionLogs = TableRegistry::getTableLocator()->get('StateMachineTransitionLogs', $config);

        $config = TableRegistry::getTableLocator()->exists('StateMachineItems') ? [] : ['className' => StateMachineItemsTable::class];
        $this->StateMachineItems = TableRegistry::getTableLocator()->get('StateMachineItems', $config);
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
    public function testTriggerForNewStateMachineItemWhenInitialProcessIsSuccessShouldNotifyHandlerStateChange(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $triggerResult = $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineProcessEntity = $this->StateMachineProcesses
            ->find()
            ->where([
                'name' => $processName,
                'state_machine' => static::TESTING_SM,
            ])
            ->first();

        $stateMachineItemStateEntity = $this->StateMachineItemStates
            ->find()
            ->matching($this->StateMachineProcesses->getAlias())
            ->where([
                $this->StateMachineItemStates->aliasField('state_machine_process_id') => $stateMachineProcessEntity->id,
                $this->StateMachineItemStates->aliasField('name') => $stateMachineHandler->getInitialStateForProcess($processName),
            ])
            ->first();

        $this->assertNotEmpty($stateMachineItemStateEntity);
        $this->assertSame(3, $triggerResult);
        $this->assertSame($identifier, $itemDto->getIdentifier());
        $this->assertSame('order exported', $itemDto->getStateName());
        $this->assertSame($processName, $itemDto->getProcessName());
    }

    /**
     * Last process in list should be used in this case.
     *
     * @return void
     */
    public function testTriggerForNewStateMachineItemWithoutSpecificProcess(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $triggerResult = $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineProcessEntity = $this->StateMachineProcesses
            ->find()
            ->where([
                'name' => $processName,
                'state_machine' => static::TESTING_SM,
            ])
            ->first();

        $stateMachineItemStateEntity = $this->StateMachineItemStates
            ->find()
            ->matching($this->StateMachineProcesses->getAlias())
            ->where([
                $this->StateMachineItemStates->aliasField('state_machine_process_id') => $stateMachineProcessEntity->id,
                $this->StateMachineItemStates->aliasField('name') => $stateMachineHandler->getInitialStateForProcess($processName),
            ])
            ->first();

        $this->assertNotEmpty($stateMachineItemStateEntity);
        $this->assertSame(3, $triggerResult);
        $this->assertSame($identifier, $itemDto->getIdentifier());
        $this->assertSame('order exported', $itemDto->getStateName());
        $this->assertSame($processName, $itemDto->getProcessName());
    }

    /**
     * @return void
     */
    public function testTriggerEventForItemWithManualEventShouldMoveToNextStateWithManualEvent(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $triggerResult = $stateMachineFacade->triggerEvent('ship order', $itemDto);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $this->assertSame(2, $triggerResult);
        $this->assertSame('waiting for payment', $itemDto->getStateName());
        $this->assertSame($processName, $itemDto->getProcessName());
        $this->assertSame($identifier, $itemDto->getIdentifier());
    }

    /**
     * @return void
     */
    public function testTriggerEventConditionFailureLogsTransition(): void
    {
        $processName = static::TEST_PROCESS_WITH_ERROR_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $failed = false;
        try {
            $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);
        } catch (InvalidArgumentException $exception) {
            $failed = true;
        }
        $this->assertTrue($failed, 'Should have thrown exception');

        $itemDto = $stateMachineHandler->getItemStateUpdated();
        $this->assertSame('invoice sent', $itemDto->getStateName());
        $this->assertSame($identifier, $itemDto->getIdentifier());

        $stateMachineItem = $this->StateMachineItems->find()->where(['state_machine' => static::TESTING_SM, 'identifier' => $identifier])->firstOrFail();

        $logs = $this->StateMachineTransitionLogs->getLogs($stateMachineItem->id);
        $this->assertCount(3, $logs);

        $lastLog = array_shift($logs);
        $this->assertSame('export order (on enter)', $lastLog->event);
        $this->assertSame('invoice sent', $lastLog->source_state);
        $this->assertNull($lastLog->target_state);
        $this->assertTrue($lastLog->is_error);
        $this->assertContains('Test exception for identity', $lastLog->error_message);
    }

    /**
     * @return void
     */
    public function testTriggerEventCommandFailureLogsTransition(): void
    {
        $processName = static::TEST_PROCESS_WITH_COMMAND_ERROR_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $failed = false;
        try {
            $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);
        } catch (InvalidArgumentException $exception) {
            $failed = true;
        }
        $this->assertTrue($failed, 'Should have thrown exception');

        $itemDto = $stateMachineHandler->getItemStateUpdated();
        $this->assertSame('invoice created', $itemDto->getStateName());
        $this->assertSame($identifier, $itemDto->getIdentifier());

        $stateMachineItem = $this->StateMachineItems->find()->where(['state_machine' => static::TESTING_SM, 'identifier' => $identifier])->firstOrFail();

        $logs = $this->StateMachineTransitionLogs->getLogs($stateMachineItem->id);
        $this->assertCount(2, $logs);

        $lastLog = array_shift($logs);
        $this->assertSame('send invoice', $lastLog->event);
        $this->assertSame('invoice created', $lastLog->source_state);
        $this->assertNull($lastLog->target_state);
        $this->assertTrue($lastLog->is_error);
        $this->assertContains('Test exception for identity', $lastLog->error_message);
    }

    /**
     * @return void
     */
    public function testGetProcessesShouldReturnListOfProcessesAddedToHandler(): void
    {
        $processName = static::TEST_PROCESS_NAME;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $processList = $stateMachineFacade->getProcesses(static::TESTING_SM);

        $this->assertCount(1, $processList);

        $processDto = array_pop($processList);
        $this->assertSame($processName, $processDto->getProcessName());
    }

    /**
     * @return void
     */
    public function testGetStateMachineProcessIdShouldReturnIdStoredInPersistence(): void
    {
        $processName = static::TEST_PROCESS_NAME;

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $processId = $stateMachineFacade->getStateMachineProcessId($processDto);

        $stateMachineProcessEntity = $this->StateMachineProcesses
            ->find()
            ->where([
                'name' => $processName,
                'state_machine' => static::TESTING_SM,
            ])
            ->first();

        $this->assertSame($stateMachineProcessEntity->id, $processId);
    }

    /**
     * @return void
     */
    public function testGetManualEventsForStateMachineItemShouldReturnAllManualEventsForProvidedState(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $manualEvents = $stateMachineFacade->getManualEventsForStateMachineItem($itemDto);

        $this->assertSame('order exported', $itemDto->getStateName());
        $this->assertCount(2, $manualEvents);

        $manualEvent = array_pop($manualEvents);
        $this->assertSame('check with condition', $manualEvent);

        $manualEvent = array_pop($manualEvents);
        $this->assertSame('ship order', $manualEvent);
    }

    /**
     * @return void
     */
    public function testGetManualEventForStateMachineItemsShouldReturnAllEventsForProvidedStates(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $firstItemIdentifier = 1985;
        $secondItemIdentifier = 1988;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineItems = [];
        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $firstItemIdentifier);
        $stateMachineItems[$firstItemIdentifier] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $secondItemIdentifier);
        $stateMachineItems[$secondItemIdentifier] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('ship order', $stateMachineItems[$secondItemIdentifier]);

        $manualEvents = $stateMachineFacade->getManualEventsForStateMachineItems($stateMachineItems);

        $this->assertCount(2, $manualEvents);

        $firstItemManualEvents = $manualEvents[$firstItemIdentifier];
        $secondItemManualEvents = $manualEvents[$secondItemIdentifier];

        $manualEvent = array_pop($firstItemManualEvents);
        $this->assertSame('check with condition', $manualEvent);

        $manualEvent = array_pop($firstItemManualEvents);
        $this->assertSame('ship order', $manualEvent);

        $manualEvent = array_pop($secondItemManualEvents);
        $this->assertSame('payment received', $manualEvent);
    }

    /**
     * @return void
     */
    public function testGetProcessedStateMachineItemsShouldReturnItemsByProvidedStateIdsStoredInPersistence(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $firstItemIdentifier = 1985;
        $secondItemIdentifier = 1988;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        /** @var \StateMachine\Dto\StateMachine\ItemDto[] $stateMachineItems */
        $stateMachineItems = [];
        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $firstItemIdentifier);
        $stateMachineItems[] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $secondItemIdentifier);
        $stateMachineItems[] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        /** @var \StateMachine\Dto\StateMachine\ItemDto[] $updatedStateMachineItems */
        $updatedStateMachineItems = $stateMachineFacade->getProcessedStateMachineItems($stateMachineItems);

        $this->assertCount(2, $updatedStateMachineItems);

        $firstUpdatedItemDto = $updatedStateMachineItems[0];
        $firstBeforeUpdateItemDto = $stateMachineItems[0];
        $this->assertSame(
            $firstUpdatedItemDto->getIdItemState(),
            $firstBeforeUpdateItemDto->getIdItemState()
        );
        $this->assertSame(
            $firstUpdatedItemDto->getProcessName(),
            $firstBeforeUpdateItemDto->getProcessName()
        );
        $this->assertSame(
            $firstUpdatedItemDto->getIdStateMachineProcess(),
            $firstBeforeUpdateItemDto->getIdStateMachineProcess()
        );
        $this->assertSame(
            $firstUpdatedItemDto->getStateName(),
            $firstBeforeUpdateItemDto->getStateName()
        );
        $this->assertSame(
            $firstUpdatedItemDto->getIdentifier(),
            $firstBeforeUpdateItemDto->getIdentifier()
        );

        $secondUpdatedItemDto = $updatedStateMachineItems[1];
        $secondBeforeUpdateItemDto = $stateMachineItems[1];
        $this->assertSame(
            $secondUpdatedItemDto->getIdItemState(),
            $secondBeforeUpdateItemDto->getIdItemState()
        );
        $this->assertSame(
            $secondUpdatedItemDto->getProcessName(),
            $secondBeforeUpdateItemDto->getProcessName()
        );
        $this->assertSame(
            $secondUpdatedItemDto->getIdStateMachineProcess(),
            $secondBeforeUpdateItemDto->getIdStateMachineProcess()
        );
        $this->assertSame(
            $secondUpdatedItemDto->getStateName(),
            $secondBeforeUpdateItemDto->getStateName()
        );
        $this->assertSame(
            $secondUpdatedItemDto->getIdentifier(),
            $secondBeforeUpdateItemDto->getIdentifier()
        );
    }

    /**
     * @return void
     */
    public function testGetProcessedItemDtoShouldReturnItemTransfer(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $firstItemIdentifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $firstItemIdentifier);
        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $updatedItemDto = $stateMachineFacade
            ->getProcessedItemDto($itemDto);

        $this->assertSame(
            $updatedItemDto->getIdItemState(),
            $itemDto->getIdItemState()
        );
        $this->assertSame(
            $updatedItemDto->getProcessName(),
            $itemDto->getProcessName()
        );
        $this->assertSame(
            $updatedItemDto->getIdStateMachineProcess(),
            $itemDto->getIdStateMachineProcess()
        );
        $this->assertSame(
            $updatedItemDto->getStateName(),
            $itemDto->getStateName()
        );
        $this->assertSame(
            $updatedItemDto->getIdentifier(),
            $itemDto->getIdentifier()
        );
    }

    /**
     * @return void
     */
    public function testGetStateHistoryByStateItemIdentifierShouldReturnAllHistoryForThatItem(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);
        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineItemsTransfer = $stateMachineFacade->getStateHistoryByStateItemIdentifier(
            $itemDto->getIdStateMachineProcess(),
            $identifier
        );

        $this->assertCount(3, $stateMachineItemsTransfer);

        $itemDto = $stateMachineItemsTransfer[0];
        $this->assertSame('invoice created', $itemDto->getStateName());

        $itemDto = $stateMachineItemsTransfer[1];
        $this->assertSame('invoice sent', $itemDto->getStateName());

        $itemDto = $stateMachineItemsTransfer[2];
        $this->assertSame('order exported', $itemDto->getStateName());
    }

    /**
     * @return void
     */
    public function testGetItemsWithFlagShouldReturnListOfStateMachineItemsWithGivenFlag(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $stateMachineItemsWithGivenFlag = $stateMachineFacade->getItemsWithFlag(
            $processDto,
            'Flag1'
        );

        $this->assertCount(2, $stateMachineItemsWithGivenFlag);

        $itemDto = $stateMachineItemsWithGivenFlag[0];
        $this->assertInstanceOf(ItemDto::class, $itemDto);
        $this->assertSame('invoice created', $itemDto->getStateName());
        $this->assertSame($identifier, $itemDto->getIdentifier());

        $itemDto = $stateMachineItemsWithGivenFlag[1];
        $this->assertSame('invoice sent', $itemDto->getStateName());
        $this->assertSame($identifier, $itemDto->getIdentifier());

        $stateMachineItemsWithGivenFlag = $stateMachineFacade->getItemsWithFlag(
            $processDto,
            'Flag2'
        );

        $this->assertCount(1, $stateMachineItemsWithGivenFlag);
    }

    /**
     * @return void
     */
    public function testCheckConditionsShouldProcessStatesWithConditionAndWithoutEvent(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('check with condition', $itemDto);

        $stateMachineHandler->setStateMachineItemsByStateIds([$itemDto]);

        $stateMachineFacade->checkConditions(static::TESTING_SM);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $this->assertSame('waiting for payment', $itemDto->getStateName());
    }

    /**
     * @return void
     */
    public function testCheckTimeoutsShouldMoveStatesWithExpiredTimeouts(): void
    {
        $processName = static::TEST_PROCESS_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('ship order', $itemDto);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineItemEventTimeoutEntity = $this->StateMachineTimeouts
            ->find()
            ->where([
                'identifier' => $itemDto->getIdentifier(),
                'state_machine_process_id' => $itemDto->getIdStateMachineProcess(),
            ])
            ->first();

        $stateMachineItemEventTimeoutEntity->timeout = new FrozenTime('1985-07-01');
        $this->StateMachineTimeouts->saveOrFail($stateMachineItemEventTimeoutEntity);

        $affectedItems = $stateMachineFacade->checkTimeouts(static::TESTING_SM);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $this->assertSame(1, $affectedItems);
        $this->assertSame('reminder I sent', $itemDto->getStateName());
    }

    /**
     * @return void
     */
    public function testLoopDoesNotCauseExceptions(): void
    {
        $processName = static::TEST_PROCESS_WITH_LOOP_NAME;
        $identifier = 1985;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName(static::TESTING_SM);

        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('enter loop action', $itemDto);
        $triggerResult = $stateMachineFacade->triggerEvent('loop exit action', $itemDto);

        $itemDto = $stateMachineHandler->getItemStateUpdated();

        $this->assertSame(1, $triggerResult);
        $this->assertSame('done', $itemDto->getStateName());
        $this->assertSame($processName, $itemDto->getProcessName());
        $this->assertSame($identifier, $itemDto->getIdentifier());
    }

    /**
     * @return void
     */
    public function testStateMachineExistsReturnsTrueWhenStateMachineHasHandler(): void
    {
        // Assign
        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineName = $stateMachineHandler->getStateMachineName();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);
        $expectedResult = true;

        // Act
        $actualResult = $stateMachineFacade->stateMachineExists($stateMachineName);

        // Assert
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return void
     */
    public function testStateMachineExistsReturnsFalseWhenStateMachineHasNoHandler(): void
    {
        // Assign
        $stateMachineHandler = $this->createTestStateMachineHandler();
        $stateMachineName = $stateMachineHandler->getStateMachineName() . 'SomethingElse';
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);
        $expectedResult = false;

        // Act
        $actualResult = $stateMachineFacade->stateMachineExists($stateMachineName);

        // Assert
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param \StateMachine\Dependency\StateMachineHandlerInterface $stateMachineHandler
     *
     * @return \StateMachine\Business\StateMachineFacadeInterface
     */
    protected function createStateMachineFacade(StateMachineHandlerInterface $stateMachineHandler): StateMachineFacadeInterface
    {
        Configure::write('StateMachine.handlers', [
            $stateMachineHandler,
        ]);
        Configure::write('StateMachine.pathToXml', __DIR__ . DS . '..' . DS . '..' . DS . 'test_files' . DS);

        return new StateMachineFacade();
    }

    /**
     * @return \App\StateMachine\TestStateMachineHandler
     */
    protected function createTestStateMachineHandler(): StateMachineHandlerInterface
    {
        return new TestStateMachineHandler();
    }
}
