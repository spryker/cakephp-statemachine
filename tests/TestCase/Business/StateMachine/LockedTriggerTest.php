<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use StateMachine\Business\Exception\LockException;
use StateMachine\Business\Lock\ItemLockInterface;
use StateMachine\Business\StateMachine\LockedTrigger;
use StateMachine\Business\StateMachine\TriggerInterface;
use StateMachine\Test\TestCase\Mocks\StateMachineMocks;
use StateMachine\Transfer\StateMachineProcessTransfer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group LockedTriggerTest
 * Add your own group annotations below this line
 */
class LockedTriggerTest extends StateMachineMocks
{
    /**
     * @return void
     */
    public function testTriggerForNewItemWhenLockedShouldThrowException()
    {
        $this->expectException(LockException::class);

        $triggerMock = $this->createTriggerMock();

        $itemLockMock = $this->createItemLockMock();

        $itemLockMock->method('acquire')
            ->willThrowException(new LockException());

        $lockedTrigger = $this->createLockedTrigger($triggerMock, $itemLockMock);
        $lockedTrigger->triggerForNewStateMachineItem(new StateMachineProcessTransfer(), 1);
    }

    /**
     * @return void
     */
    public function testTriggerEventForNewItemWhenLockedShouldThrowException()
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
     * @param \StateMachine\Business\StateMachine\TriggerInterface|null $triggerMock
     * @param \StateMachine\Business\Lock\ItemLockInterface|null $itemLockMock
     *
     * @return \StateMachine\Business\StateMachine\LockedTrigger
     */
    public function createLockedTrigger(?TriggerInterface $triggerMock = null, ?ItemLockInterface $itemLockMock = null)
    {
        if ($triggerMock === null) {
            $triggerMock = $this->createTriggerMock();
        }

        if ($itemLockMock === null) {
            $itemLockMock = $this->createItemLockMock();
        }

        return new LockedTrigger(
            $triggerMock,
            $itemLockMock
        );
    }
}
