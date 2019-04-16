<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

interface ProcessInterface
{
    /**
     * @param \StateMachine\Business\Process\ProcessInterface[] $subProcesses
     *
     * @return void
     */
    public function setSubProcesses($subProcesses);

    /**
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function getSubProcesses();

    /**
     * @return bool
     */
    public function hasSubProcesses();

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $subProcess
     *
     * @return void
     */
    public function addSubProcess(ProcessInterface $subProcess);

    /**
     * @param bool $isMain
     *
     * @return void
     */
    public function setIsMain($isMain);

    /**
     * @return bool
     */
    public function getIsMain();

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param \StateMachine\Business\Process\StateInterface[] $states
     *
     * @return void
     */
    public function setStates($states);

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return void
     */
    public function addState(StateInterface $state);

    /**
     * @param string $stateId
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getState($stateId);

    /**
     * @param string $stateId
     *
     * @return bool
     */
    public function hasState($stateId);

    /**
     * @param string $stateName
     *
     * @throws \Exception
     *
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getStateFromAllProcesses($stateName);

    /**
     * @return \StateMachine\Business\Process\StateInterface[]
     */
    public function getStates();

    /**
     * @return bool
     */
    public function hasStates();

    /**
     * @param \StateMachine\Business\Process\TransitionInterface $transition
     *
     * @return void
     */
    public function addTransition(TransitionInterface $transition);

    /**
     * @param \StateMachine\Business\Process\TransitionInterface[] $transitions
     *
     * @return void
     */
    public function setTransitions($transitions);

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getTransitions();

    /**
     * @return bool
     */
    public function hasTransitions();

    /**
     * @return \StateMachine\Business\Process\StateInterface[]
     */
    public function getAllStates();

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getAllTransitions();

    /**
     * @return \StateMachine\Business\Process\TransitionInterface[]
     */
    public function getAllTransitionsWithoutEvent();

    /**
     * @return \StateMachine\Business\Process\EventInterface[]
     */
    public function getManuallyExecutableEvents();

    /**
     * @return string[][]
     */
    public function getManuallyExecutableEventsBySource();

    /**
     * @return \StateMachine\Business\Process\ProcessInterface[]
     */
    public function getAllProcesses();

    /**
     * @param string $file
     *
     * @return void
     */
    public function setFile($file);

    /**
     * @return bool
     */
    public function hasFile();

    /**
     * @return string
     */
    public function getFile();
}
