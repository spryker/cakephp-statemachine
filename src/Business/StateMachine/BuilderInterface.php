<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Transfer\StateMachineProcessTransfer;

interface BuilderInterface
{
    /**
     * @param \StateMachine\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function createProcess(StateMachineProcessTransfer $stateMachineProcessTransfer);
}
