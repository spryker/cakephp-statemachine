<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * @property \StateMachine\Model\Table\StateMachineTimeoutsTable $StateMachineTimeouts
 *
 * @method \StateMachine\Model\Entity\StateMachineTimeout[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineTimeoutsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['StateMachineItemStates', 'StateMachineProcesses'],
        ];
        $stateMachineTimeouts = $this->paginate();

        $this->set(compact('stateMachineTimeouts'));
        $this->set('_serialize', ['stateMachineTimeouts']);
    }

    /**
     * View method
     *
     * @param string|int|null $id State Machine Timeout id.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $stateMachineTimeout = $this->StateMachineTimeouts->get($id, [
            'contain' => ['StateMachineItemStates', 'StateMachineProcesses'],
        ]);

        $this->set(compact('stateMachineTimeout'));
        $this->set('_serialize', ['stateMachineTimeout']);
    }

    /**
     * Delete method
     *
     * @param string|int|null $id State Machine Timeout id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineTimeout = $this->StateMachineTimeouts->get($id);
        if ($this->StateMachineTimeouts->delete($stateMachineTimeout)) {
            $this->Flash->success(__('The state machine timeout has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine timeout could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
