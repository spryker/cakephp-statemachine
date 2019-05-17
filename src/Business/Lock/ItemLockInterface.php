<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Lock;

interface ItemLockInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function acquire(string $identifier): bool;

    /**
     * @param string $identifier
     *
     * @return void
     */
    public function release(string $identifier): void;

    /**
     * @return void
     */
    public function clearLocks(): void;
}
