<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use DateTime;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\StateMachine\PersistenceInterface;
use StateMachine\Business\StateMachine\Timeout;
use StateMachine\Business\StateMachine\TimeoutInterface;
use StateMachine\Transfer\StateMachineItemTransfer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group TimeoutTest
 * Add your own group annotations below this line
 */
class TimeoutTest extends TestCase
{
    public const STATE_WITH_TIMEOUT = 'State with timeout';

    /**
     * @return void
     */
    public function testSetTimeoutShouldStoreNewTimeout()
    {
        $stateMachinePersistenceMock = $this->createPersistenceMock();

        $stateMachinePersistenceMock->expects($this->once())
            ->method('dropTimeoutByItem')
            ->with($this->isInstanceOf(StateMachineItemTransfer::class));

        $stateMachinePersistenceMock->expects($this->once())
            ->method('saveStateMachineItemTimeout')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class),
                $this->isInstanceOf(DateTime::class),
                $this->isType('string')
            );

        $timeout = $this->createTimeout($stateMachinePersistenceMock);
        $timeout->setNewTimeout(
            $this->createProcess(),
            $this->createStateMachineItemTransfer()
        );
    }

    /**
     * @return void
     */
    public function testDropOldTimeoutShouldRemoveExpiredTimeoutsFromPersistence()
    {
        $stateMachinePersistenceMock = $this->createPersistenceMock();

        $stateMachinePersistenceMock->expects($this->once())
            ->method('dropTimeoutByItem')
            ->with($this->isInstanceOf(StateMachineItemTransfer::class));

        $timeout = $this->createTimeout($stateMachinePersistenceMock);
        $timeout->dropOldTimeout(
            $this->createProcess(),
            static::STATE_WITH_TIMEOUT,
            $this->createStateMachineItemTransfer()
        );
    }

    /**
     * @return \StateMachine\Business\Process\Process
     */
    protected function createProcess(): Process
    {
        $process = new Process();

        $outgoingTransitions = new Transition();
        $event = new Event();
        $event->setName('Timeout event');
        $event->setTimeout('1 DAY');
        $outgoingTransitions->setEvent($event);

        $state = new State();
        $state->setName(static::STATE_WITH_TIMEOUT);
        $state->addOutgoingTransition($outgoingTransitions);

        $process->addState($state);

        return $process;
    }

    /**
     * @return \StateMachine\Transfer\StateMachineItemTransfer
     */
    protected function createStateMachineItemTransfer(): StateMachineItemTransfer
    {
        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setStateName(static::STATE_WITH_TIMEOUT);

        return $stateMachineItemTransfer;
    }

    /**
     * @param \StateMachine\Business\StateMachine\PersistenceInterface $persistenceMock
     *
     * @return \StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected function createTimeout(PersistenceInterface $persistenceMock): TimeoutInterface
    {
        return new Timeout($persistenceMock);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistenceMock()
    {
        $persistenceMock = $this->getMockBuilder(PersistenceInterface::class)->getMock();

        return $persistenceMock;
    }
}
