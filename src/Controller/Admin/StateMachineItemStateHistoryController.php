<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

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
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['StateMachineItemStates'],
        ];
        $stateMachineItemStateHistory = $this->paginate();

        $this->set(compact('stateMachineItemStateHistory'));
        $this->set('_serialize', ['stateMachineItemStateHistory']);
    }

    /**
     * View method
     *
     * @param string|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->get($id, [
            'contain' => ['StateMachineItemStates'],
        ]);

        $this->set(compact('stateMachineItemStateHistory'));
        $this->set('_serialize', ['stateMachineItemStateHistory']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->newEntity();
        if ($this->request->is('post')) {
            $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->patchEntity($stateMachineItemStateHistory, (array)$this->request->getData());
            if ($this->StateMachineItemStateHistory->save($stateMachineItemStateHistory)) {
                $this->Flash->success(__('The state machine item state history has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine item state history could not be saved. Please, try again.'));
            }
        }
        $stateMachineItemStates = $this->StateMachineItemStateHistory->StateMachineItemStates->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineItemStateHistory', 'stateMachineItemStates'));
        $this->set('_serialize', ['stateMachineItemStateHistory']);
    }

    /**
     * Edit method
     *
     * @param string|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stateMachineItemStateHistory = $this->StateMachineItemStateHistory->patchEntity($stateMachineItemStateHistory, (array)$this->request->getData());
            if ($this->StateMachineItemStateHistory->save($stateMachineItemStateHistory)) {
                $this->Flash->success(__('The state machine item state history has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine item state history could not be saved. Please, try again.'));
            }
        }
        $stateMachineItemStates = $this->StateMachineItemStateHistory->StateMachineItemStates->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineItemStateHistory', 'stateMachineItemStates'));
        $this->set('_serialize', ['stateMachineItemStateHistory']);
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
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
