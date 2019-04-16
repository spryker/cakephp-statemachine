<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Dependency;

use StateMachine\Transfer\StateMachineItemTransfer;

interface CommandPluginInterface
{
    /**
     * Specification:
     * - This method is called when event have concrete command assigned.
     *
     * @api
     *
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer): bool;
}
