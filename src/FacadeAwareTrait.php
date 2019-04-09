<?php

namespace StateMachine;

use StateMachine\Business\StateMachineFacade;
use StateMachine\Business\StateMachineFacadeInterface;

trait FacadeAwareTrait
{
    /**
     * @return \StateMachine\Business\StateMachineFacadeInterface
     */
    protected function getFacade(): StateMachineFacadeInterface
    {
        return new StateMachineFacade();
    }
}
