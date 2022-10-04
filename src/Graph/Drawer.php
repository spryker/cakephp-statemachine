<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

use StateMachine\Business\Exception\DrawerException;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\Process\TransitionInterface;
use StateMachine\Dependency\StateMachineHandlerInterface;

class Drawer implements DrawerInterface
{
    /**
     * @var string
     */
    public const ATTRIBUTE_FONT_SIZE = 'fontsize';

    /**
     * @var string
     */
    public const EDGE_UPPER_HALF = 'upper half';

    /**
     * @var string
     */
    public const EDGE_LOWER_HALF = 'lower half';

    /**
     * @var string
     */
    public const EDGE_FULL = 'edge full';

    /**
     * @var string
     */
    public const HIGHLIGHT_COLOR = '#FFFFCC';

    /**
     * @var string
     */
    public const HAPPY_PATH_COLOR = '#70ab28';

    /**
     * @var array
     */
    protected $attributesProcess = [
        'fontname' => 'Verdana',
        'fillcolor' => '#cfcfcf',
        'style' => 'filled',
        'color' => '#ffffff',
        'fontsize' => 12,
        'fontcolor' => 'black',
    ];

    /**
     * @var array
     */
    protected $attributesState = [
        'fontname' => 'Verdana',
        'fontsize' => 14,
        'style' => 'filled',
        'fillcolor' => '#f9f9f9',
    ];

    /**
     * @var array
     */
    protected $attributesDiamond = [
        'fontname' => 'Verdana',
        'label' => '?',
        'shape' => 'diamond',
        'fontcolor' => 'white',
        'fontsize' => '1',
        'style' => 'filled',
        'fillcolor' => '#f9f9f9',
    ];

    /**
     * @var array
     */
    protected $attributesTransition = [
        'fontname' => 'Verdana',
        'fontsize' => 12,
    ];

    /**
     * @var string
     */
    protected $brLeft = '<br align="left"/>';

    /**
     * @var string
     */
    protected $notImplemented = '<font color="red">(not implemented)</font>';

    /**
     * @var string
     */
    protected $br = '<br/>';

    /**
     * @var string
     */
    protected $format = 'svg';

    /**
     * @var int|null
     */
    protected $fontSizeBig;

    /**
     * @var int|null
     */
    protected $fontSizeSmall;

    /**
     * @var \StateMachine\Graph\GraphInterface
     */
    protected $graph;

