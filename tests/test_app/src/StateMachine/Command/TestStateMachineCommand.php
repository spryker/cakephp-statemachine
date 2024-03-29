<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace TestApp\StateMachine\Command;

use StateMachine\Dependency\StateMachineCommandInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class TestStateMachineCommand implements StateMachineCommandInterface
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
