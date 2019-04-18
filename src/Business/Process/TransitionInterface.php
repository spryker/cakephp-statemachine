<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

interface TransitionInterface
{
    /**
     * @param bool $happy
     *
     * @return void
     */
    public function setHappyCase(bool $happy): void;

    /**
     * @return bool
     */
    public function isHappyCase(): bool;

    /**
     * @param string $condition
     *
     * @return void
     */
    public function setCondition(string $condition): void;

    /**
     * @return string
     */
    public function getCondition(): string;

    /**
     * @return bool
     */
    public function hasCondition(): bool;

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return void
     */
    public function setEvent(EventInterface $event): void;

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getEvent(): EventInterface;

    /**
     * @return bool
     */
    public function hasEvent(): bool;

    /**
     * @param \StateMachine\Business\Process\StateInterface $source
     *
     * @return void
     */
    public function setSourceState(StateInterface $source): void;

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getSourceState(): StateInterface;

    /**
     * @param \StateMachine\Business\Process\StateInterface $target
     *
     * @return void
     */
    public function setTargetState(StateInterface $target): void;

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getTargetState(): StateInterface;
}
