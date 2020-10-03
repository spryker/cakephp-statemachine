<?php

use Migrations\AbstractMigration;

/**
 * Use conventional names.
 */
class StateMachineLogs extends AbstractMigration
{
    /**
     * @return void
     */
    public function change(): void
    {
        $this->table('state_machine_item_state_history')
            ->rename('state_machine_item_state_logs')
            ->update();
    }
}
