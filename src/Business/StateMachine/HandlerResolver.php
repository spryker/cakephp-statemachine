<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Exception\StateMachineHandlerNotFound;

class HandlerResolver implements HandlerResolverInterface
{
    /**
     * @var \StateMachine\Dependency\StateMachineHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * @param \StateMachine\Dependency\StateMachineHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @param string $stateMachineName
     *
     * @throws \StateMachine\Business\Exception\StateMachineHandlerNotFound
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface
     */
    public function get($stateMachineName)
    {
        $stateMachineHandler = $this->find($stateMachineName);
        if ($stateMachineHandler !== null) {
            return $stateMachineHandler;
        }

        throw new StateMachineHandlerNotFound(
            sprintf(
                'State machine handler with name "%s", not found',
                $stateMachineName
            )
        );
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface|null
     */
    public function find($stateMachineName)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->getStateMachineName() === $stateMachineName) {
                return $handler;
            }
        }

        return null;
    }
}
