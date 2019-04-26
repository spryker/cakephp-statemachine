<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

interface StateInterface
{
    /**
     * @param \StateMachine\Business\Process\TransitionInterface[] $incomingTransitions
     *
     * @return $this
     */
    public function setIncomingTransitions(array $incomingTransitions);

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getIncomingTransitions(): array;

    /**
     * @return bool
     */
    public function hasIncomingTransitions(): bool;

    /**
     * @param \StateMachine\Business\Process\TransitionInterface[] $outgoingTransitions
     *
     * @return $this
     */
    public function setOutgoingTransitions(array $outgoingTransitions);

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getOutgoingTransitions();

    /**
     * @return bool
     */
    public function hasOutgoingTransitions(): bool;

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getOutgoingTransitionsByEvent(EventInterface $event): array;

    /**
     * @return \StateMachine\Business\Process\EventInterface[]
     */
    public function getEvents(): array;

    /**
     * @param string $eventName
     *
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getEvent(string $eventName): EventInterface;

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasEvent(string $id): bool;

    /**
     * @return bool
     */
    public function hasAnyEvent(): bool;

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addIncomingTransition(TransitionInterface $transition): void;

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addOutgoingTransition(TransitionInterface $transition): void;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return $this
     */
    public function setProcess(ProcessInterface $process);

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function getProcess(): ProcessInterface;

    /**
     * @return bool
     */
    public function hasOnEnterEvent(): bool;

    /**
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getOnEnterEvent(): EventInterface;

    /**
     * @return bool
     */
    public function hasTimeoutEvent(): bool;

    /**
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\EventInterface[]
     */
    public function getTimeoutEvents(): array;

    /**
     * @param string $flag
     *
     * @return $this
     */
    public function addFlag(string $flag);

    /**
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool;

    /**
     * @return bool
     */
    public function hasFlags(): bool;

    /**
     * @return array
     */
    public function getFlags(): array;

    /**
     * @return bool
     */
    public function hasDisplay(): bool;

    /**
     * @return string
     */
    public function getDisplay(): string;

    /**
     * @param string $display
     *
     * @return $this
     */
    public function setDisplay(string $display);
}
