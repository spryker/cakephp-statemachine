<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\StateMachine;

use RuntimeException;
use SimpleXMLElement;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Business\Process\EventInterface;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\Process\TransitionInterface;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\StateMachineConfig;

class Builder implements BuilderInterface
{
    /**
     * @var string
     */
    public const STATE_NAME_ATTRIBUTE = 'name';

    /**
     * @var string
     */
    public const STATE_DISPLAY_ATTRIBUTE = 'display';

    /**
     * @var string
     */
    public const PROCESS_NAME_ATTRIBUTE = 'name';

    /**
     * @var string
     */
    public const PROCESS_FILE_ATTRIBUTE = 'file';

    /**
     * @var string
     */
    public const PROCESS_MAIN_FLAG_ATTRIBUTE = 'main';

    /**
     * @var string
     */
    public const EVENT_COMMAND_ATTRIBUTE = 'command';

    /**
     * @var string
     */
    public const EVENT_MANUAL_ATTRIBUTE = 'manual';

    /**
     * @var string
     */
    public const EVENT_ON_ENTER_ATTRIBUTE = 'onEnter';

    /**
     * @var string
     */
    public const EVENT_TIMEOUT_ATTRIBUTE = 'timeout';

    /**
     * @var string
     */
    public const TRANSITION_CONDITION_ATTRIBUTE = 'condition';

    /**
     * @var string
     */
    public const TRANSITION_HAPPY_PATH_ATTRIBUTE = 'happy';

    /**
     * @var \SimpleXMLElement
     */
    protected $rootElement;

    /**
     * @var array<\StateMachine\Business\Process\ProcessInterface>
     */
    protected static $processBuffer = [];

    /**
     * @var \StateMachine\Business\Process\EventInterface
     */
    protected $event;

    /**
     * @var \StateMachine\Business\Process\StateInterface
     */
    protected $state;

    /**
     * @var \StateMachine\Business\Process\TransitionInterface
     */
    protected $transition;

    /**
     * @var \StateMachine\Business\Process\ProcessInterface
     */
    protected $process;

