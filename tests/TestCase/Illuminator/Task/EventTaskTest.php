<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace IdeHelper\Test\TestCase\Illuminator\Task;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;
use StateMachine\Illuminator\Task\EventTask;

class EventTaskTest extends TestCase
{
    /**
     * @var \Shim\TestSuite\ConsoleOutput
     */
    protected $out;

    /**
     * @var \Shim\TestSuite\ConsoleOutput
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

        $this->assertTextContains('const EVENT_CREATE_INVOICE = \'create invoice\';', $result);
        $this->assertTextContains('const EVENT_EXPORT_ORDER = \'export order\';', $result);
    }

    /**
     * @param array $params
     *
     * @return \StateMachine\Illuminator\Task\EventTask
     */
    protected function _getTask(array $params = []): EventTask
    {
        $params += [
            AbstractAnnotator::CONFIG_DRY_RUN => true,
            AbstractAnnotator::CONFIG_VERBOSE => true,
        ];

        return new EventTask($params);
    }
}
