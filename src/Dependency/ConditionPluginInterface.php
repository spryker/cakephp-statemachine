<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Dependency;

use StateMachine\Transfer\StateMachineItemTransfer;

interface ConditionPluginInterface
{
    /**
     * This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @api
     *
     * @param \StateMachine\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function check(StateMachineItemTransfer $stateMachineItemTransfer);
}
