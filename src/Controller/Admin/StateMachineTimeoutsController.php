<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

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
     * @return \Cake\Http\Response|null
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
     * @param string|null $id State Machine Timeout id.
     *
     * @return \Cake\Http\Response|null
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
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stateMachineTimeout = $this->StateMachineTimeouts->newEntity();
        if ($this->request->is('post')) {
            $stateMachineTimeout = $this->StateMachineTimeouts->patchEntity($stateMachineTimeout, $this->request->data);
            if ($this->StateMachineTimeouts->save($stateMachineTimeout)) {
                $this->Flash->success(__('The state machine timeout has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine timeout could not be saved. Please, try again.'));
            }
        }
        $stateMachineItemStates = $this->StateMachineTimeouts->StateMachineItemStates->find('list', ['limit' => 1000]);
        $stateMachineProcesses = $this->StateMachineTimeouts->StateMachineProcesses->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineTimeout', 'stateMachineItemStates', 'stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineTimeout']);
    }

    /**
     * Edit method
     *
     * @param string|null $id State Machine Timeout id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $stateMachineTimeout = $this->StateMachineTimeouts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stateMachineTimeout = $this->StateMachineTimeouts->patchEntity($stateMachineTimeout, $this->request->data);
            if ($this->StateMachineTimeouts->save($stateMachineTimeout)) {
                $this->Flash->success(__('The state machine timeout has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine timeout could not be saved. Please, try again.'));
            }
        }
        $stateMachineItemStates = $this->StateMachineTimeouts->StateMachineItemStates->find('list', ['limit' => 1000]);
        $stateMachineProcesses = $this->StateMachineTimeouts->StateMachineProcesses->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineTimeout', 'stateMachineItemStates', 'stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineTimeout']);
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Timeout id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
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
