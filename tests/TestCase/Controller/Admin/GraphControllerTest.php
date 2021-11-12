<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use StateMachine\Controller\Admin\GraphController;
use TestApp\StateMachine\DemoStateMachineHandler;

/**
 * @uses \StateMachine\Controller\Admin\GraphController
 */
class GraphControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineProcesses',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testDraw(): void
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->disableErrorHandlerMiddleware();

        $query = [
            GraphController::URL_PARAM_PROCESS => 'TestProcess',
            GraphController::URL_PARAM_STATE_MACHINE => 'TestingSm',
        ];
        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'Graph', 'action' => 'draw', '?' => $query]);

        $this->assertResponseCode(200);

        $content = (string)$this->_response->getBody();
        $this->assertTextContains('<svg ', $content);
        $this->assertTextContains('</svg>', $content);
    }
}
