<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineItemStateHistory;
use StateMachine\Model\Table\StateMachineItemStateHistoryTable;

class StateMachineItemStateHistoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \StateMachine\Model\Table\StateMachineItemStateHistoryTable
     */
    public $StateMachineItemStateHistory;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineItemStates',
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
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
