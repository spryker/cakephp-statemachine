<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

interface HandlerResolverInterface
{
    /**
     * @param string $stateMachineName
     *
     * @throws \StateMachine\Business\Exception\StateMachineHandlerNotFound
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface
     */
    public function get($stateMachineName);

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface|null
     */
    public function find($stateMachineName);
}
