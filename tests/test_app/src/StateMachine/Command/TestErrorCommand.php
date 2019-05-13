<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\StateMachine\Command;

use InvalidArgumentException;
use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TestErrorCommand implements CommandPluginInterface
{
    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function run(ItemDto $itemDto): bool
    {
        throw new InvalidArgumentException('Test exception for identity: ' . $itemDto->getIdentifierOrFail());
    }
}
