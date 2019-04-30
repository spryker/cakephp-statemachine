<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller\Admin;

use App\StateMachine\DemoStateMachineHandler;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use StateMachine\Controller\Admin\TriggerController;

class TriggerControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineItemStateHistory',
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
        $this->post(['plugin' => 'StateMachine', 'prefix' => 'admin', 'controller' => 'Trigger', 'action' => 'eventForNewItem', '?' => $query]);

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
        $this->post(['plugin' => 'StateMachine', 'prefix' => 'admin', 'controller' => 'Trigger', 'action' => 'event', '?' => $query]);

        $this->assertResponseCode(302);
    }
}
