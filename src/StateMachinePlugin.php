<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;
use Queue\Command\AddCommand;
use Queue\Command\InfoCommand;
use Queue\Command\JobCommand;
use Queue\Command\RunCommand;
use Queue\Command\WorkerCommand;
use StateMachine\Command\CheckConditionsStateMachineCommand;
use StateMachine\Command\CheckTimeoutsStatemachineCommand;
use StateMachine\Command\ClearLocksStatemachineCommand;
use StateMachine\Command\InitStatemachineCommand;

/**
 * Plugin for StateMachine
 */
class StateMachinePlugin extends BasePlugin
{
    /**
     * @var string|null
     */
    protected ?string $name = 'StateMachine';

    /**
     * @var bool
     */
    protected bool $middlewareEnabled = false;

    /**
     * @var bool
     */
    protected bool $bootstrapEnabled = false;

    /**
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     *
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->plugin('StateMachine', ['path' => '/state-machine'], function (RouteBuilder $routes): void {
            $routes->connect('/', ['controller' => 'StateMachine', 'action' => 'index']);

            $routes->fallbacks();
        });

        $routes->prefix('Admin', function (RouteBuilder $routes): void {
            $routes->plugin('StateMachine', ['path' => '/state-machine'], function (RouteBuilder $routes): void {
                $routes->connect('/', ['controller' => 'StateMachine', 'action' => 'index']);

                $routes->fallbacks();
            });
        });
    }

    /**
     * @inheritDoc
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        if (class_exists('Bake\Command\SimpleBakeCommand')) {
            $commandList = $commands->discoverPlugin($this->getName());

            return $commands->addMany($commandList);
        }

        $commands->add('state_machine check_conditions', CheckConditionsStateMachineCommand::class);
        $commands->add('state_machine check_timeouts', CheckTimeoutsStatemachineCommand::class);
        $commands->add('state_machine clear-locks', ClearLocksStatemachineCommand::class);
        $commands->add('state_machine init', InitStatemachineCommand::class);

        return $commands;
    }
}
