<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine\Condition;

use StateMachine\Dependency\ConditionPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TestFalseCondition implements ConditionPluginInterface
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
        return false;
    }
}
