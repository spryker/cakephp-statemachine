<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\Process;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\Transition;

class ProcessTest extends TestCase
{
    /**
     * @return void
     */
    public function testThatManualEventsIncludeOnEnterEvents()
    {
        $process = $this->createProcess();
        $process->setTransitions($this->getTransitionsWithManualAndOnEnterEvents());

        $result = $process->getManuallyExecutableEvents();
        $this->assertSame(2, count($result));

        $this->assertSame('manual', $result[0]->getName());
        $this->assertSame('onenter', $result[1]->getName());
    }

    /**
     * @return array
     */
    protected function getTransitionsWithManualAndOnEnterEvents()
    {
        $transitions = [];

        $transition = new Transition();
        $event = new Event();
        $event->setName('default');
        $transition->setEvent($event);
        $transitions[] = $transition;

        $transition = new Transition();
        $event = new Event();
        $event->setName('manual');
        $event->setManual(true);
        $transition->setEvent($event);
        $transitions[] = $transition;

        $transition = new Transition();
        $event = new Event();
        $event->setName('onenter');
        $event->setOnEnter(true);
        $transition->setEvent($event);
        $transitions[] = $transition;

        return $transitions;
    }

    /**
     * @return \StateMachine\Business\Process\Process
     */
    protected function createProcess()
    {
        return new Process();
    }
}
