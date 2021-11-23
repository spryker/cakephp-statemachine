<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

use StateMachine\Business\Exception\StateMachineException;

class State implements StateInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $display;

    /**
     * @var \StateMachine\Business\Process\ProcessInterface
     */
    protected $process;

    /**
     * @var array
     */
    protected $flags = [];

    /**
     * @var array<\StateMachine\Business\Process\TransitionInterface>
     */
    protected $outgoingTransitions = [];

    /**
     * @var array<\StateMachine\Business\Process\TransitionInterface>
     */
    protected $incomingTransitions = [];

    /**
     * @param array<\StateMachine\Business\Process\TransitionInterface> $incomingTransitions
     *
     * @return $this
     */
    public function setIncomingTransitions(array $incomingTransitions)
    {
        $this->incomingTransitions = $incomingTransitions;

        return $this;
    }

    /**
     * @return array<\StateMachine\Business\Process\TransitionInterface>
     */
    public function getIncomingTransitions(): array
    {
        return $this->incomingTransitions;
    }

    /**
     * @return bool
     */
    public function hasIncomingTransitions(): bool
    {
        return (bool)$this->incomingTransitions;
    }

    /**
     * @param array<\StateMachine\Business\Process\TransitionInterface> $outgoingTransitions
     *
     * @return $this
     */
    public function setOutgoingTransitions(array $outgoingTransitions)
    {
        $this->outgoingTransitions = $outgoingTransitions;

        return $this;
    }

    /**
     * @return array<\StateMachine\Business\Process\TransitionInterface>
     */
    public function getOutgoingTransitions(): array
    {
        return $this->outgoingTransitions;
    }

    /**
     * @return bool
     */
    public function hasOutgoingTransitions(): bool
    {
        return (bool)$this->outgoingTransitions;
    }

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return array<\StateMachine\Business\Process\TransitionInterface>
     */
    public function getOutgoingTransitionsByEvent(EventInterface $event): array
    {
        $transitions = [];
        foreach ($this->outgoingTransitions as $transition) {
            if ($transition->hasEvent()) {
                if ($transition->getEvent()->getName() === $event->getName()) {
                    $transitions[] = $transition;
                }
            }
        }

        return $transitions;
    }

    /**
     * @return array<\StateMachine\Business\Process\EventInterface>
     */
    public function getEvents(): array
    {
        $events = [];
        foreach ($this->outgoingTransitions as $transition) {
            if ($transition->hasEvent()) {
                $events[$transition->getEvent()->getName()] = $transition->getEvent();
            }
        }

        return $events;
    }

    /**
     * @param string $eventName
     *
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getEvent(string $eventName): EventInterface
    {
        foreach ($this->outgoingTransitions as $transition) {
            if ($transition->hasEvent()) {
                $event = $transition->getEvent();
                if ($event->getName() === $eventName) {
                    return $event;
                }
            }
        }

        throw new StateMachineException(
            sprintf(
                'Event "%d" not found. Have you added this event to transition?',
                $eventName,
            ),
        );
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasEvent(string $id): bool
    {
        foreach ($this->outgoingTransitions as $transition) {
            if ($transition->hasEvent()) {
                $event = $transition->getEvent();
                if ($event->getName() === $id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasAnyEvent(): bool
    {
        foreach ($this->outgoingTransitions as $transition) {
            if ($transition->hasEvent()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addIncomingTransition(TransitionInterface $transition): void
    {
        $this->incomingTransitions[] = $transition;
    }

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addOutgoingTransition(TransitionInterface $transition): void
    {
        $this->outgoingTransitions[] = $transition;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return $this
     */
    public function setProcess(ProcessInterface $process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function getProcess(): ProcessInterface
    {
        return $this->process;
    }

    /**
     * @return bool
     */
    public function hasOnEnterEvent(): bool
    {
        $transitions = $this->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                if ($transition->getEvent()->isOnEnter() === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws \StateMachine\Business\Exception\StateMachineException
     *
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getOnEnterEvent(): EventInterface
    {
        $transitions = $this->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                if ($transition->getEvent()->isOnEnter() === true) {
                    return $transition->getEvent();
                }
            }
        }

        throw new StateMachineException(
            sprintf(
                'There is no `onEnter` event for state `%s`',
                $this->getName(),
            ),
        );
    }

    /**
     * @return bool
     */
    public function hasTimeoutEvent(): bool
    {
        $transitions = $this->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                if ($transition->getEvent()->hasTimeout() === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<\StateMachine\Business\Process\EventInterface>
     */
    public function getTimeoutEvents(): array
    {
        $events = [];

        $transitions = $this->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                if ($transition->getEvent()->hasTimeout() === true) {
                    $events[] = $transition->getEvent();
                }
            }
        }

        return $events;
    }

    /**
     * @param string $flag
     *
     * @return $this
     */
    public function addFlag(string $flag)
    {
        $this->flags[] = $flag;

        return $this;
    }

    /**
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }

    /**
     * @return bool
     */
    public function hasFlags(): bool
    {
        return count($this->flags) > 0;
    }

    /**
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @return bool
     */
    public function hasDisplay(): bool
    {
        return $this->display !== null;
    }

    /**
     * @return string
     */
    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * @param string $display
     *
     * @return $this
     */
    public function setDisplay(string $display)
    {
        $this->display = $display;

        return $this;
    }
}
