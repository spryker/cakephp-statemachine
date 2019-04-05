<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Mocks;

use StateMachine\StateMachineConfig as SprykerStateMachineConfig;

class StateMachineConfig extends SprykerStateMachineConfig
{
    /**
     * @return string
     */
    public function getPathToStateMachineXmlFiles()
    {
        return realpath(__DIR__ . '/../test_files');
    }
}
