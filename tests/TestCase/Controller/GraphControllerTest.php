<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller;

use App\StateMachine\DemoStateMachineHandler;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use StateMachine\Controller\Admin\GraphController;

class GraphControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineProcesses',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testDraw()
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class
        ]);

        $this->disableErrorHandlerMiddleware();

        $query = [
            GraphController::URL_PARAM_PROCESS => 'TestProcess',
            GraphController::URL_PARAM_STATE_MACHINE => 'TestingSm',
        ];
        $this->get(['plugin' => 'StateMachine', 'prefix' => 'admin', 'controller' => 'Graph', 'action' => 'draw', '?' => $query]);

        $this->assertResponseCode(200);

        $content = (string)$this->_response->getBody();
        $this->assertTextContains('<svg ', $content);
        $this->assertTextContains('</svg>', $content);
    }
}
