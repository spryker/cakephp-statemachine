<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

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
     * @param string|null $id State Machine Process id.
     *
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        $stateMachineProcess = $this->StateMachineProcesses->get($id, [
            'contain' => ['StateMachineItemStates', 'StateMachineTimeouts', 'StateMachineTransitionLogs'],
        ]);

        $this->set(compact('stateMachineProcess'));
        $this->set('_serialize', ['stateMachineProcess']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stateMachineProcess = $this->StateMachineProcesses->newEntity();
        if ($this->request->is('post')) {
            $stateMachineProcess = $this->StateMachineProcesses->patchEntity($stateMachineProcess, (array)$this->request->getData());
            if ($this->StateMachineProcesses->save($stateMachineProcess)) {
                $this->Flash->success(__('The state machine process has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine process could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('stateMachineProcess'));
        $this->set('_serialize', ['stateMachineProcess']);
    }

    /**
     * Edit method
     *
     * @param string|null $id State Machine Process id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $stateMachineProcess = $this->StateMachineProcesses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stateMachineProcess = $this->StateMachineProcesses->patchEntity($stateMachineProcess, (array)$this->request->getData());
            if ($this->StateMachineProcesses->save($stateMachineProcess)) {
                $this->Flash->success(__('The state machine process has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine process could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('stateMachineProcess'));
        $this->set('_serialize', ['stateMachineProcess']);
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Process id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
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
