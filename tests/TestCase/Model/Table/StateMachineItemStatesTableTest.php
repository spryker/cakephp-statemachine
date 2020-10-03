<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineItemState;
use StateMachine\Model\Table\StateMachineItemStatesTable;

class StateMachineItemStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineItemStatesTable
     */
    protected $StateMachineItemStates;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineTimeouts',
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
        $config = TableRegistry::getTableLocator()->exists('StateMachineItemStates') ? [] : ['className' => StateMachineItemStatesTable::class];
        $this->StateMachineItemStates = TableRegistry::getTableLocator()->get('StateMachineItemStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineItemStates);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(StateMachineItemStatesTable::class, $this->StateMachineItemStates);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $result = $this->StateMachineItemStates->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineItemState::class, $result);
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'state_machine_process_id' => 1,
            'name' => 'E',
        ];
        $item = $this->StateMachineItemStates->newEntity($data);
        $this->StateMachineItemStates->saveOrFail($item);

        $this->assertNotEmpty($item->id);
    }
}