    /**
     * @var \StateMachine\StateMachineConfig
     */
    protected $stateMachineConfig;

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     * @param \StateMachine\Business\Process\StateInterface $state
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param \StateMachine\StateMachineConfig $stateMachineConfig
     */
    public function __construct(
        EventInterface $event,
        StateInterface $state,
        TransitionInterface $transition,
        ProcessInterface $process,
        StateMachineConfig $stateMachineConfig
    ) {
        $this->event = $event;
        $this->state = $state;
        $this->transition = $transition;
        $this->process = $process;
        $this->stateMachineConfig = $stateMachineConfig;
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function createProcess(ProcessDto $processDto): ProcessInterface
    {
        $processIdentifier = $this->createProcessIdentifier($processDto);
        if (isset(self::$processBuffer[$processIdentifier])) {
            return self::$processBuffer[$processIdentifier];
        }

        $pathToXml = $this->buildPathToXml($processDto);
        $this->rootElement = $this->loadXmlFromProcessName($pathToXml, $processDto->getProcessNameOrFail());

        $this->mergeSubProcessFiles($pathToXml);

        [$processMap, $mainProcess] = $this->createMainSubProcess();

        $stateToProcessMap = $this->createStates($processMap);

        $this->createSubProcesses($processMap);

        $eventMap = $this->createEvents();

        $this->createTransitions($stateToProcessMap, $processMap, $eventMap);

        self::$processBuffer[$processIdentifier] = $mainProcess;

        return $mainProcess;
    }

    /**
     * @param string $pathToXml
     *
     * @return void
     */
    protected function mergeSubProcessFiles(string $pathToXml): void
    {
        foreach ($this->rootElement->children() as $xmlProcess) {
            $processFile = $this->getAttributeString($xmlProcess, self::PROCESS_FILE_ATTRIBUTE);

            if ($processFile === null) {
                 continue;
            }

            $processName = $this->getAttributeString($xmlProcess, 'name');
            $processPrefix = $this->getAttributeString($xmlProcess, 'prefix');
            $xmlSubProcess = $this->loadXmlFromFileName($pathToXml, str_replace(' ', '_', $processFile));

            if ($processName) {
                $xmlSubProcess->children()->process[0]['name'] = $processName;
            }

            $this->recursiveMerge($xmlSubProcess, $this->rootElement, $processPrefix);
        }
    }

    /**
     * @param \SimpleXMLElement $fromXmlElement
     * @param \SimpleXMLElement $intoXmlNode
     * @param string|null $prefix
     *
     * @return void
     */
    protected function recursiveMerge(SimpleXMLElement $fromXmlElement, SimpleXMLElement $intoXmlNode, ?string $prefix = null): void
    {
        /** @var array<\SimpleXMLElement> $xmlElements */
        $xmlElements = $fromXmlElement->children();
        if (!$xmlElements) {
            return;
        }

        foreach ($xmlElements as $xmlElement) {
            $xmlElement = $this->prefixSubProcessElementValue($xmlElement, $prefix);
            $xmlElement = $this->prefixSubProcessElementAttributes($xmlElement, $prefix);

            $child = $intoXmlNode->addChild($xmlElement->getName(), $xmlElement);
            /** @var array<string> $attributes */
            $attributes = $xmlElement->attributes();
            foreach ($attributes as $name => $value) {
                $child->addAttribute($name, $value);
            }

            $this->recursiveMerge($xmlElement, $child, $prefix);
        }
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string|null $prefix
     *
     * @return \SimpleXMLElement
     */
    protected function prefixSubProcessElementValue(SimpleXMLElement $xmlElement, ?string $prefix = null): SimpleXMLElement
    {
        if ($prefix === null) {
            return $xmlElement;
        }

        $namespaceDependentElementNames = ['source', 'target', 'event'];

        if (in_array($xmlElement->getName(), $namespaceDependentElementNames, true)) {
            $xmlElement[0] = $prefix . $this->stateMachineConfig->getSubProcessPrefixDelimiter() . $xmlElement[0];
        }

        return $xmlElement;
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string|null $prefix
     *
     * @return \SimpleXMLElement
     */
    protected function prefixSubProcessElementAttributes(SimpleXMLElement $xmlElement, ?string $prefix = null): SimpleXMLElement
    {
        if ($prefix === null) {
            return $xmlElement;
        }

        $namespaceDependentElementNames = ['state', 'event'];

        if (in_array($xmlElement->getName(), $namespaceDependentElementNames, true)) {
            $xmlElement->attributes()['name'] = $prefix . $this->stateMachineConfig->getSubProcessPrefixDelimiter() . $xmlElement->attributes()['name'];
        }

        return $xmlElement;
    }

    /**
     * @param string $pathToXml
     * @param string $fileName
     *
     * @throws \RuntimeException
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \SimpleXMLElement
     */
    protected function loadXmlFromFileName(string $pathToXml, string $fileName): SimpleXMLElement
    {
        $pathToXml = $pathToXml . $fileName . '.xml';

        if (!file_exists($pathToXml)) {
            throw new StateMachineException(
                sprintf(
                    'State machine XML file not found in `%s`.',
                    $pathToXml,
                ),
            );
        }

        $xmlContents = file_get_contents($pathToXml);
        if ($xmlContents === false) {
            throw new RuntimeException('Loading of file failed: ' . $pathToXml);
        }

        return $this->loadXml($xmlContents);
    }

    /**
     * @param string $pathToXml
     * @param string $processName
     *
     * @return \SimpleXMLElement
     */
    protected function loadXmlFromProcessName(string $pathToXml, string $processName): SimpleXMLElement
    {
        return $this->loadXmlFromFileName($pathToXml, $processName);
    }

    /**
     * @param string $xml
     *
     * @return \SimpleXMLElement
     */
    protected function loadXml(string $xml): SimpleXMLElement
    {
        return new SimpleXMLElement($xml);
    }

    /**
     * @return array
     */
    protected function createEvents(): array
    {
        $eventMap = [];
        foreach ($this->rootElement as $xmlProcess) {
            if (!isset($xmlProcess->events)) {
                continue;
            }

            $xmlEvents = $xmlProcess->events->children();
            foreach ($xmlEvents as $xmlEvent) {
                $event = $this->createEvent($xmlEvent);
                if ($event === null) {
                    continue;
                }

                $eventMap[$event->getName()] = $event;
            }
        }

        return $eventMap;
    }

    /**
     * @param \SimpleXMLElement $xmlEvent
     *
     * @return \StateMachine\Business\Process\EventInterface|null
     */
    protected function createEvent(SimpleXMLElement $xmlEvent): ?EventInterface
    {
        $eventName = $this->getAttributeString($xmlEvent, self::STATE_NAME_ATTRIBUTE);
        if ($eventName === null) {
            return null;
        }

        $event = clone $this->event;
        $command = $this->getAttributeString($xmlEvent, self::EVENT_COMMAND_ATTRIBUTE);
        if ($command) {
            $event->setCommand($command);
        }
        $timeout = $this->getAttributeString($xmlEvent, self::EVENT_TIMEOUT_ATTRIBUTE);
        if ($timeout) {
            $event->setTimeout($timeout);
        }
        $event->setManual($this->getAttributeBoolean($xmlEvent, self::EVENT_MANUAL_ATTRIBUTE));
        $event->setOnEnter($this->getAttributeBoolean($xmlEvent, self::EVENT_ON_ENTER_ATTRIBUTE));
        $event->setName($eventName);

        return $event;
    }

    /**
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return array
     */
    protected function createMainSubProcess(): array
    {
        $mainProcess = null;
        $xmlProcesses = $this->rootElement->children();
        $processMap = [];

        /** @var \SimpleXMLElement $xmlProcess */
        foreach ($xmlProcesses as $xmlProcess) {
            $process = clone $this->process;
            $processName = $this->getAttributeString($xmlProcess, self::STATE_NAME_ATTRIBUTE);
            if (!$processName) {
                throw new StateMachineException('No process name found in ' . $xmlProcess);
            }
            $process->setName($processName);
            $processMap[$processName] = $process;
            $process->setIsMain($this->getAttributeBoolean($xmlProcess, self::PROCESS_MAIN_FLAG_ATTRIBUTE));
            $file = $this->getAttributeString($xmlProcess, self::PROCESS_FILE_ATTRIBUTE);
            if ($file) {
                $process->setFile($file);
            }

            if ($process->getIsMain()) {
                $mainProcess = $process;
            }
        }

        if ($mainProcess === null) {
            throw new StateMachineException('Main process could not be created.');
        }

        return [$processMap, $mainProcess];
    }

    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $processMap
     *
     * @return void
     */
    protected function createSubProcesses(array $processMap): void
    {
        foreach ($this->rootElement as $xmlProcess) {
            $processName = $this->getAttributeString($xmlProcess, self::PROCESS_NAME_ATTRIBUTE);

            $process = $processMap[$processName];

            if (!empty($xmlProcess->subprocesses)) {
                $xmlSubProcesses = $xmlProcess->subprocesses->children();

                foreach ($xmlSubProcesses as $xmlSubProcess) {
                    $subProcessName = (string)$xmlSubProcess;
                    $subProcess = $processMap[$subProcessName];
                    $process->addSubProcess($subProcess);
                }
            }
        }
    }

    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $processMap
     *
     * @return array<\StateMachine\Business\Process\ProcessInterface>
     */
    protected function createStates(array $processMap): array
    {
        $stateToProcessMap = [];

        $xmlProcesses = $this->rootElement->children();
        foreach ($xmlProcesses as $xmlProcess) {
            $processName = $this->getAttributeString($xmlProcess, self::PROCESS_NAME_ATTRIBUTE);
            $process = $processMap[$processName];

            if (empty($xmlProcess->states)) {
                continue;
            }

            $xmlStates = $xmlProcess->states->children();

            foreach ($xmlStates as $xmlState) {
                $state = $this->createState($xmlState, $process);

                $process->addState($state);
                $stateToProcessMap[$state->getName()] = $process;
            }
        }

        return $stateToProcessMap;
    }

    /**
     * @param \SimpleXMLElement $xmlState
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function createState(SimpleXMLElement $xmlState, ProcessInterface $process): StateInterface
    {
        $state = clone $this->state;
        $name = $this->getAttributeString($xmlState, self::STATE_NAME_ATTRIBUTE);
        if (!$name) {
            throw new StateMachineException('No name given for XML of ' . $process->getName());
        }

        $state->setName($name);
        $display = $this->getAttributeString($xmlState, self::STATE_DISPLAY_ATTRIBUTE);
        if ($display) {
            $state->setDisplay($display);
        }
        $state->setProcess($process);
        $state = $this->addFlags($xmlState, $state);

        return $state;
    }

    /**
     * @param \SimpleXMLElement $xmlState
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function addFlags(SimpleXMLElement $xmlState, StateInterface $state): StateInterface
    {
        /** @var \SimpleXMLElement|null $flagElement */
        $flagElement = $xmlState->flag;
        if (!$flagElement) {
            return $state;
        }

        $flags = $xmlState->children();
        foreach ($flags as $flag) {
            $state->addFlag((string)$flag);
        }

        return $state;
    }

    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $stateToProcessMap
     * @param array<\StateMachine\Business\Process\ProcessInterface> $processMap
     * @param array<\StateMachine\Business\Process\EventInterface> $eventMap
     *
     * @return void
     */
    protected function createTransitions(array $stateToProcessMap, array $processMap, array $eventMap): void
    {
        /** @var \SimpleXMLElement $xmlProcess */
        foreach ($this->rootElement as $xmlProcess) {
            if (empty($xmlProcess->transitions)) {
                continue;
            }

            $processName = $this->getAttributeString($xmlProcess, self::STATE_NAME_ATTRIBUTE);
            $xmlTransitions = $xmlProcess->transitions->children();
            foreach ($xmlTransitions as $xmlTransition) {
                $transition = $this->createTransition($stateToProcessMap, $eventMap, $xmlTransition);
                $processMap[$processName]->addTransition($transition);
            }
        }
    }

    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $stateToProcessMap
     * @param array<\StateMachine\Business\Process\EventInterface> $eventMap
     * @param \SimpleXMLElement $xmlTransition
     *
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    protected function createTransition(array $stateToProcessMap, array $eventMap, SimpleXMLElement $xmlTransition): TransitionInterface
    {
        $transition = clone $this->transition;

        $condition = $this->getAttributeString($xmlTransition, self::TRANSITION_CONDITION_ATTRIBUTE);
        if ($condition) {
            $transition->setCondition($condition);
        }

        $transition->setHappyCase(
            $this->getAttributeBoolean(
                $xmlTransition,
                self::TRANSITION_HAPPY_PATH_ATTRIBUTE,
            ),
        );

        $sourceState = (string)$xmlTransition->source;

        $this->setTransitionSource($stateToProcessMap, $sourceState, $transition);
        $this->setTransitionTarget($stateToProcessMap, $xmlTransition, $sourceState, $transition);
        $this->setTransitionEvent($eventMap, $xmlTransition, $sourceState, $transition);

        return $transition;
    }

    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $stateToProcessMap
     * @param string $sourceName
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @throws \StateMachineException
     *
     * @return void
     */
    protected function setTransitionSource(
        array $stateToProcessMap,
        string $sourceName,
        TransitionInterface $transition
    ): void {
        $sourceProcess = $stateToProcessMap[$sourceName] ?? null;
        if (!$sourceProcess) {
            throw new StateMachineException(
                sprintf(
                    'No Process found for source `%s` Is the transition properly defined in XML definition file?',
                    $sourceName,
                ),
            );
        }
        $sourceState = $sourceProcess->getState($sourceName);
        $transition->setSourceState($sourceState);
        $sourceState->addOutgoingTransition($transition);
    }

    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $stateToProcessMap
     * @param \SimpleXMLElement $xmlTransition
     * @param string $sourceName
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function setTransitionTarget(
        array $stateToProcessMap,
        SimpleXMLElement $xmlTransition,
        string $sourceName,
        TransitionInterface $transition
    ): void {
        $targetStateName = (string)$xmlTransition->target;
        if (!isset($stateToProcessMap[$targetStateName])) {
            throw new StateMachineException(
                sprintf(
                    'Target `%s` does not exist from source `%s`',
                    $targetStateName,
                    $sourceName,
                ),
            );
        }

        $targetProcess = $stateToProcessMap[$targetStateName];
        $targetState = $targetProcess->getState($targetStateName);
        $transition->setTargetState($targetState);
        $targetState->addIncomingTransition($transition);
    }

    /**
     * @param array<\StateMachine\Business\Process\EventInterface> $eventMap
     * @param \SimpleXMLElement $xmlTransition
     * @param string $sourceState
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    protected function setTransitionEvent(array $eventMap, SimpleXMLElement $xmlTransition, string $sourceState, TransitionInterface $transition): void
    {
        if (isset($xmlTransition->event)) {
            $eventName = (string)$xmlTransition->event;

            $this->assertEventExists($eventMap, $sourceState, $eventName);

            $event = $eventMap[$eventName];
            $event->addTransition($transition);
            $transition->setEvent($event);
        }
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string $attributeName
     *
     * @return string|null
     */
    protected function getAttributeString(SimpleXMLElement $xmlElement, string $attributeName): ?string
    {
        $string = (string)$xmlElement->attributes()[$attributeName];
        $string = ($string === '') ? null : $string;

        return $string;
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string $attributeName
     *
     * @return bool
     */
    protected function getAttributeBoolean(SimpleXMLElement $xmlElement, string $attributeName): bool
    {
        return (string)$xmlElement->attributes()[$attributeName] === 'true';
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return string
     */
    protected function createProcessIdentifier(ProcessDto $processDto): string
    {
        return $processDto->getStateMachineNameOrFail() . '-' . $processDto->getProcessNameOrFail();
    }

    /**
     * @param \StateMachine\Dto\StateMachine\ProcessDto $processDto
     *
     * @return string
     */
    protected function buildPathToXml(ProcessDto $processDto): string
    {
        return $this->stateMachineConfig->getPathToStateMachineXmlFiles() . $processDto->getStateMachineNameOrFail() . DS;
    }

    /**
     * @param array $eventMap
     * @param string $sourceName
     * @param string $eventName
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function assertEventExists(array $eventMap, string $sourceName, string $eventName): void
    {
        if (!isset($eventMap[$eventName])) {
            throw new StateMachineException(
                sprintf(
                    'Event `%s` does not exist from source `%s`.',
                    $eventName,
                    $sourceName,
                ),
            );
        }
    }
}
