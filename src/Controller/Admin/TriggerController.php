<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use StateMachine\FacadeAwareTrait;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class TriggerController extends AppController
{
    use FacadeAwareTrait;

    public const URL_PARAM_IDENTIFIER = 'identifier';
    public const URL_PARAM_ID_STATE = 'id-state';
    public const URL_PARAM_ID_PROCESS = 'id-process';
    public const URL_PARAM_STATE_MACHINE = 'state-machine';
    public const URL_PARAM_PROCESS = 'process';
    public const URL_PARAM_REDIRECT = 'redirect';
    public const URL_PARAM_EVENT = 'event';

    public const DEFAULT_REDIRECT_URL = [
        'controller' => 'StateMachine',
        'action' => 'index',
    ];

    /**
     * @return \Cake\Http\Response|null
     */
    public function eventForNewItem(): ?Response
    {
        $stateMachineName = $this->request->getQuery(static::URL_PARAM_STATE_MACHINE);
        $processName = $this->request->getQuery(self::URL_PARAM_PROCESS);

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $identifier = $this->request->getQuery(static::URL_PARAM_IDENTIFIER);
        $this->getFacade()->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $redirect = $this->request->getQuery(static::URL_PARAM_REDIRECT, static::DEFAULT_REDIRECT_URL);
        if (!$redirect) {
            $this->autoRender = false;
            return null;
        }

        return $this->redirect($redirect);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function event(): ?Response
    {
        $identifier = $this->request->getQuery(self::URL_PARAM_IDENTIFIER);
        $idState = $this->castId($this->request->getQuery(self::URL_PARAM_ID_STATE));

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setIdentifier($identifier);
        $stateMachineItemTransfer->setIdItemState($idState);

        $stateMachineName = $this->request->getQuery(static::URL_PARAM_STATE_MACHINE);
        $stateMachineItemTransfer->setStateMachineName($stateMachineName);
        $processName = $this->request->getQuery(self::URL_PARAM_PROCESS);
        $stateMachineItemTransfer->setProcessName($processName);

        $eventName = $this->request->getQuery(self::URL_PARAM_EVENT);
        $this->getFacade()->triggerEvent($eventName, $stateMachineItemTransfer);

        $redirect = $this->request->getQuery(self::URL_PARAM_REDIRECT, self::DEFAULT_REDIRECT_URL);
        if (!$redirect) {
            $this->autoRender = false;
            return null;
        }

        return $this->redirect($redirect);
    }

    /**
     * @param string|int|null $id
     *
     * @throws \Cake\Http\Exception\NotFoundException
     *
     * @return int
     */
    protected function castId($id): int
    {
        if (!is_numeric($id) || $id === 0) {
            throw new NotFoundException('The given id is not numeric or 0 (zero)');
        }

        return (int)$id;
    }
}
