<?php
namespace TestApp\StateMachine\Condition;

use StateMachine\Dependency\StateMachineConditionInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class FooBarBazCondition implements StateMachineConditionInterface
{
    /**
     * @param \StateMachine\Dto\StateMachine\ItemDto $itemDto
     *
     * @return bool
     */
    public function check(ItemDto $itemDto): bool
    {
        // TODO: Implement check() method.
        return true;
    }
}
