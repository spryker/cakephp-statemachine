<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineTimeout;
use StateMachine\Model\Table\StateMachineTimeoutsTable;

class StateMachineTimeoutsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineTimeoutsTable
     */
    protected $StateMachineTimeouts;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineItemStates',
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
        unset($this->StateMachineTimeouts);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(StateMachineTimeoutsTable::class, $this->StateMachineTimeouts);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $result = $this->StateMachineTimeouts->find()->first();
        $this->assertTrue((bool)$result);
        $this->assertInstanceOf(StateMachineTimeout::class, $result);
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'state_machine_item_state_id' => 1,
            'state_machine_process_id' => 1,
            'identifier' => 2,
            'event' => 'E',
            'timeout' => new FrozenTime('+1 minute'),
        ];
        $item = $this->StateMachineTimeouts->newEntity($data);
        $this->StateMachineTimeouts->saveOrFail($item);

        $this->assertNotEmpty($item->id);
    }
}
