<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Model;

trait FieldNameTrait
{
    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function getFullFieldName(string $fieldName): string
    {
        return $this->getAlias() . '.' . $fieldName;
    }
}
