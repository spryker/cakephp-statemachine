<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

interface ProcessInterface
{
    /**
     * @param array<\StateMachine\Business\Process\ProcessInterface> $subProcesses
     *
     * @return void
     */
    public function setSubProcesses(array $subProcesses): void;

    /**
     * @return array<\StateMachine\Business\Process\ProcessInterface>
     */
    public function getSubProcesses(): array;

    /**
     * @return bool
     */
    public function hasSubProcesses(): bool;

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $subProcess
     *
     * @return void
     */
    public function addSubProcess(ProcessInterface $subProcess): void;

    /**
     * @param bool $isMain
     *
     * @return void
     */
    public function setIsMain(bool $isMain): void;

    /**
     * @return bool
     */
    public function getIsMain(): bool;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param array<\StateMachine\Business\Process\StateInterface> $states
     *
     * @return void
     */
    public function setStates(array $states): void;

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return void
     */
    public function addState(StateInterface $state): void;

    /**
     * @param string $stateId
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getState(string $stateId): StateInterface;

    /**
     * @param string $stateId
     *
     * @return bool
     */
    public function hasState(string $stateId): bool;

    /**
     * @param string $stateName
     *
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getStateFromAllProcesses(string $stateName): StateInterface;

    /**
     * @return array<\StateMachine\Business\Process\StateInterface>
     */
    public function getStates(): array;

    /**
     * @return bool
     */
    public function hasStates(): bool;

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addTransition(TransitionInterface $transition): void;

    /**
     * @param array<\StateMachine\Business\Process\TransitionInterface> $transitions
     *
     * @return void
     */
    public function setTransitions(array $transitions): void;

    /**
     * @return array<\StateMachine\Business\Process\TransitionInterface>
     */
    public function getTransitions(): array;

    /**
     * @return bool
     */
    public function hasTransitions(): bool;

    /**
     * @return array<\StateMachine\Business\Process\StateInterface>
     */
    public function getAllStates(): array;

    /**
     * @return array<\StateMachine\Business\Process\TransitionInterface>
     */
    public function getAllTransitions(): array;

    /**
     * @return array<\StateMachine\Business\Process\TransitionInterface>
     */
    public function getAllTransitionsWithoutEvent(): array;

    /**
     * @return array<\StateMachine\Business\Process\EventInterface>
     */
    public function getManuallyExecutableEvents(): array;

    /**
     * @return array<array<string>>
     */
    public function getManuallyExecutableEventsBySource(): array;

    /**
     * @return array<\StateMachine\Business\Process\ProcessInterface>
     */
    public function getAllProcesses(): array;

    /**
     * @param string $file
     *
     * @return void
     */
    public function setFile(string $file): void;

    /**
     * @return bool
     */
    public function hasFile(): bool;

    /**
     * @return string
     */
    public function getFile(): string;
}
