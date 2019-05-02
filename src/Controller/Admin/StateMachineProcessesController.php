<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * @property \StateMachine\Model\Table\StateMachineProcessesTable $StateMachineProcesses
 *
 * @method \StateMachine\Model\Entity\StateMachineProcess[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineProcessesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $stateMachineProcesses = $this->paginate();

        $this->set(compact('stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineProcesses']);
    }

    /**
     * View method
     *
     * @param int|null $id State Machine Process id.
     *
     * @return \Cake\Http\Response|null
     */
    public function view(?int $id = null)
    {
        $stateMachineProcess = $this->StateMachineProcesses->get($id, [
            'contain' => ['StateMachineItemStates', 'StateMachineTimeouts'],
        ]);

        $this->set(compact('stateMachineProcess'));
        $this->set('_serialize', ['stateMachineProcess']);
    }

    /**
     * Delete method
     *
     * @param int|null $id State Machine Process id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete(?int $id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineProcess = $this->StateMachineProcesses->get($id);
        if ($this->StateMachineProcesses->delete($stateMachineProcess)) {
            $this->Flash->success(__('The state machine process has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine process could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
