<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

interface StringGeneratorInterface
{
    /**
     * @param int $length
     *
     * @return string
     */
    public function generateRandomString(int $length = 32): string;
}
