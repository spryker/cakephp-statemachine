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
    public function hasOutgoingTransitions();

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getOutgoingTransitionsByEvent(EventInterface $event);

    /**
     * @return \StateMachine\Business\Process\EventInterface[]
     */
    public function getEvents();

    /**
     * @param string $eventName
     *
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getEvent($eventName);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasEvent($id);

    /**
     * @return bool
     */
    public function hasAnyEvent();

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addIncomingTransition(TransitionInterface $transition);

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addOutgoingTransition(TransitionInterface $transition);

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return $this
     */
    public function setProcess($process);

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    public function getProcess();

    /**
     * @return bool
     */
    public function hasOnEnterEvent();

    /**
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getOnEnterEvent();

    /**
     * @return bool
     */
    public function hasTimeoutEvent();

    /**
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\EventInterface[]
     */
    public function getTimeoutEvents();

    /**
     * @param string $flag
     *
     * @return $this
     */
    public function addFlag($flag);

    /**
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag($flag);

    /**
     * @return bool
     */
    public function hasFlags();

    /**
     * @return array
     */
    public function getFlags();

    /**
     * @return string
     */
    public function getDisplay();

    /**
     * @param string $display
     *
     * @return $this
     */
    public function setDisplay($display);
}
