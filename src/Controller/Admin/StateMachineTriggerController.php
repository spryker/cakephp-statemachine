<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;
use StateMachine\Exception\InvalidIdException;
use StateMachine\FacadeAwareTrait;
use StateMachine\Transfer\StateMachineItemTransfer;
use StateMachine\Transfer\StateMachineProcessTransfer;

class StateMachineTriggerController extends AppController
{
    use FacadeAwareTrait;

    public const URL_PARAM_IDENTIFIER = 'identifier';
    public const URL_PARAM_ID_STATE = 'id-state';
    public const URL_PARAM_ID_PROCESS = 'id-process';
    public const URL_PARAM_STATE_MACHINE_NAME = 'state-machine-name';
    public const URL_PARAM_PROCESS_NAME = 'process-name';
    public const URL_PARAM_REDIRECT = 'redirect';
    public const URL_PARAM_EVENT = 'event';

    public const DEFAULT_REDIRECT_URL = '/state-machine/list';

    /**
     * @return \Cake\Http\Response|null
     */
    public function triggerEventForNewItemAction(): ?Response
    {
        $stateMachineName = $this->request->getParam(static::URL_PARAM_STATE_MACHINE_NAME);
        $processName = $this->request->getParam(self::URL_PARAM_PROCESS_NAME);

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $identifier = $this->request->getParam(static::URL_PARAM_IDENTIFIER);
        $this->getFacade()->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $redirect = $this->request->getParam(static::URL_PARAM_REDIRECT, static::DEFAULT_REDIRECT_URL);

        return $this->redirect(htmlentities($redirect));
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function triggerEventAction(): ?Response
    {
        $identifier = $this->request->getParam(self::URL_PARAM_IDENTIFIER);
        $idState = $this->castId($this->request->getParam(self::URL_PARAM_ID_STATE));

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setIdentifier($identifier);
        $stateMachineItemTransfer->setIdItemState($idState);

        $eventName = $this->request->getParam(self::URL_PARAM_EVENT);
        $this->getFacade()->triggerEvent($eventName, $stateMachineItemTransfer);

        $redirect = $this->request->getParam(self::URL_PARAM_REDIRECT, self::DEFAULT_REDIRECT_URL);

        return $this->redirect(htmlentities($redirect));
    }

    /**
     * @param mixed $id
     *
     * @throws \StateMachine\Exception\InvalidIdException
     *
     * @return int
     */
    protected function castId($id): int
    {
        if (!is_numeric($id) || $id === 0) {
            throw new InvalidIdException('The given id is not numeric or 0 (zero)');
        }

        return (int)$id;
    }
}
