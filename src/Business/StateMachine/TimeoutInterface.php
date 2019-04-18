<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Dto\StateMachine\ItemDto;

interface TimeoutInterface
{
    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    public function setNewTimeout(ProcessInterface $process, ItemDto $itemDto);

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string $stateName
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return void
     */
    public function dropOldTimeout(
        ProcessInterface $process,
        $stateName,
        ItemDto $itemDto
    );
}
