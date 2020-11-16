<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Shell;

use App\StateMachine\DemoStateMachineHandler;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Shim\TestSuite\ConsoleOutput;
use StateMachine\Shell\StateMachineShell;

class StateMachineShellTest extends TestCase
{
    /**
     * @var \StateMachine\Shell\StateMachineShell|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $Shell;

    /**
     * @var array
     */
    protected $fixtures = [
        'plugin.StateMachine.StateMachineLocks',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineItemStateLogs',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineTimeouts',
        'plugin.StateMachine.StateMachineItems',
    ];

    /**
     * @var \Shim\TestSuite\ConsoleOutput
     */
    protected $out;

    /**
     * @var \Shim\TestSuite\ConsoleOutput
     */
    protected $err;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->out = new ConsoleOutput();
        $this->err = new ConsoleOutput();
        $io = new ConsoleIo($this->out, $this->err);

        $this->Shell = $this->getMockBuilder(StateMachineShell::class)
         ->setMethods(['in', '_stop'])
         ->setConstructorArgs([$io])
         ->getMock();
    }

    /**
     * @return void
     */
    public function testClearLocks(): void
    {
        $this->Shell->runCommand(['clearLocks']);

        $this->assertEmpty($this->err->output());
    }

    /**
     * @return void
     */
    public function testCheckConditions(): void
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->Shell->runCommand(['checkConditions', 'TestingSm']);

        $this->assertEmpty($this->err->output());
    }

    /**
     * @return void
     */
    public function testCheckTimeouts(): void
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->Shell->runCommand(['checkTimeouts', 'TestingSm']);

        $this->assertEmpty($this->err->output());
    }
}
