<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use StateMachine\Controller\CastTrait;
use StateMachine\FactoryTrait;
use Tools\Model\Table\Table;

/**
 * @property \Cake\ORM\Table $StateMachine
 */
class StateMachineController extends AppController
{
    use FactoryTrait;
    use CastTrait;

    /**
     * @var string
     */
    public const URL_PARAM_STATE_MACHINE = 'state-machine';

    /**
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $stateMachines = [];
        foreach ($this->getFactory()->getStateMachineHandlers() as $stateMachineHandler) {
            $stateMachines[] = $stateMachineHandler->getStateMachineName();
        }

        $itemsPerStateMachine = [];
        if ($stateMachines) {
            $query = $this->getFactory()->getTableLocator()->get('StateMachine.StateMachineItems')
                ->find();
            $itemsPerStateMachine = $query
                ->select(['state_machine', 'count' => $query->func()->count('*')])
                ->group('state_machine')
                ->where(['state_machine IN' => $stateMachines])
                ->find('list', ['keyField' => 'state_machine', 'valueField' => 'count'])
                ->toArray();
        }

        $this->set(compact('stateMachines', 'itemsPerStateMachine'));

        return null;
    }

    /**
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return \Cake\Http\Response|null
     */
    public function process(): ?Response
    {
        $stateMachineName = $this->castString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE)) ?: null;
        if (!$stateMachineName) {
            throw new NotFoundException('State Machine is required as param.');
        }

        $processes = $this->getFactory()
            ->createStateMachineFinder()
            ->getProcesses($stateMachineName);

        $this->set(compact('stateMachineName', 'processes'));

        return null;
    }

    /**
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return \Cake\Http\Response|null
     */
    public function overview(): ?Response
    {
        $stateMachineName = $this->castString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE)) ?: null;
        if (!$stateMachineName) {
            throw new NotFoundException('State Machine is required as param.');
        }

        $matrix = $this->getFactory()
            ->createStateMachineFinder()
            ->getItemMatrix($stateMachineName);

        $this->set(compact('stateMachineName', 'matrix'));

        return null;
    }

    /**
     * Trigger event for new identifier.
     *
     * @return \Cake\Http\Response|null
     */
    public function reset(): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);

        $this->getTable('StateMachine.StateMachineLocks')->truncate();
        $this->getTable('StateMachine.StateMachineTimeouts')->truncate();
        $this->getTable('StateMachine.StateMachineItemStateLogs')->truncate();

        $this->getTable('StateMachine.StateMachineItems')->deleteAll('1=1');
        $this->getTable('StateMachine.StateMachineItemStates')->deleteAll('1=1');
        $this->getTable('StateMachine.StateMachineProcesses')->deleteAll('1=1');
        $this->getTable('StateMachine.StateMachineTransitionLogs')->deleteAll('1=1');

        return $this->redirect($this->referer(['action' => 'index'], true));
    }

    /**
     * @param string $table
     *
     * @return \Tools\Model\Table\Table
     */
    protected function getTable(string $table): Table
    {
        return $this->getFactory()->getTableLocator()->get($table);
    }
}
