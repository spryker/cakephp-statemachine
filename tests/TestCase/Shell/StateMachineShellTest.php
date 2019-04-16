<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Shell;

use App\StateMachine\DemoStateMachineHandler;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use StateMachine\Shell\StateMachineShell;
use Tools\TestSuite\ConsoleOutput;

class StateMachineShellTest extends TestCase
{
    /**
     * @var \StateMachine\Shell\StateMachineShell|\PHPUnit\Framework\MockObject\MockObject
     */
    public $Shell;

    /**
     * @var array
     */
    public $fixtures = [
        'plugin.StateMachine.StateMachineLocks',
        'plugin.StateMachine.StateMachineItemStates',
        'plugin.StateMachine.StateMachineItemStateHistory',
        'plugin.StateMachine.StateMachineProcesses',
        'plugin.StateMachine.StateMachineTimeouts',
    ];

    /**
     * @var \Tools\TestSuite\ConsoleOutput
     */
    protected $out;

    /**
     * @var \Tools\TestSuite\ConsoleOutput
     */
    protected $err;

    /**
     * @return void
     */
    public function setUp()
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
    public function testClearLocks()
    {
        $this->Shell->runCommand(['clearLocks']);

        $this->assertEmpty($this->err->output());
    }

    /**
     * @return void
     */
    public function testCheckConditions()
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->Shell->runCommand(['checkConditions', 'TestingSm']);
        $output = $this->out->output();

        $this->assertEmpty($this->err->output());
    }

    /**
     * @return void
     */
    public function testCheckTimeouts()
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);
        Configure::write('StateMachine.handlers', [
            DemoStateMachineHandler::class,
        ]);

        $this->Shell->runCommand(['checkTimeouts', 'TestingSm']);
        $output = $this->out->output();

        $this->assertEmpty($this->err->output());
    }
}
