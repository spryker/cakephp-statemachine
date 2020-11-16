<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Business\Process;

class Transition implements TransitionInterface
{
    /**
     * @var \StateMachine\Business\Process\EventInterface
     */
    protected $event;

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var bool
     */
    protected $happy;

    /**
     * @var \StateMachine\Business\Process\StateInterface
     */
    protected $source;

    /**
     * @var \StateMachine\Business\Process\StateInterface
     */
    protected $target;

    /**
     * @param bool $happy
     *
     * @return void
     */
    public function setHappyCase(bool $happy): void
    {
        $this->happy = $happy;
    }

    /**
     * @return bool
     */
    public function isHappyCase(): bool
    {
        return $this->happy;
    }

    /**
     * @param string $condition
     *
     * @return void
     */
    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return bool
     */
    public function hasCondition(): bool
    {
        return isset($this->condition);
    }

    /**
     * @param \StateMachine\Business\Process\EventInterface $event
     *
     * @return void
     */
    public function setEvent(EventInterface $event): void
    {
        $this->event = $event;
    }

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    public function getEvent(): EventInterface
    {
        return $this->event;
    }

    /**
     * @return bool
     */
    public function hasEvent(): bool
    {
        return isset($this->event);
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $source
     *
     * @return void
     */
    public function setSourceState(StateInterface $source): void
    {
        $this->source = $source;
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getSourceState(): StateInterface
    {
        return $this->source;
    }

    /**
     * @param \StateMachine\Business\Process\StateInterface $target
     *
     * @return void
     */
    public function setTargetState(StateInterface $target): void
    {
        $this->target = $target;
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    public function getTargetState(): StateInterface
    {
        return $this->target;
    }
}
