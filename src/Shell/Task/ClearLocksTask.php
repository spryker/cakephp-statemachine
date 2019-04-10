<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell\Task;

use Cake\Console\Shell;
use StateMachine\FacadeAwareTrait;

class ClearLocksTask extends Shell
{
    use FacadeAwareTrait;

    /**
     * @return void
     */
    public function main(): void
    {
        $this->getFacade()->clearLocks();
    }
}
