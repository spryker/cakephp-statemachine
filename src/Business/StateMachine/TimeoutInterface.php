<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

interface TimeoutInterface
{
    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function setNewTimeout(ProcessInterface $process, StateMachineItemTransfer $stateMachineItemTransfer);

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string $stateName
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function dropOldTimeout(
        ProcessInterface $process,
        $stateName,
        StateMachineItemTransfer $stateMachineItemTransfer
    );
}
