<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace App\StateMachine\Command;

use StateMachine\Dependency\CommandPluginInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TestCommand implements CommandPluginInterface
{
    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    public function run(ItemDto $itemDto): void
    {
    }
}
