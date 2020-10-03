<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller\Admin;

use Cake\TestSuite\IntegrationTestCase;

/**
 * @uses \StateMachine\Controller\Admin\StateMachineItemsController
 */
class StateMachineItemsControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineTransitionLogs',
        'plugin.StateMachine.StateMachineItems',
        'plugin.StateMachine.StateMachineProcesses',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->disableErrorHandlerMiddleware();

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachineItems', 'action' => 'index']);

        $this->assertResponseCode(200);
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView(): void
    {
        $this->disableErrorHandlerMiddleware();

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachineItems', 'action' => 'view', 1]);

        $this->assertResponseCode(200);
    }
}
