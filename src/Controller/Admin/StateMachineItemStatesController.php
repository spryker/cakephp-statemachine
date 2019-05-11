<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * @property \StateMachine\Model\Table\StateMachineItemStatesTable $StateMachineItemStates
 *
 * @method \StateMachine\Model\Entity\StateMachineItemState[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineItemStatesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['StateMachineProcesses'],
        ];
        $stateMachineItemStates = $this->paginate();

        $this->set(compact('stateMachineItemStates'));
        $this->set('_serialize', ['stateMachineItemStates']);
    }

    /**
     * View method
     *
     * @param int|null $id State Machine Item State id.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function view(?int $id = null)
    {
        $stateMachineItemState = $this->StateMachineItemStates->get($id, [
            'contain' => ['StateMachineProcesses', 'StateMachineTimeouts'],
        ]);

        $this->set(compact('stateMachineItemState'));
        $this->set('_serialize', ['stateMachineItemState']);
    }

    /**
     * Delete method
     *
     * @param int|null $id State Machine Item State id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete(?int $id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineItemState = $this->StateMachineItemStates->get($id);
        if ($this->StateMachineItemStates->delete($stateMachineItemState)) {
            $this->Flash->success(__('The state machine item state has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine item state could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
