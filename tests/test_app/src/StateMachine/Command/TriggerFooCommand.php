<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine\Command;

use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TriggerFooCommand implements CommandPluginInterface
{
    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $stateMachineItemTransfer
     *
     * @return bool
     */
    public function run(ItemDto $stateMachineItemTransfer): bool
    {
        file_put_contents(TMP . 'triggered.txt', date('Y-m-d H:i:s'));

        return true;
    }
}
