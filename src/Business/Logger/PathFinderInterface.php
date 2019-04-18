<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Logger;

interface PathFinderInterface
{
    /**
     * @return string
     */
    public function getCurrentExecutionPath(): string;
}
