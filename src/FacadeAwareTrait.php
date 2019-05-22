<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Core\Configure;
use StateMachine\Business\StateMachineFacade;
use StateMachine\Business\StateMachineFacadeInterface;

trait FacadeAwareTrait
{
    /**
     * @return \StateMachine\Business\StateMachineFacadeInterface
     */
    protected function getFacade(): StateMachineFacadeInterface
    {
        $class = Configure::read('StateMachine.facade') ?: StateMachineFacade::class;

        return new $class();
    }
}
