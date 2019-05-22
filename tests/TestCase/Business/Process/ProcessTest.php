<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Process;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\Transition;

class ProcessTest extends TestCase
{
    protected const EVENT_NAME_DEFAULT = 'default';
    protected const EVENT_NAME_MANUAL = 'manual';
    protected const EVENT_NAME_ONENTER = 'onenter';

    /**
     * @return void
     */
    public function testThatManualEventsDoNotIncludeOnEnterEvents(): void
    {
        $process = $this->createProcess();
        $process->setTransitions($this->getTransitionsWithManualAndOnEnterEvents());

        $result = $process->getManuallyExecutableEvents();
        $this->assertSame(1, count($result));

        $this->assertSame(static::EVENT_NAME_MANUAL, $result[0]->getName());
    }

    /**
     * @return array
     */
    protected function getTransitionsWithManualAndOnEnterEvents(): array
    {
        $transitions = [];

        $transition = new Transition();
        $event = new Event();
        $event->setName(static::EVENT_NAME_DEFAULT);
        $transition->setEvent($event);
        $transitions[] = $transition;

        $transition = new Transition();
        $event = new Event();
        $event->setName(static::EVENT_NAME_MANUAL);
        $event->setManual(true);
        $transition->setEvent($event);
        $transitions[] = $transition;

        $transition = new Transition();
        $event = new Event();
        $event->setName(static::EVENT_NAME_ONENTER);
        $event->setOnEnter(true);
        $transition->setEvent($event);
        $transitions[] = $transition;

        return $transitions;
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    protected function createProcess(): ProcessInterface
    {
        return new Process();
    }
}
