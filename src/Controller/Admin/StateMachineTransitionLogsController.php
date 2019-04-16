<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

/**
 * @property \StateMachine\Model\Table\StateMachineTransitionLogsTable $StateMachineTransitionLogs
 *
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineTransitionLogsController extends AppController
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
        $stateMachineTransitionLogs = $this->paginate();

        $this->set(compact('stateMachineTransitionLogs'));
        $this->set('_serialize', ['stateMachineTransitionLogs']);
    }

    /**
     * View method
     *
     * @param string|null $id State Machine Transition Log id.
     *
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        $stateMachineTransitionLog = $this->StateMachineTransitionLogs->get($id, [
            'contain' => ['StateMachineProcesses'],
        ]);

        $this->set(compact('stateMachineTransitionLog'));
        $this->set('_serialize', ['stateMachineTransitionLog']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stateMachineTransitionLog = $this->StateMachineTransitionLogs->newEntity();
        if ($this->request->is('post')) {
            $stateMachineTransitionLog = $this->StateMachineTransitionLogs->patchEntity($stateMachineTransitionLog, $this->request->getData());
            if ($this->StateMachineTransitionLogs->save($stateMachineTransitionLog)) {
                $this->Flash->success(__('The state machine transition log has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine transition log could not be saved. Please, try again.'));
            }
        }
        $stateMachineProcesses = $this->StateMachineTransitionLogs->StateMachineProcesses->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineTransitionLog', 'stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineTransitionLog']);
    }

    /**
     * Edit method
     *
     * @param string|null $id State Machine Transition Log id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $stateMachineTransitionLog = $this->StateMachineTransitionLogs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stateMachineTransitionLog = $this->StateMachineTransitionLogs->patchEntity($stateMachineTransitionLog, $this->request->getData());
            if ($this->StateMachineTransitionLogs->save($stateMachineTransitionLog)) {
                $this->Flash->success(__('The state machine transition log has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The state machine transition log could not be saved. Please, try again.'));
            }
        }
        $stateMachineProcesses = $this->StateMachineTransitionLogs->StateMachineProcesses->find('list', ['limit' => 1000]);

        $this->set(compact('stateMachineTransitionLog', 'stateMachineProcesses'));
        $this->set('_serialize', ['stateMachineTransitionLog']);
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Transition Log id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineTransitionLog = $this->StateMachineTransitionLogs->get($id);
        if ($this->StateMachineTransitionLogs->delete($stateMachineTransitionLog)) {
            $this->Flash->success(__('The state machine transition log has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine transition log could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
