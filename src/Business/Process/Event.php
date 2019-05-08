<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

class Event implements EventInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var \StateMachine\Business\Process\TransitionInterface[]
     */
    protected $transitions = [];

    /**
     * @var bool
     */
    protected $onEnter = false;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $timeout;

    /**
     * @var bool
     */
    protected $manual = false;

    /**
     * @param bool $manual
     *
     * @return void
     */
    public function setManual(bool $manual): void
    {
        $this->manual = $manual;
    }

    /**
     * @return bool
     */
    public function isManual(): bool
    {
        return $this->manual;
    }

    /**
     * @param string $command
     *
     * @return void
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return bool
     */
    public function hasCommand(): bool
    {
        return isset($this->command);
    }

    /**
     * @param bool $onEnter
     *
     * @return void
     */
    public function setOnEnter(bool $onEnter): void
    {
        $this->onEnter = $onEnter;
    }

    /**
     * @return bool
     */
    public function isOnEnter(): bool
    {
        return $this->onEnter;
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function setName(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->id;
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
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     *
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getTransitionsBySource(StateInterface $sourceState): array
    {
        $transitions = [];

        foreach ($this->transitions as $transition) {
            if ($transition->getSourceState()->getName() !== $sourceState->getName()) {
                continue;
            }
            $transitions[] = $transition;
        }

        return $transitions;
    }

    /**
     * @return string
     */
    public function getEventTypeLabel(): string
    {
        if ($this->isOnEnter()) {
            return ' (on enter)';
        }

        if ($this->hasTimeout()) {
            return ' (timeout)';
        }

        if ($this->isManual()) {
            return ' (manual)';
        }

        return '';
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * @param string $timeout
     *
     * @return void
     */
    public function setTimeout(string $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getTimeout(): string
    {
        return $this->timeout;
    }

    /**
     * @return bool
     */
    public function hasTimeout(): bool
    {
        return isset($this->timeout);
    }
}
