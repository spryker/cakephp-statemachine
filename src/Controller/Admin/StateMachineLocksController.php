<?php
namespace StateMachine\Controller\Admin;

use App\Controller\AppController;

/**
 * @property \StateMachine\Model\Table\StateMachineLocksTable $StateMachineLocks
 *
 * @method \StateMachine\Model\Entity\StateMachineLock[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateMachineLocksController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $stateMachineLocks = $this->paginate();

        $this->set(compact('stateMachineLocks'));
    }

    /**
     * View method
     *
     * @param string|null $id State Machine Lock id.
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        $stateMachineLock = $this->StateMachineLocks->get($id, [
            'contain' => []
        ]);

        $this->set(compact('stateMachineLock'));
    }

    /**
     * Delete method
     *
     * @param string|null $id State Machine Lock id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
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
