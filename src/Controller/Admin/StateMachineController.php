<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use StateMachine\Controller\CastTrait;
use StateMachine\FactoryTrait;

/**
 * @property \Cake\ORM\Table $StateMachine
 */
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
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return \Cake\Http\Response|null
     */
    public function process()
    {
        $stateMachineName = $this->castString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE)) ?: null;
        if (!$stateMachineName) {
            throw new NotFoundException('State Machine is required as param.');
        }

        $processes = $this->getFactory()
            ->createStateMachineFinder()
            ->getProcesses($stateMachineName);

        $this->set(compact('stateMachineName', 'processes'));
    }

    /**
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return \Cake\Http\Response|null
     */
    public function overview()
    {
        $stateMachineName = $this->castString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE)) ?: null;
        if (!$stateMachineName) {
            throw new NotFoundException('State Machine is required as param.');
        }

        $matrix = $this->getFactory()
            ->createStateMachineFinder()
            ->getItemMatrix($stateMachineName);
        dd($matrix);
    }
}
