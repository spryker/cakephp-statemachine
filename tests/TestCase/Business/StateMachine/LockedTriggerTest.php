<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Exception\LockException;
use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Business\StateMachine\HandlerResolver;
use StateMachine\Business\StateMachine\LockedTrigger;
use StateMachine\Business\StateMachine\TriggerInterface;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\Test\Fixture\StateMachineProcessesFixture;

class LockedTriggerTest extends TestCase
{
    /**
     * @return void
     */
    public function testTriggerForNewStateMachineItemWhenLockedShouldThrowException(): void
    {
        $this->expectException(LockException::class);

        $triggerMock = $this->createTriggerMock();

        $itemLockMock = $this->createItemLockMock();

        $itemLockMock->method('acquire')
            ->willThrowException(new LockException());

        $lockedTrigger = $this->createLockedTrigger($triggerMock, $itemLockMock);
        $lockedTrigger->triggerForNewStateMachineItem(
            $this->createProcessDto(
                StateMachineProcessesFixture::PROCESS_NAME_1,
                StateMachineProcessesFixture::DEFAULT_TEST_STATE_MACHINE_NAME
            ),
            1
        );
    }

    /**
     * @return void
     */
    public function testTriggerEventForNewItemWhenLockedShouldThrowException(): void
    {
        $this->expectException(LockException::class);

        $triggerMock = $this->createTriggerMock();

        $itemLockMock = $this->createItemLockMock();

        $itemLockMock->method('acquire')
            ->willThrowException(new LockException());

        $lockedTrigger = $this->createLockedTrigger($triggerMock, $itemLockMock);
        $lockedTrigger->triggerEvent('event', []);
    }

    /**
     * @param \StateMachine\Business\StateMachine\TriggerInterface $triggerMock
     * @param \StateMachine\Business\Lock\ItemLockInterface $itemLockMock
     *
     * @return \StateMachine\Business\StateMachine\TriggerInterface
     */
    public function createLockedTrigger(TriggerInterface $triggerMock, ItemLockInterface $itemLockMock): TriggerInterface
    {
        return new LockedTrigger(
            $triggerMock,
            $itemLockMock,
            new HandlerResolver([])
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\StateMachine\TriggerInterface
     */
    protected function createTriggerMock()
    {
        $triggerLockMock = $this->getMockBuilder(TriggerInterface::class)->getMock();

        return $triggerLockMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Business\Lock\ItemLockInterface
     */
    protected function createItemLockMock()
    {
        $itemLockMock = $this->getMockBuilder(ItemLockInterface::class)->getMock();

        return $itemLockMock;
    }

    /**
     * @param string $processName
     * @param string $stateMachineName
     *
     * @return \StateMachine\Dto\StateMachine\ProcessDto
     */
    protected function createProcessDto(string $processName, string $stateMachineName): ProcessDto
    {
        $processDto = new ProcessDto();
        $processDto->setProcessName($processName);
        $processDto->setStateMachineName($stateMachineName);

        return $processDto;
    }
}
