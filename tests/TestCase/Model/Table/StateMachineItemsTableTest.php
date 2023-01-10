<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Table\StateMachineItemsTable;

/**
 * StateMachine\Model\Table\StateMachineItemsTable Test Case
 */
class StateMachineItemsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineItemsTable
     */
    protected $StateMachineItems;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
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
        unset($this->StateMachineItems);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'identifier' => 1,
            'state_machine' => 'Foo',
            'process' => 'P',
            'state' => 'S',
        ];
        $item = $this->StateMachineItems->newEntity($data);
        $this->StateMachineItems->saveOrFail($item);

        $this->assertNotEmpty($item->id);
    }
}
