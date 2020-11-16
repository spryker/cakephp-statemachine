<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineItemStateLog;
use StateMachine\Model\Entity\StateMachineTransitionLog;
use StateMachine\Model\Table\StateMachineItemStateLogsTable;

class StateMachineItemStateLogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineItemStateLogsTable
     */
    protected $StateMachineItemStateLogs;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineItemStateLogs',
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
        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStateLogs') ? [] : ['className' => StateMachineItemStateLogsTable::class];
        $this->StateMachineItemStateLogs = TableRegistry::getTableLocator()->get('StateMachineItemStateLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStateLogs);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(StateMachineItemStateLogsTable::class, $this->StateMachineItemStateLogs);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $result = $this->StateMachineItemStateLogs->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineItemStateLog::class, $result);
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
        $log = $this->StateMachineItemStateLogs->newEntity($data);
        $this->StateMachineItemStateLogs->saveOrFail($log);

        $this->assertNotEmpty($log->created);
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

        $result = $this->StateMachineItemStateLogs->getHistory($stateMachineItem);
        $this->assertCount(1, $result);
    }
}
