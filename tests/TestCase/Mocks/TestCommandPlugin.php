<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Mocks;

use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

class TestCommandPlugin implements CommandPluginInterface
{
    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        return true;
    }
}
