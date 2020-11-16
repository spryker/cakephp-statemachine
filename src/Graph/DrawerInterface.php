<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\StateInterface;

interface DrawerInterface
{
    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string|null $highlightState
     * @param string|null $format
     * @param int|null $fontSize
     *
     * @return string
     */
    public function draw(ProcessInterface $process, ?string $highlightState = null, ?string $format = null, ?int $fontSize = null): string;

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     * @param string|null $highlightState
     *
     * @return void
     */
    public function drawStates(ProcessInterface $process, ?string $highlightState = null): void;

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return void
     */
    public function drawTransitions(ProcessInterface $process): void;

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return void
     */
    public function drawTransitionsEvents(StateInterface $state): void;

    /**
     * @param \StateMachine\Business\Process\StateInterface $state
     *
     * @return void
     */
    public function drawTransitionsConditions(StateInterface $state): void;

    /**
     * @param \StateMachine\Business\Process\ProcessInterface $process
     *
     * @return void
     */
    public function drawClusters(ProcessInterface $process): void;
}
