<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Dto\StateMachine\ProcessDto;

interface BuilderInterface
{
    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function createProcess(ProcessDto $processDto): ProcessInterface;
}
