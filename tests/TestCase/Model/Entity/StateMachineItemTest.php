<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Model\Entity;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use StateMachine\Model\Entity\StateMachineItem;

class StateMachineItemTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $map = [
            'Foo' => [
                'controller' => 'FooBars',
            ],
            'MyStateMachine' => 'MyModel',
            'MatchesModelName' => true,
        ];
        Configure::write('StateMachine.map', $map);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Configure::delete('StateMachine.map');
    }

    /**
     * @return void
     */
    public function testIdentifierUrlArrayMap(): void
    {
        $stateMachineItem = new StateMachineItem();

        $result = $stateMachineItem->url;
        $this->assertNull($result);

        $stateMachineItem->state_machine = 'Foo';
        $stateMachineItem->identifier = 3;

        $result = $stateMachineItem->url;
        $expected = [
            'controller' => 'FooBars',
            'prefix' => false,
            'plugin' => false,
            'action' => 'view',
            0 => 3,
        ];
        $this->assertSame($expected, $result);
    }

    /**
     * @return void
     */
    public function testIdentifierUrlStringMap(): void
    {
        $stateMachineItem = new StateMachineItem();

        $stateMachineItem->state_machine = 'MyStateMachine';
        $stateMachineItem->identifier = 3;

        $result = $stateMachineItem->url;
        $expected = [
            'controller' => 'MyModel',
            'prefix' => false,
            'plugin' => false,
            'action' => 'view',
            0 => 3,
        ];
        $this->assertSame($expected, $result);
    }

    /**
     * @return void
     */
    public function testIdentifierUrlBoolMap(): void
    {
        $stateMachineItem = new StateMachineItem();

        $stateMachineItem->state_machine = 'MatchesModelName';
        $stateMachineItem->identifier = 3;

        $result = $stateMachineItem->url;
        $expected = [
            'controller' => 'MatchesModelName',
            'prefix' => false,
            'plugin' => false,
            'action' => 'view',
            0 => 3,
        ];
        $this->assertSame($expected, $result);
    }
}
