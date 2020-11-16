<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineTransitionLog;
use StateMachine\Model\Table\StateMachineTransitionLogsTable;

class StateMachineTransitionLogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineTransitionLogsTable
     */
    protected $StateMachineTransitionLogs;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineTransitionLogs',
        'plugin.StateMachine.StateMachineProcesses',
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
    public function testInstance(): void
    {
        $this->assertInstanceOf(StateMachineTransitionLogsTable::class, $this->StateMachineTransitionLogs);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $result = $this->StateMachineTransitionLogs->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineTransitionLog::class, $result);
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'state_machine_process_id' => 1,
            'identifier' => 2,
            'locked' => false,
            'is_error' => false,
        ];
        $item = $this->StateMachineTransitionLogs->newEntity($data);
        $item->state_machine_item_id = 1;
        $this->StateMachineTransitionLogs->saveOrFail($item);

        $this->assertNotEmpty($item->id);
    }
}
