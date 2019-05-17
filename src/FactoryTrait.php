<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Core\Configure;

trait FactoryTrait
{
    /**
     * @return \StateMachine\PluginFactory
     */
    public function getFactory(): PluginFactory
    {
        $class = Configure::read('StateMachine.factory') ?: PluginFactory::class;

        return new $class();
    }
}
