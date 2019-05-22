<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * @property \StateMachine\Model\Table\StateMachineItemStateHistoryTable $StateMachineItemStateHistory
 *
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineItemStateHistoryController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->paginate = [
            'order' => [
                'id' => 'DESC',
            ],
            'contain' => ['StateMachineItemStates'],
        ];
        $stateMachineItemStateHistory = $this->paginate();

        $this->set(compact('stateMachineItemStateHistory'));
        $this->set('_serialize', ['stateMachineItemStateHistory']);
    }

    /**
     * View method
     *
     * @param int|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function view(?int $id = null)
    {
        $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->get($id, [
            'contain' => ['StateMachineItemStates'],
        ]);

        $this->set(compact('stateMachineItemStateHistory'));
        $this->set('_serialize', ['stateMachineItemStateHistory']);
    }

    /**
     * Delete method
     *
     * @param int|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete(?int $id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->get($id);
        if ($this->StateMachineItemStateHistory->delete($stateMachineItemStateHistory)) {
            $this->Flash->success(__('The state machine item state history has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine item state history could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
