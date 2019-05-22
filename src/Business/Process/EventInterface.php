<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

interface EventInterface
{
    /**
     * @param bool $manual
     *
     * @return void
     */
    public function setManual(bool $manual): void;

    /**
     * @return bool
     */
    public function isManual(): bool;

    /**
     * @param string $command
     *
     * @return void
     */
    public function setCommand(string $command): void;

    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @return bool
     */
    public function hasCommand(): bool;

    /**
     * @param bool $onEnter
     *
     * @return void
     */
    public function setOnEnter(bool $onEnter): void;

    /**
     * @return bool
     */
    public function isOnEnter(): bool;

    /**
     * @param string $id
     *
     * @return void
     */
    public function setName(string $id): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getEventTypeLabel(): string;

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addTransition(TransitionInterface $transition): void;

    /**
     * @param \StateMachine\Business\Process\StateInterface $sourceState
     *
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getTransitionsBySource(StateInterface $sourceState): array;

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getTransitions(): array;

    /**
     * @param string $timeout
     *
     * @return void
     */
    public function setTimeout(string $timeout): void;

    /**
     * @return string
     */
    public function getTimeout(): string;

    /**
     * @return bool
     */
    public function hasTimeout(): bool;
}
