<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace App\StateMachine\Condition;

use StateMachine\Dependency\StateMachineConditionInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class IsFooTriggeredStateMachineCondition implements StateMachineConditionInterface
{
    /**
     * Specification:
     * - This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function check(ItemDto $itemDto): bool
    {
        if (!file_exists(TMP . 'triggered.txt')) {
            return false;
        }

        return (bool)file_get_contents(TMP . 'triggered.txt');
    }
}
