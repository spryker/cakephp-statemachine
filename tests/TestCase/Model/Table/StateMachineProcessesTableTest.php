<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineProcess;
use StateMachine\Model\Table\StateMachineProcessesTable;

class StateMachineProcessesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineProcessesTable
     */
    protected $StateMachineProcesses;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTimeouts',
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
        $config = TableRegistry::getTableLocator()->exists('StateMachineProcesses') ? [] : ['className' => StateMachineProcessesTable::class];
        $this->StateMachineProcesses = TableRegistry::getTableLocator()->get('StateMachineProcesses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineProcesses);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(StateMachineProcessesTable::class, $this->StateMachineProcesses);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $result = $this->StateMachineProcesses->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineProcess::class, $result);
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'name' => 'E',
            'state_machine' => 'SM',
        ];
        $item = $this->StateMachineProcesses->newEntity($data);
        $this->StateMachineProcesses->saveOrFail($item);

        $this->assertNotEmpty($item->id);
    }
}
