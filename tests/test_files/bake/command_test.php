<?php

namespace TestApp\Test\TestCase\StateMachine\Command;

use Cake\TestSuite\TestCase;
use TestApp\StateMachine\Command\FooBarBazCommand;

class FooBarBazCommandTest extends TestCase
{
    /**
     * @var array<string>
     */
    protected $fixtures = [
    ];

    /**
     * @return void
     */
    public function testRun(): void
    {
        $command = new FooBarBazCommand();

        //$itemDto = new ItemDto();
        //TODO

        //$command->run($itemDto);
    }
}