    /**
     * @var \StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected $stateMachineHandler;

    /**
     * @param \StateMachine\Graph\GraphInterface $graph
     * @param \StateMachine\Dependency\StateMachineHandlerInterface $stateMachineHandler
     */
    public function __construct(GraphInterface $graph, StateMachineHandlerInterface $stateMachineHandler)
    {
        $this->graph = $graph;
        $this->stateMachineHandler = $stateMachineHandler;
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string|null $highlightState
     * @param string|null $format
     * @param int|null $fontSize
     *
     * @return string
     */
    public function draw(ProcessInterface $process, ?string $highlightState = null, ?string $format = null, ?int $fontSize = null): string
    {
        $this->init($format, $fontSize);

        $this->drawClusters($process);
        $this->drawStates($process, $highlightState);
        $this->drawTransitions($process);

        return $this->graph->render($this->format);
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string|null $highlightState
     *
     * @return void
     */
    public function drawStates(ProcessInterface $process, ?string $highlightState = null): void
    {
        $states = $process->getAllStates();
        foreach ($states as $state) {
            $isHighlighted = $highlightState === $state->getName();
            $this->addNode($state, [], null, $isHighlighted);
        }
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return void
     */
    public function drawTransitions(ProcessInterface $process): void
    {
        $states = $process->getAllStates();
        foreach ($states as $state) {
            $this->drawTransitionsEvents($state);
            $this->drawTransitionsConditions($state);
        }
    }

    /**
     * @return string
     */
    protected function getDiamondId(): string
    {
        $stringGenerator = new StringGenerator();

        return $stringGenerator->generateRandomString(32);
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @throws \StateMachine\Business\Exception\DrawerException
     *
     * @return void
     */
    public function drawTransitionsEvents(StateInterface $state): void
    {
        $events = $state->getEvents();
        foreach ($events as $event) {
            $transitions = $state->getOutgoingTransitionsByEvent($event);

            $currentTransition = current($transitions);
            if (!$currentTransition) {
                throw new DrawerException('Transitions container seems to be empty.');
            }

            if (count($transitions) > 1) {
                $diamondId = $this->getDiamondId();

                $this->graph->addNode($diamondId, $this->attributesDiamond, $state->getProcess()->getName());
                $this->addEdge($currentTransition, self::EDGE_UPPER_HALF, [], null, $diamondId);

                foreach ($transitions as $transition) {
                    $this->addEdge($transition, self::EDGE_LOWER_HALF, [], $diamondId);
                }
            } else {
                $this->addEdge($currentTransition, self::EDGE_FULL);
            }
        }
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return void
     */
    public function drawTransitionsConditions(StateInterface $state): void
    {
        $transitions = $state->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                continue;
            }
            $this->addEdge($transition);
        }
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return void
     */
    public function drawClusters(ProcessInterface $process): void
    {
        $processes = $process->getAllProcesses();
        foreach ($processes as $subProcess) {
            $group = $subProcess->getName();
            $attributes = $this->attributesProcess;
            $attributes['label'] = $group;

            $this->graph->addCluster($group, $attributes);
        }
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     * @param array $attributes
     * @param string|null $name
     * @param bool $highlighted
     *
     * @return void
     */
    protected function addNode(StateInterface $state, array $attributes = [], ?string $name = null, bool $highlighted = false): void
    {
        $name = $name ?? $state->getName();

        $labelName = $state->hasDisplay() ? $state->getDisplay() : $name;
        $label = [];
        $label[] = str_replace(' ', $this->br, $labelName);

        if ($state->hasFlags()) {
            $flags = implode(', ', $state->getFlags());
            $label[] = '<font color="violet" point-size="' . $this->fontSizeSmall . '">' . $flags . '</font>';
        }

        $attributes['label'] = implode($this->br, $label);

        if (!$state->hasOutgoingTransitions() || $this->hasOnlySelfReferences($state)) {
            $attributes['peripheries'] = 2;
        }

        if ($highlighted) {
            $attributes['fillcolor'] = self::HIGHLIGHT_COLOR;
        }

        $attributes = array_merge($this->attributesState, $attributes);
        $this->graph->addNode($name, $attributes, $state->getProcess()->getName());
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return bool
     */
    protected function hasOnlySelfReferences(StateInterface $state): bool
    {
        $hasOnlySelfReferences = true;
        $transitions = $state->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->getTargetState()->getName() !== $state->getName()) {
                $hasOnlySelfReferences = false;

                break;
            }
        }

        return $hasOnlySelfReferences;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param string $type
     * @param array $attributes
     * @param string|null $fromName
     * @param string|null $toName
     *
     * @return void
     */
    protected function addEdge(
        TransitionInterface $transition,
        string $type = self::EDGE_FULL,
        array $attributes = [],
        ?string $fromName = null,
        ?string $toName = null
    ): void {
        $label = [];

        if ($type !== self::EDGE_LOWER_HALF) {
            $label = $this->addEdgeEventText($transition, $label);
        }

        if ($type !== self::EDGE_UPPER_HALF) {
            $label = $this->addEdgeConditionText($transition, $label);
        }

        $label = $this->addEdgeElse($label);
        $fromName = $this->addEdgeFromState($transition, $fromName);
        $toName = $this->addEdgeToState($transition, $toName);
        $attributes = $this->addEdgeAttributes($transition, $attributes, $label, $type);

        $this->graph->addEdge($fromName, $toName, $attributes);
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param array $label
     *
     * @return array
     */
    protected function addEdgeConditionText(TransitionInterface $transition, array $label): array
    {
        if ($transition->hasCondition()) {
            $conditionLabel = $transition->getCondition();

            if (!isset($this->stateMachineHandler->getConditions()[$transition->getCondition()])) {
                $conditionLabel .= ' ' . $this->notImplemented;
            }

            $label[] = $conditionLabel;
        }

        return $label;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param array $label
     *
     * @return array
     */
    protected function addEdgeEventText(TransitionInterface $transition, array $label): array
    {
        if ($transition->hasEvent()) {
            $event = $transition->getEvent();

            if ($event->isOnEnter()) {
                $label[] = '<b>' . $event->getName() . ' (on enter)</b>';
            } else {
                $label[] = '<b>' . $event->getName() . '</b>';
            }

            if ($event->hasTimeout()) {
                $label[] = 'timeout: ' . $event->getTimeout();
            }

            if ($event->hasCommand()) {
                $commandLabel = 'command:' . $event->getCommand();

                if (!isset($this->stateMachineHandler->getCommands()[$event->getCommand()])) {
                    $commandLabel .= ' ' . $this->notImplemented;
                }
                $label[] = $commandLabel;
            }

            if ($event->isManual()) {
                $label[] = 'manually executable';
            }
        } else {
            $label[] = '&infin;';
        }

        return $label;
    }

    /**
     * @param array $label
     *
     * @return string
     */
    protected function addEdgeElse(array $label): string
    {
        if ($label) {
            $label = implode($this->brLeft, $label);
        } else {
            $label = 'else';
        }

        return $label;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param array $attributes
     * @param string $label
     * @param string $type
     *
     * @return array
     */
    protected function addEdgeAttributes(TransitionInterface $transition, array $attributes, string $label, string $type = self::EDGE_FULL): array
    {
        $attributes = array_merge($this->attributesTransition, $attributes);
        $attributes['label'] = '  ' . $label;

        if ($transition->hasEvent() === false) {
            $attributes['style'] = 'dashed';
        }

        if ($type === self::EDGE_FULL || $type === self::EDGE_UPPER_HALF) {
            if ($transition->hasEvent() && $transition->getEvent()->isOnEnter()) {
                $attributes['arrowtail'] = 'crow';
                $attributes['dir'] = 'both';
            }
        }

        if ($transition->isHappyCase()) {
            $attributes['weight'] = '100';
            $attributes['color'] = self::HAPPY_PATH_COLOR;
        } elseif ($transition->hasEvent()) {
            $attributes['weight'] = '10';
        } else {
            $attributes['weight'] = '1';
        }

        return $attributes;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param string|null $fromName
     *
     * @return string
     */
    protected function addEdgeFromState(TransitionInterface $transition, ?string $fromName): string
    {
        $fromName = $fromName ?? $transition->getSourceState()->getName();

        return $fromName;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param string|null $toName
     *
     * @return string
     */
    protected function addEdgeToState(TransitionInterface $transition, ?string $toName): string
    {
        $toName = $toName ?? $transition->getTargetState()->getName();

        return $toName;
    }

    /**
     * @param string|null $format
     * @param int|null $fontSize
     *
     * @return void
     */
    protected function init(?string $format, ?int $fontSize): void
    {
        if ($format !== null) {
            $this->format = $format;
        }

        if ($fontSize !== null) {
            $this->attributesState[self::ATTRIBUTE_FONT_SIZE] = $fontSize;
            $this->attributesProcess[self::ATTRIBUTE_FONT_SIZE] = $fontSize - 2;
            $this->attributesTransition[self::ATTRIBUTE_FONT_SIZE] = $fontSize - 2;
            $this->fontSizeBig = $fontSize;
            $this->fontSizeSmall = $fontSize - 2;
        }
    }
}
