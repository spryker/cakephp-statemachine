<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Controller\Admin;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * @uses \StateMachine\Controller\Admin\StateMachineLocksController
 */
class StateMachineLocksControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.StateMachine.StateMachineLocks',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->disableErrorHandlerMiddleware();

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachineLocks', 'action' => 'index']);

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

        $this->get(['plugin' => 'StateMachine', 'prefix' => 'Admin', 'controller' => 'StateMachineLocks', 'action' => 'view', 1]);

        $this->assertResponseCode(200);
    }
}
