<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

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
    public $StateMachineTimeouts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineProcesses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
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
    public function tearDown()
    {
        unset($this->StateMachineTimeouts);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance()
    {
        $this->assertInstanceOf(StateMachineTimeoutsTable::class, $this->StateMachineTimeouts);
    }

    /**
     * @return void
     */
    public function testFind()
    {
        $result = $this->StateMachineTimeouts->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineTimeout::class, $result);
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
