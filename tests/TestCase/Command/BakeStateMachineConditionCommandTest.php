<?php
declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Shim\TestSuite\TestTrait;

/**
 * @uses \StateMachine\Command\BakeStateMachineConditionCommand
 */
class BakeStateMachineConditionCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use TestTrait;

    /**
     * @var string
     */
    protected $filePath = APP . 'StateMachine' . DS . 'Condition' . DS;

    /**
     * @var string
     */
    protected $testFilePath = ROOT . DS . 'tests' . DS . 'TestCase' . DS . 'StateMachine' . DS . 'Condition' . DS;

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
        $this->exec('bake state_machine_condition FooBarBaz -f');

        $output = $this->_out->output();
        $this->assertStringContainsString('Creating file', $output);
        $this->assertStringContainsString('<success>Wrote</success>', $output);

        $file = $this->filePath . 'FooBarBazCondition.php';
        $expected = TESTS . 'test_files' . DS . 'bake' . DS . 'condition.php';
        $this->assertFileEquals($expected, $file);

        $file = $this->testFilePath . 'FooBarBazConditionTest.php';
        $expected = TESTS . 'test_files' . DS . 'bake' . DS . 'condition_test.php';
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

        $file = $this->filePath . 'FooBarBazCondition.php';
        if (file_exists($file)) {
            unlink($file);
        }

        $testFile = $this->testFilePath . 'FooBarBazConditionTest.php';
        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }
}
