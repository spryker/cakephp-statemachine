<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use StateMachine\Controller\CastTrait;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\FactoryTrait;

/**
 * @property \Cake\ORM\Table $Graph
 */
class GraphController extends AppController
{
    use FactoryTrait;
    use CastTrait;

    /**
     * @var string
     */
    public const URL_PARAM_PROCESS = 'process';

    /**
     * @var string
     */
    public const URL_PARAM_FORMAT = 'format';

    /**
     * @var string
     */
    public const URL_PARAM_FONT_SIZE = 'font';

    /**
     * @var string
     */
    public const URL_PARAM_HIGHLIGHT_STATE = 'highlight-state';

    /**
     * @var string
     */
    public const URL_PARAM_STATE_MACHINE = 'state-machine';

    /**
     * Returns an image of the state machine.
     *
     * It can also output in specific format (JPG, PDF, ...)
     *
     * @throws \NotFoundException
     *
     * @return \Cake\Http\Response
     */
    public function draw(): Response
    {
        $processName = $this->castString($this->request->getQuery(self::URL_PARAM_PROCESS));
        $stateMachine = $this->castString($this->request->getQuery(self::URL_PARAM_STATE_MACHINE));
        if (!$processName || !$stateMachine) {
            throw new NotFoundException('Missing state machine or process name.');
        }

        $format = $this->assertString($this->request->getQuery(self::URL_PARAM_FORMAT));
        $fontSize = $this->assertInt($this->request->getQuery(self::URL_PARAM_FONT_SIZE));
        $highlightState = $this->assertString($this->request->getQuery(self::URL_PARAM_HIGHLIGHT_STATE));

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
                $processDto->getStateMachineNameOrFail(),
            )->draw($process, $highlightState, $format, $fontSize);

        $this->response = $this->response->withType($format);

        return $this->response->withStringBody($response);
    }
}
