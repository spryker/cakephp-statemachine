<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Table\StateMachineLocksTable;

/**
 * StateMachine\Model\Table\StateMachineLocksTable Test Case
 */
class StateMachineLocksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineLocksTable
     */
    protected $StateMachineLocks;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.StateMachine.StateMachineLocks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StateMachineLocks') ? [] : ['className' => StateMachineLocksTable::class];
        $this->StateMachineLocks = TableRegistry::getTableLocator()->get('StateMachineLocks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->StateMachineLocks);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $data = [
            'identifier' => 1,
            'expires' => new FrozenTime('+1 minute'),
        ];
        $item = $this->StateMachineLocks->newEntity($data);
        $this->StateMachineLocks->saveOrFail($item);

        $this->assertNotEmpty($item->id);
    }
}
