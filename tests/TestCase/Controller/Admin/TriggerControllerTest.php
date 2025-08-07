<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use StateMachine\Controller\Admin\TriggerController;
use TestApp\StateMachine\DemoStateMachineHandler;

/**
 * @uses \StateMachine\Controller\Admin\TriggerController
 */
class TriggerControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineLocks',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineTransitionLogs',
        'plugin.StateMachine.StateMachineItems',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testEventForNewItem(): void
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->disableErrorHandlerMiddleware();

        $query = [
            TriggerController::URL_PARAM_PROCESS => 'TestProcess',
            TriggerController::URL_PARAM_STATE_MACHINE => 'TestingSm',
            TriggerController::URL_PARAM_IDENTIFIER => 1,
        ];
        $this->post(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'Trigger', 'action' => 'eventForNewItem', '?' => $query]);

        $this->assertResponseCode(302);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testEvent(): void
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->disableErrorHandlerMiddleware();

        $query = [
            TriggerController::URL_PARAM_PROCESS => 'TestProcess',
            TriggerController::URL_PARAM_STATE_MACHINE => 'TestingSm',
            TriggerController::URL_PARAM_IDENTIFIER => 1,
            TriggerController::URL_PARAM_EVENT => 'Foo',
            TriggerController::URL_PARAM_ID_STATE => 1,
        ];
        $this->post(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'Trigger', 'action' => 'event', '?' => $query]);

        $this->assertResponseCode(302);
    }
}
