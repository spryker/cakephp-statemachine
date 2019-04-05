<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Mocks;

use Exception;
use StateMachine\Transfer\StateMachineItemTransfer;

class TestCommandExceptionPlugin extends TestCommandPlugin
{
    /**
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $this->throwTestException();

        return true;
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    protected function throwTestException()
    {
        throw new Exception('Sry, something went wrong');
    }
}
