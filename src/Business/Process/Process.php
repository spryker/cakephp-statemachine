<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

use StateMachine\Business\Exception\StateMachineException;

class Process implements ProcessInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \StateMachine\Business\Process\StateInterface[]
     */
    protected $states = [];

    /**
     * @var \StateMachine\Business\Process\TransitionInterface[]
     */
    protected $transitions = [];

    /**
     * @var bool
     */
    protected $isMain = false;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var \StateMachine\Business\Process\ProcessInterface[]
     */
    protected $subProcesses = [];

    /**
     * @param \StateMachine\Business\Process\ProcessInterface[] $subProcesses
     *
     * @return void
     */
    public function setSubProcesses($subProcesses): void
    {
        $this->subProcesses = $subProcesses;
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function getSubProcesses(): array
    {
        return $this->subProcesses;
    }

    /**
     * @return bool
     */
    public function hasSubProcesses(): bool
    {
        return count($this->subProcesses) > 0;
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $subProcess
     *
     * @return void
     */
    public function addSubProcess(ProcessInterface $subProcess): void
    {
        $this->subProcesses[] = $subProcess;
    }

    /**
     * @param bool $isMain
     *
     * @return void
     */
    public function setIsMain(bool $isMain): void
    {
        $this->isMain = $isMain;
    }

    /**
     * @return bool
     */
    public function getIsMain(): bool
    {
        return $this->isMain;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface[] $states
     *
     * @return void
     */
    public function setStates($states): void
    {
        $this->states = $states;
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return void
     */
    public function addState(StateInterface $state): void
    {
        $this->states[$state->getName()] = $state;
    }

    /**
     * @param string $stateId
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getState(string $stateId): StateInterface
    {
        return $this->states[$stateId];
    }

    /**
     * @param string $stateId
     *
     * @return bool
     */
    public function hasState(string $stateId): bool
    {
        return array_key_exists($stateId, $this->states);
    }

    /**
     * @param string $stateName
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getStateFromAllProcesses(string $stateName): StateInterface
    {
        $processes = $this->getAllProcesses();
        foreach ($processes as $process) {
            if ($process->hasState($stateName)) {
                return $process->getState($stateName);
            }
        }

        throw new StateMachineException(
            sprintf(
                'State "%s" not found in any of state machine processes. Is state defined in xml definition file?',
                $stateName
            )
        );
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface[]
     */
    public function getStates(): array
    {
        return $this->states;
    }

    /**
     * @return bool
     */
    public function hasStates(): bool
    {
        return (bool)$this->states;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addTransition(TransitionInterface $transition): void
    {
        $this->transitions[] = $transition;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface[] $transitions
     *
     * @return void
     */
    public function setTransitions($transitions): void
    {
        $this->transitions = $transitions;
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * @return bool
     */
    public function hasTransitions(): bool
    {
        return (bool)$this->transitions;
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface[]
     */
    public function getAllStates(): array
    {
        $states = [];
        if ($this->hasStates()) {
            $states = $this->getStates();
        }

        if (!$this->hasSubProcesses()) {
            return $states;
        }

        foreach ($this->getSubProcesses() as $subProcess) {
            if (!$subProcess->hasStates()) {
                continue;
            }
            $states = array_merge($states, $subProcess->getStates());
        }

        return $states;
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getAllTransitions(): array
    {
        $transitions = [];
        if ($this->hasTransitions()) {
            $transitions = $this->getTransitions();
        }
        foreach ($this->getSubProcesses() as $subProcess) {
            if ($subProcess->hasTransitions()) {
                $transitions = array_merge($transitions, $subProcess->getTransitions());
            }
        }

        return $transitions;
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getAllTransitionsWithoutEvent(): array
    {
        $transitions = [];
        $allTransitions = $this->getAllTransitions();
        foreach ($allTransitions as $transition) {
            if ($transition->hasEvent() === true) {
                continue;
            }
            $transitions[] = $transition;
        }

        return $transitions;
    }

    /**
     * Gets all "manual" and "on enter" events as manually executable ones.
     *
     * @return \StateMachine\Business\Process\EventInterface[]
     */
    public function getManuallyExecutableEvents(): array
    {
        $manuallyExecutableEventList = [];
        $transitions = $this->getAllTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                $event = $transition->getEvent();
                if ($event->isManual() || $event->isOnEnter()) {
                    $manuallyExecutableEventList[] = $event;
                }
            }
        }

        return $manuallyExecutableEventList;
    }

    /**
     * @return string[][]
     */
    public function getManuallyExecutableEventsBySource(): array
    {
        $events = $this->getManuallyExecutableEvents();

        $eventsBySource = [];
        foreach ($events as $event) {
            $transitions = $event->getTransitions();
            $eventsBySource = $this->groupTransitionsBySourceName(
                $transitions,
                $eventsBySource,
                $event
            );
        }

        return $eventsBySource;
    }

    /**
     * @param array $transitions
     * @param array $eventsBySource
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return array
     */
    protected function groupTransitionsBySourceName(array $transitions, array $eventsBySource, EventInterface $event): array
    {
        foreach ($transitions as $transition) {
            $sourceName = $transition->getSourceState()->getName();
            if (!isset($eventsBySource[$sourceName])) {
                $eventsBySource[$sourceName] = [];
            }
            if (!in_array($event->getName(), $eventsBySource[$sourceName], true)) {
                $eventsBySource[$sourceName][] = $event->getName();
            }
        }

        return $eventsBySource;
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function getAllProcesses(): array
    {
        $processes = [];
        $processes[] = $this;
        $processes = array_merge($processes, $this->getSubProcesses());

        return $processes;
    }

    /**
     * @param string $file
     *
     * @return void
     */
    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return bool
     */
    public function hasFile(): bool
    {
        return $this->file !== null;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
