<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
    public $StateMachineTransitionLogs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineTransitionLogs',
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
        $config = TableRegistry::getTableLocator()->exists('StateMachineTransitionLogs') ? [] : ['className' => StateMachineTransitionLogsTable::class];
        $this->StateMachineTransitionLogs = TableRegistry::getTableLocator()->get('StateMachineTransitionLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->StateMachineTransitionLogs);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testInstance()
    {
        $this->assertInstanceOf(StateMachineTransitionLogsTable::class, $this->StateMachineTransitionLogs);
    }

    /**
     * @return void
     */
    public function testFind()
    {
        $result = $this->StateMachineTransitionLogs->find()->first();
        $this->assertTrue(!empty($result));
        $this->assertInstanceOf(StateMachineTransitionLog::class, $result);
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
