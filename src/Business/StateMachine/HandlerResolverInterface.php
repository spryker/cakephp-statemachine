<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Dependency\StateMachineHandlerInterface;

interface HandlerResolverInterface
{
    /**
     * @param string $stateMachineName
     *
     * @throws \StateMachine\Business\Exception\StateMachineHandlerNotFound
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface
     */
    public function get(string $stateMachineName): StateMachineHandlerInterface;

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface|null
     */
    public function find(string $stateMachineName): ?StateMachineHandlerInterface;
}
