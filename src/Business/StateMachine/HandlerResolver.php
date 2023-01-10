<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use StateMachine\Business\Exception\StateMachineHandlerNotFound;
use StateMachine\Dependency\StateMachineHandlerInterface;

class HandlerResolver implements HandlerResolverInterface
{
    /**
     * @var array<\StateMachine\Dependency\StateMachineHandlerInterface>
     */
    protected array $handlers = [];

    /**
     * @param array<\StateMachine\Dependency\StateMachineHandlerInterface> $handlers
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
    public function get(string $stateMachineName): StateMachineHandlerInterface
    {
        $stateMachineHandler = $this->find($stateMachineName);
        if ($stateMachineHandler !== null) {
            return $stateMachineHandler;
        }

        throw new StateMachineHandlerNotFound(
            sprintf(
                'State machine handler with name `%s` not found!',
                $stateMachineName,
            ),
        );
    }

    /**
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dependency\StateMachineHandlerInterface|null
     */
    public function find(string $stateMachineName): ?StateMachineHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->getStateMachineName() === $stateMachineName) {
                return $handler;
            }
        }

        return null;
    }
}
