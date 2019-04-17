<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

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
     * @return \Cake\Http\Response|null
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
     * @param string|null $id State Machine Item State id.
     *
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        $stateMachineItemState = $this->StateMachineItemStates->get($id, [
            'contain' => ['StateMachineProcesses', 'StateMachineItemStateHistory', 'StateMachineTimeouts'],
        ]);

        $this->set(compact('stateMachineItemState'));
        $this->set('_serialize', ['stateMachineItemState']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stateMachineItemState = $this->StateMachineItemStates->newEntity();
        if ($this->request->is('post')) {
            $stateMachineItemState = $this->StateMachineItemStates->patchEntity($stateMachineItemState, (array)$this->request->getData());
            if ($this->StateMachineItemStates->save($stateMachineItemState)) {
                $this->Flash->success(__('The state machine item state has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine item state could not be saved. Please, try again.'));
            }
        }
        $stateMachineProcesses = $this->StateMachineItemStates->StateMachineProcesses->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineItemState', 'stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineItemState']);
    }

    /**
     * Edit method
     *
     * @param string|null $id State Machine Item State id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $stateMachineItemState = $this->StateMachineItemStates->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stateMachineItemState = $this->StateMachineItemStates->patchEntity($stateMachineItemState, (array)$this->request->getData());
            if ($this->StateMachineItemStates->save($stateMachineItemState)) {
                $this->Flash->success(__('The state machine item state has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine item state could not be saved. Please, try again.'));
            }
        }
        $stateMachineProcesses = $this->StateMachineItemStates->StateMachineProcesses->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineItemState', 'stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineItemState']);
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Item State id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
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
