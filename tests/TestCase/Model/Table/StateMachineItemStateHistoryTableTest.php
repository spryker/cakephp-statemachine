<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineItemStateHistory;
use StateMachine\Model\Entity\StateMachineTransitionLog;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;

class StateMachineItemStateHistoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineItemStateHistoryTable
     */
    protected $StateMachineItemStateHistory;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineProcesses',
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
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStateHistory);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(StateMachineItemStateHistoryTable::class, $this->StateMachineItemStateHistory);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $result = $this->StateMachineItemStateHistory->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineItemStateHistory::class, $result);
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'state_machine_item_state_id' => 1,
            'identifier' => 1,
        ];
        $history = $this->StateMachineItemStateHistory->newEntity($data);
        $this->StateMachineItemStateHistory->saveOrFail($history);

        $this->assertNotEmpty($history->created);
    }

    /**
     * @return void
     */
    public function testGetHistory(): void
    {
        /** @var \StateMachine\Model\Entity\StateMachineItem $stateMachineItem */
        $stateMachineItem = $this->getTableLocator()->get('StateMachine.StateMachineItems')->find()
            ->firstOrFail();
        $stateMachineItem->state_machine_transition_log = new StateMachineTransitionLog();
        $stateMachineItem->state_machine_transition_log->state_machine_process_id = 1;

        $result = $this->StateMachineItemStateHistory->getHistory($stateMachineItem);
        $this->assertCount(1, $result);
    }
}
