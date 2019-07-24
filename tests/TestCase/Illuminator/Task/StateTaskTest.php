<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use StateMachine\Illuminator\Task\StateTask;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class StateTaskTest extends TestCase
{
    /**
     * @var \Tools\TestSuite\ConsoleOutput
     */
    protected $out;

    /**
     * @var \Tools\TestSuite\ConsoleOutput
     */
    protected $err;

    /**
     * @var \IdeHelper\Console\Io
     */
    protected $io;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->out = new ConsoleOutput();
        $this->err = new ConsoleOutput();
        $consoleIo = new ConsoleIo($this->out, $this->err);
        $this->io = new Io($consoleIo);
    }

    /**
     * @return void
     */
    public function testShouldRun(): void
    {
        $task = $this->_getTask();

        $result = $task->shouldRun('/src/StateMachine/MyStateMachineHandler.php');
        $this->assertTrue($result);

        $result = $task->shouldRun('/src/StateMachine/My/Own/AwesomeStateMachineHandler.php');
        $this->assertTrue($result);

        $result = $task->shouldRun('/src/StateMachine/StateMachineHandler.php');
        $this->assertTrue($result);

        $result = $task->shouldRun('/src/StateMachine/StateMachineHandlerFoo.php');
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testIlluminate(): void
    {
        Configure::write('StateMachine.pathToXml', TESTS . 'test_files' . DS);

        $task = $this->_getTask();

        $path = TESTS . 'test_app' . DS . 'src' . DS . 'StateMachine' . DS . 'DemoStateMachineHandler.php';
        $result = $task->run(file_get_contents($path), $path);

        $this->assertTextContains('const STATE_NEW = \'new\';', $result);
        $this->assertTextContains('const STATE_INVOICE_CREATED = \'invoice created\';', $result);
    }

    /**
     * @param array $params
     *
     * @return \StateMachine\Illuminator\Task\StateTask
     */
    protected function _getTask(array $params = []): StateTask
    {
        $params += [
            AbstractAnnotator::CONFIG_DRY_RUN => true,
            AbstractAnnotator::CONFIG_VERBOSE => true,
        ];

        return new StateTask($params);
    }
}
