<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Command;

use Bake\Command\SimpleBakeCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Core\Plugin;

/**
 * Command class for generating Condition class.
 */
class BakeStateMachineConditionCommand extends SimpleBakeCommand
{
    /**
     * Task name used in path generation.
     *
     * @var string
     */
    public string $pathFragment = 'StateMachine/Condition/';

    /**
     * @var string
     */
    protected $_name;

    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'bake state_machine_condition';
    }

    /**
     * @inheritDoc
     */
    public function bake(string $name, Arguments $args, ConsoleIo $io): void
    {
        $this->_name = $name;

        parent::bake($name, $args, $io);
    }

    /**
     * Generate a test case.
     *
     * @param string $name The class to bake a test for.
     * @param \Cake\Console\Arguments $args The console arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     *
     * @return void
     */
    public function bakeTest(string $name, Arguments $args, ConsoleIo $io): void
    {
        if ($args->getOption('no-test')) {
            return;
        }

        $className = $name . 'Condition';
        $io->out('Generating: ' . $className . ' test class');

        $plugin = (string)$args->getOption('plugin');
        $namespace = $plugin ? str_replace('/', DS, $plugin) : Configure::read('App.namespace');

        $content = $this->generateTaskTestContent($className, $namespace);
        $path = $plugin ? Plugin::path($plugin) : ROOT . DS;
        $path .= 'tests/TestCase/StateMachine/Condition/' . $className . 'Test.php';

        $io->createFile($path, $content, (bool)$args->getOption('force'));
    }

    /**
     * @param string $name
     * @param string $namespace
     *
     * @return string
     */
    protected function generateTaskTestContent(string $name, string $namespace): string
    {
        $taskClassNamespace = '\\StateMachine\\Condition';

        $namespacePart = null;
        if (strpos($name, '/') !== false) {
            $parts = explode('/', $name);
            $name = array_pop($parts);
            $namespacePart = implode('\\', $parts);
        }
        if ($namespacePart) {
            $taskClassNamespace .= '\\' . $namespacePart;
        }

        $taskClass = $namespace . $taskClassNamespace . '\\' . $name;
        $testName = $name . 'Test';

        $content = <<<TXT
<?php

namespace $namespace\Test\TestCase$taskClassNamespace;

use Cake\TestSuite\TestCase;
use $taskClass;

class $testName extends TestCase
{
    /**
     * @var array<string>
     */
    protected \$fixtures = [
    ];

    /**
     * @return void
     */
    public function testRun(): void
    {
        \$condition = new $name();

        //\$itemDto = new ItemDto();
        //TODO

        //\$result = \$condition->check(\$itemDto);
        //\$this->assertTrue(\$result);
    }
}

TXT;

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function template(): string
    {
        return 'StateMachine.Condition/condition';
    }

    /**
     * @inheritDoc
     */
    public function templateData(Arguments $arguments): array
    {
        $name = $this->_name;
        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
            $pluginPath = $this->plugin . '.';
        }

        $namespace .= '\\StateMachine\\Condition';

        $namespacePart = null;
        if (strpos($name, '/') !== false) {
            $parts = explode('/', $name);
            $name = array_pop($parts);
            $namespacePart = implode('\\', $parts);
        }
        if ($namespacePart) {
            $namespace .= '\\' . $namespacePart;
        }

        return [
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'namespace' => $namespace,
            'name' => $name,
        ];
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'state_machine_condition';
    }

    /**
     * @inheritDoc
     */
    public function fileName(string $name): string
    {
        return $name . 'Condition.php';
    }
}
