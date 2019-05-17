<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller;

use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

trait CastTrait
{
    /**
     * @param mixed|null $integer
     *
     * @return int|null
     */
    protected function assertInt($integer): ?int
    {
        if ($integer === null) {
            return $integer;
        }

        return $this->castInt($integer);
    }

    /**
     * @param mixed|null $integer
     *
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return int
     */
    protected function castInt($integer): int
    {
        if (!is_numeric($integer) || $integer === 0) {
            throw new NotFoundException('The given number is not numeric or 0 (zero): ' . Debugger::exportVar($integer));
        }

        return (int)$integer;
    }

    /**
     * @param mixed|null $string
     *
     * @return string|null
     */
    protected function assertString($string): ?string
    {
        if ($string === null) {
            return $string;
        }

        return $this->castString($string);
    }

    /**
     * @param mixed|null $string
     *
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return string
     */
    protected function castString($string): string
    {
        if (!is_scalar($string)) {
            throw new NotFoundException('The given string is not scalar: ' . Debugger::exportVar($string));
        }

        return (string)$string;
    }

    /**
     * @param mixed|null $boolean
     *
     * @return bool|null
     */
    protected function assertBool($boolean): ?bool
    {
        if ($boolean === null) {
            return $boolean;
        }

        return $this->castBool($boolean);
    }

    /**
     * @param mixed|null $boolean
     *
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return bool
     */
    protected function castBool($boolean): bool
    {
        if (!is_scalar($boolean)) {
            throw new NotFoundException('The given string is not scalar: ' . Debugger::exportVar($boolean));
        }

        if ($boolean === 'true') {
            return true;
        }
        if ($boolean === 'false') {
            return false;
        }

        return (bool)$boolean;
    }
}
