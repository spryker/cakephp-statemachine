<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use StateMachine\Shell\StateMachineShell;

/**
 * Plugin for StateMachine
 */
class Plugin extends BasePlugin
{
    /**
     * Plugin name.
     *
     * @var string
     */
    protected $name = 'StateMachine';

    /**
     * @var bool
     */
    protected $middlewareEnabled = false;

    /**
     * @var bool
     */
    protected $bootstrapEnabled = false;

    /**
     * @var string[]
     */
    protected $stateMachineCommandsList = [
        StateMachineShell::class,
    ];

    /**
     * @inheritDoc
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        if (class_exists('Bake\Command\SimpleBakeCommand')) {
            $commandList = $commands->discoverPlugin($this->getName());

            return $commands->addMany($commandList);
        }

        $commandList = [];
        foreach ($this->stateMachineCommandsList as $class) {
            $name = $class::defaultName();
            // If the short name has been used, use the full name.
            // This allows app commands to have name preference.
            // and app commands to overwrite migration commands.
            if (!$commands->has($name)) {
                $commandList[$name] = $class;
            }
            // full name
            $commandList['state_machine.' . $name] = $class;
        }

        return $commands->addMany($commandList);
    }
}
