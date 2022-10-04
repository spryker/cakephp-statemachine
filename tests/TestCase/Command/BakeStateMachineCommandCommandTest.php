<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace StateMachine\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Shim\TestSuite\TestTrait;

/**
 * @uses \StateMachine\Command\BakeStateMachineCommandCommand
 */
class BakeStateMachineCommandCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use TestTrait;

    /**
     * @var string
     */
    protected $filePath = APP . 'StateMachine' . DS . 'Command' . DS;

    /**
     * @var string
     */
    protected $testFilePath = ROOT . DS . 'tests' . DS . 'TestCase' . DS . 'StateMachine' . DS . 'Command' . DS;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();

        $this->removeFiles();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->removeFiles();
    }

    /**
     * Test execute method
     *
     * @return void
     */
    public function testExecute(): void
    {
        $this->exec('bake state_machine_command FooBarBaz -f');

        $output = $this->_out->output();
        $this->assertStringContainsString('Creating file', $output);
        $this->assertStringContainsString('<success>Wrote</success>', $output);

        $file = $this->filePath . 'FooBarBazCommand.php';
        $expected = TESTS . 'test_files' . DS . 'bake' . DS . 'command.php';
        $this->assertFileEquals($expected, $file);

        $file = $this->testFilePath . 'FooBarBazCommandTest.php';
        $expected = TESTS . 'test_files' . DS . 'bake' . DS . 'command_test.php';
        $this->assertFileEquals($expected, $file);
    }

    /**
     * @return void
     */
    protected function removeFiles(): void
    {
        if ($this->isDebug()) {
            return;
        }

        $file = $this->filePath . 'FooBarBazCommand.php';
        if (file_exists($file)) {
            unlink($file);
        }

        $testFile = $this->testFilePath . 'FooBarBazCommandTest.php';
        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }
}
