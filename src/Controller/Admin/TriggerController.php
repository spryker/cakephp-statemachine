<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;
use Exception;
use StateMachine\Controller\CastTrait;
use StateMachine\Dto\StateMachine\ItemDto;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\FacadeAwareTrait;

/**
 * @property \Cake\ORM\Table $Trigger
 */
class TriggerController extends AppController
{
    use FacadeAwareTrait;
    use CastTrait;

    public const URL_PARAM_IDENTIFIER = 'identifier';
    public const URL_PARAM_ID_STATE = 'id-state';
    public const URL_PARAM_STATE = 'state';
    public const URL_PARAM_STATE_MACHINE = 'state-machine';
    public const URL_PARAM_PROCESS = 'process';
    public const URL_PARAM_REDIRECT = 'redirect';
    public const URL_PARAM_EVENT = 'event';
    public const URL_PARAM_CATCH = 'catch';

    public const DEFAULT_REDIRECT_URL = [
        'controller' => 'StateMachine',
        'action' => 'index',
    ];

    /**
     * @var array
     */
    public $components = [
        'Flash',
    ];

    /**
     * Trigger event for new identifier.
     *
     * @throws \Exception
     *
     * @return \Cake\Http\Response|null
     */
    public function eventForNewItem(): ?Response
    {
        $this->request->allowMethod('post');

        $stateMachineName = $this->castString($this->request->getQuery(static::URL_PARAM_STATE_MACHINE));
        $processName = $this->castString($this->request->getQuery(self::URL_PARAM_PROCESS));

        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName($stateMachineName);

        $identifier = $this->castInt($this->request->getQuery(static::URL_PARAM_IDENTIFIER));
        $catchException = $this->assertBool($this->request->getQuery(static::URL_PARAM_CATCH));
        $redirect = $this->assertString($this->request->getQuery(static::URL_PARAM_REDIRECT));

        try {
            $this->getFacade()->triggerForNewStateMachineItem($processDto, $identifier);
            if ($redirect !== 'no') {
                $this->Flash->success('Initialized.');
            }
        } catch (Exception $exception) {
            if (!$catchException) {
                throw $exception;
            }

            if ($redirect !== 'no') {
                $this->Flash->error($exception->getMessage());
            }
        }

        if ($redirect === 'no') {
            $this->autoRender = false;
            return null;
        }

        return $this->redirect($redirect ?: $this->referer(static::DEFAULT_REDIRECT_URL, true));
    }

    /**
     * Trigger event for existing identifier/state-machine-process.
     *
     * @throws \Exception
     *
     * @return \Cake\Http\Response|null
     */
    public function event(): ?Response
    {
        $this->request->allowMethod('post');

        $stateMachineName = $this->castString($this->request->getQuery(static::URL_PARAM_STATE_MACHINE));
        $processName = $this->castString($this->request->getQuery(self::URL_PARAM_PROCESS));
        $identifier = $this->castInt($this->request->getQuery(self::URL_PARAM_IDENTIFIER));
        $idState = $this->assertInt($this->request->getQuery(self::URL_PARAM_ID_STATE));
        $stateName = $this->assertString($this->request->getQuery(self::URL_PARAM_STATE));

        $itemDto = new ItemDto();
        $itemDto->setIdentifier($identifier);
        if ($idState) {
            $itemDto->setIdItemState($idState);
        } else {
            $itemDto->setStateName($stateName);
        }
        $itemDto->setStateMachineName($stateMachineName);
        $itemDto->setProcessName($processName);

        $eventName = $this->castString($this->request->getQuery(self::URL_PARAM_EVENT));
        $catchException = $this->assertBool($this->request->getQuery(static::URL_PARAM_CATCH));
        $redirect = $this->assertString($this->request->getQuery(static::URL_PARAM_REDIRECT));

        try {
            $this->getFacade()->triggerEvent($eventName, $itemDto);
            if ($redirect !== 'no') {
                $this->Flash->success(sprintf('Event `%s` triggered.', $eventName));
            }
        } catch (Exception $exception) {
            if (!$catchException) {
                throw $exception;
            }

            if ($redirect !== 'no') {
                $this->Flash->error($exception->getMessage());
            }
        }

        if ($redirect === 'no') {
            $this->autoRender = false;
            return null;
        }

        return $this->redirect($redirect ?: $this->referer(static::DEFAULT_REDIRECT_URL, true));
    }
}
