<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use StateMachine\Controller\CastTrait;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\FactoryTrait;

class GraphController extends AppController
{
    use FactoryTrait;
    use CastTrait;

    public const URL_PARAM_PROCESS = 'process';
    public const URL_PARAM_FORMAT = 'format';
    public const URL_PARAM_FONT_SIZE = 'font';
    public const URL_PARAM_HIGHLIGHT_STATE = 'highlight-state';
    public const URL_PARAM_STATE_MACHINE = 'state-machine';

    /**
     * @return \Cake\Http\Response|null
     */
    public function draw()
    {
        $processName = $this->castString($this->request->getQuery(self::URL_PARAM_PROCESS)) ?: null;
        if ($processName === null) {
            return $this->redirect(['controller' => 'StateMachine', 'action' => 'index']);
        }

        $format = $this->assertString($this->request->getQuery(self::URL_PARAM_FORMAT));
        $fontSize = $this->assertInt($this->request->getQuery(self::URL_PARAM_FONT_SIZE));
        $highlightState = $this->assertString($this->request->getQuery(self::URL_PARAM_HIGHLIGHT_STATE));
        $stateMachine = $this->assertString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE));

        $stateMachineBundleConfig = $this->getFactory()->getConfig();
        if ($format === null) {
            $format = $stateMachineBundleConfig->getGraphDefaultFormat();
        }
        if ($fontSize === 0) {
            $fontSize = $stateMachineBundleConfig->getGraphDefaultFontSize();
        }

        $processDto = new ProcessDto();
        $processDto->setStateMachineName($stateMachine);
        $processDto->setProcessName($processName);

        $process = $this->getFactory()
            ->createStateMachineBuilder()
            ->createProcess($processDto);

        $response = $this->getFactory()
            ->createGraphDrawer(
                $processDto->getStateMachineNameOrFail()
            )->draw($process, $highlightState, $format, $fontSize);

        /*
        return $this->streamedResponse(
            $callback,
            Response::HTTP_OK,
            $this->getStreamedResponseHeaders($format)
        );
        */
        $this->response = $this->response->withType($format);

        return $this->response->withStringBody($response);
    }
}
