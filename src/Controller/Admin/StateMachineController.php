<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use StateMachine\Controller\CastTrait;
use StateMachine\FactoryTrait;

class StateMachineController extends AppController
{
    use FactoryTrait;
    use CastTrait;

    public const URL_PARAM_STATE_MACHINE = 'state-machine';

    /**
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $stateMachines = [];
        foreach ($this->getFactory()->getStateMachineHandlers() as $stateMachineHandler) {
            $stateMachines[] = $stateMachineHandler->getStateMachineName();
        }

        $this->set(compact('stateMachines'));
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function process()
    {
        $stateMachineName = $this->castString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE)) ?: null;

        $processes = $this->getFactory()
            ->createStateMachineFinder()
            ->getProcesses($stateMachineName);

        $this->set(compact('stateMachineName', 'processes'));
    }
}
