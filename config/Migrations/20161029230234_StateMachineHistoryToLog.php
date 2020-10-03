<?php

use Migrations\AbstractMigration;

/**
 * Use conventional names.
 */
class StateMachineHistoryToLog extends AbstractMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->table('state_machine_item_state_history')
            ->rename('state_machine_item_state_logs')
            ->update();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->table('state_machine_item_state_logs')
            ->rename('state_machine_item_state_history')
            ->update();
    }
}
