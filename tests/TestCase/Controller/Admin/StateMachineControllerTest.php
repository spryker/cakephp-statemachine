<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller\Admin;

use App\StateMachine\DemoStateMachineHandler;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @uses \StateMachine\Controller\Admin\StateMachineController
 */
class StateMachineControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineLocks',
        'plugin.StateMachine.StateMachineTransitionLogs',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->disableErrorHandlerMiddleware();

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachine', 'action' => 'index']);

        $this->assertResponseCode(200);
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $this->disableErrorHandlerMiddleware();

        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachine', 'action' => 'process', '?' => ['state-machine' => 'TestingSm']]);

        $this->assertResponseCode(200);
    }

    /**
     * @return void
     */
    public function testOverview(): void
    {
        $this->disableErrorHandlerMiddleware();

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachine', 'action' => 'overview', '?' => ['state-machine' => 'TestingSm']]);

        $this->assertResponseCode(200);
    }

    /**
     * @return void
     */
    public function testReset(): void
    {
        $this->disableErrorHandlerMiddleware();

        $stateMachineItemsTable = TableRegistry::get('StateMachine.StateMachineItems');
        $countBefore = $stateMachineItemsTable->find()->count();
        $this->assertSame(1, $countBefore);

        $this->post(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachine', 'action' => 'reset']);

        $this->assertResponseCode(302);

        $countAfter = $stateMachineItemsTable->find()->count();
        $this->assertSame(0, $countAfter);
    }
}
