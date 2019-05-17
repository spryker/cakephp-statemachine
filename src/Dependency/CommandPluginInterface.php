<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Dependency;

use StateMachine\Dto\StateMachine\ItemDto;

interface CommandPluginInterface
{
    /**
     * Specification:
     * - This method is called when event have concrete command assigned.
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function run(ItemDto $itemDto): bool;
}
