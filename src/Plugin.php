<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\Plugin as CakePlugin;

/**
 * Plugin for StateMachine
 */
class Plugin extends BasePlugin
{
    /**
     * @var bool
     */
    protected $middlewareEnabled = false;

    /**
     * @var bool
     */
    protected $bootstrapEnabled = false;

    /**
     * @inheritDoc
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        $commandCollection = $commands->discoverPlugin($this->getName());
        if (!CakePlugin::isLoaded('Bake')) {
            foreach ($commandCollection as $key => $value) {
                if (strpos($value, '\\Bake') !== false) {
                    unset($commandCollection[$key]);
                }
            }
        }

        return $commands->addMany($commandCollection);
    }
}
