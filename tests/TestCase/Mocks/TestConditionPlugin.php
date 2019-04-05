<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Mocks;

use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

class TestConditionPlugin implements ConditionPluginInterface
{
    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function check(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        return true;
    }
}
