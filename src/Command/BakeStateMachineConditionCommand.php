<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Command;

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Command;

use Bake\Command\SimpleBakeCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;

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
    public $pathFragment = 'StateMachine/Condition/';

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
