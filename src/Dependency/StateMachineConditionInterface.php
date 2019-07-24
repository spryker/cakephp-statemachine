<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Dependency;

use StateMachine\Dto\StateMachine\ItemDto;

interface StateMachineConditionInterface
{
    /**
     * Specification:
     * - This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function check(ItemDto $itemDto): bool;
}
