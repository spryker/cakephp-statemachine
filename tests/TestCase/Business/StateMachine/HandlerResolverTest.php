<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Exception\StateMachineHandlerNotFound;
use StateMachine\Business\StateMachine\HandlerResolver;
use StateMachine\Dependency\StateMachineHandlerInterface;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group HandlerResolverTest
 * Add your own group annotations below this line
 */
class HandlerResolverTest extends TestCase
{
    public const TEST_HANDLER_NAME = 'testing state machine name';

    /**
     * @return void
     */
    public function testHandlerResolverShouldReturnInstanceOfHandlerWhenCorrectNameGiven()
    {
        $handlerResolver = $this->createHandlerResolver()->get(static::TEST_HANDLER_NAME);

        $this->assertInstanceOf(StateMachineHandlerInterface::class, $handlerResolver);
    }

    /**
     * @return void
     */
    public function testHandlerResolverWhenRequestedNonExistentShouldThrowException()
    {
        $this->expectException(StateMachineHandlerNotFound::class);

        $this->createHandlerResolver()->get('no existing state machine');
    }

    /**
     * @return \StateMachine\Business\StateMachine\HandlerResolver
     */
    protected function createHandlerResolver()
    {
        $stateMachineHandlerMock = $this->createStateMachineHandlerMock();
        $stateMachineHandlerMock->method('getStateMachineName')->willReturn(static::TEST_HANDLER_NAME);

        return new HandlerResolver([$stateMachineHandlerMock]);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\Dependency\StateMachineHandlerInterface
     */
    protected function createStateMachineHandlerMock()
    {
        $stateMachineHandlerMock = $this->getMockBuilder(StateMachineHandlerInterface::class)->getMock();

        return $stateMachineHandlerMock;
    }
}
