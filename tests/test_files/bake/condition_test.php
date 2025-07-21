<?php

namespace TestApp\Test\TestCase\StateMachine\Condition;

use Cake\TestSuite\TestCase;
use TestApp\StateMachine\Condition\FooBarBazCondition;

class FooBarBazConditionTest extends TestCase
{
    /**
     * @var array<string>
     */
    protected array $fixtures = [
    ];

    /**
     * @return void
     */
    public function testRun(): void
    {
        $condition = new FooBarBazCondition();

        //$itemDto = new ItemDto();
        //TODO

        //$result = $condition->check($itemDto);
        //$this->assertTrue($result);
    }
}
