<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace App\StateMachine\Condition;

use InvalidArgumentException;
use StateMachine\Dependency\StateMachineConditionInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TestErrorStateMachineCondition implements StateMachineConditionInterface
{
    /**
     * Specification:
     * - This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function check(ItemDto $itemDto): bool
    {
        throw new InvalidArgumentException('Test exception for identity: ' . $itemDto->getIdentifierOrFail());
    }
}
