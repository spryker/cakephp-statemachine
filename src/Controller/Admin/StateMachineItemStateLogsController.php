<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * @property \StateMachine\Model\Table\StateMachineItemStateLogsTable $StateMachineItemStateLogs
 *
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineItemStateLogsController extends AppController
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
        $stateMachineItemStateLogs = $this->paginate();

        $this->set(compact('stateMachineItemStateLogs'));
        $this->set('_serialize', ['stateMachineItemStateLogs']);
    }

    /**
     * View method
     *
     * @param string|int|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $stateMachineItemStateLog = $this->StateMachineItemStateLogs->get($id, [
            'contain' => ['StateMachineItemStates'],
        ]);

        $this->set(compact('stateMachineItemStateLog'));
    }

    /**
     * Delete method
     *
     * @param string|int|null $id State Machine Item State History id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateMachineItemStateLog = $this->StateMachineItemStateLogs->get($id);
        if ($this->StateMachineItemStateLogs->delete($stateMachineItemStateLog)) {
            $this->Flash->success(__('The state machine item state log has been deleted.'));
        } else {
            $this->Flash->error(__('The state machine item state log could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
