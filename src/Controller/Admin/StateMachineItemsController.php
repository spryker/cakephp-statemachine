<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

/**
 * StateMachineItems Controller
 *
 * @property \StateMachine\Model\Table\StateMachineItemsTable $StateMachineItems
 *
 * @method \StateMachine\Model\Entity\StateMachineItem[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineItemsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $stateMachineItems = $this->paginate($this->StateMachineItems);

        $this->set(compact('stateMachineItems'));
    }

    /**
     * View method
     *
     * @param string|null $id State Machine Item id.
     *
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        $stateMachineItem = $this->StateMachineItems->get($id, [
            'contain' => ['StateMachineTransitionLogs' => 'StateMachineProcesses'],
        ]);

        $this->set('stateMachineItem', $stateMachineItem);
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Item id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineItem = $this->StateMachineItems->get($id);
        if ($this->StateMachineItems->delete($stateMachineItem)) {
            $this->Flash->success(__('The state machine item has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine item could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
