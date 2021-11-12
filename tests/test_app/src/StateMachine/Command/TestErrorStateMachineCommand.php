<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace TestApp\StateMachine\Command;

use InvalidArgumentException;
use StateMachine\Dependency\StateMachineCommandInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TestErrorStateMachineCommand implements StateMachineCommandInterface
{
    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function run(ItemDto $itemDto): void
    {
        throw new InvalidArgumentException('Test exception for identity: ' . $itemDto->getIdentifierOrFail());
    }
}
