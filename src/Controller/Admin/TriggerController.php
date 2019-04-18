<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;
use StateMachine\Controller\CastTrait;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\FacadeAwareTrait;

class TriggerController extends AppController
{
    use FacadeAwareTrait;
    use CastTrait;

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
        $stateMachineName = $this->castString($this->request->getQuery(static::URL_PARAM_STATE_MACHINE)) ?: null;
        $processName = $this->castString($this->request->getQuery(self::URL_PARAM_PROCESS)) ?: null;

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName($stateMachineName);

        $identifier = $this->castInt($this->request->getQuery(static::URL_PARAM_IDENTIFIER));
        $this->getFacade()->triggerForNewStateMachineItem($processDto, $identifier);

        $redirect = $this->assertString($this->request->getQuery(static::URL_PARAM_REDIRECT)) ?: static::DEFAULT_REDIRECT_URL;
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
        $identifier = $this->castInt($this->request->getQuery(self::URL_PARAM_IDENTIFIER));
        $idState = $this->castInt($this->request->getQuery(self::URL_PARAM_ID_STATE));

        $itemDto = new ItemDto();
        $itemDto->setIdentifier($identifier);
        $itemDto->setIdItemState($idState);

        $stateMachineName = $this->castString($this->request->getQuery(static::URL_PARAM_STATE_MACHINE)) ?: null;
        $itemDto->setStateMachineName($stateMachineName);
        $processName = $this->castString($this->request->getQuery(self::URL_PARAM_PROCESS)) ?: null;
        $itemDto->setProcessName($processName);

        $eventName = $this->castString($this->request->getQuery(self::URL_PARAM_EVENT)) ?: null;
        $this->getFacade()->triggerEvent($eventName, $itemDto);

        $redirect = $this->request->getQuery(self::URL_PARAM_REDIRECT, self::DEFAULT_REDIRECT_URL);
        if (!$redirect) {
            $this->autoRender = false;
            return null;
        }

        return $this->redirect($redirect);
    }
}
