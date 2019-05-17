<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

class StringGenerator implements StringGeneratorInterface
{
    /**
     * @param int $length
     *
     * @return string
     */
    public function generateRandomString(int $length = 32): string
    {
        $tokenLength = $length / 2;
        $token = bin2hex(random_bytes($tokenLength));

        if (strlen($token) !== $length) {
            $token = str_pad($token, $length, '0');
        }

        return $token;
    }
}
