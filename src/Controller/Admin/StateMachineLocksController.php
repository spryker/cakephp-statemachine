<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineLock> paginate($object = null, array $settings = [])
 */
class StateMachineLocksController extends AppController
{
    /**
     * @var \StateMachine\Model\Table\StateMachineLocksTable
     */
    protected $StateMachineLocks;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->StateMachineLocks = $this->fetchModel('StateMachine.StateMachineLocks');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $query = $this->StateMachineLocks->find();
        $stateMachineLocks = $this->paginate($query);

        $this->set(compact('stateMachineLocks'));
    }

    /**
     * View method
     *
     * @param string|int|null $id State Machine Lock id.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $stateMachineLock = $this->StateMachineLocks->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('stateMachineLock'));
    }

    /**
     * Delete method
     *
     * @param string|int|null $id State Machine Lock id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineLock = $this->StateMachineLocks->get($id);
        if ($this->StateMachineLocks->delete($stateMachineLock)) {
            $this->Flash->success(__('The state machine lock has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine lock could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
