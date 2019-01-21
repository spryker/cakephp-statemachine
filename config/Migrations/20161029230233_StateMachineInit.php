<?php
use Migrations\AbstractMigration;

/**
 * Add this for PostgreSQL via:
 * bin/cake Migrations migrate -p StateMachine
 *
 * It uses the default database collation and encoding (utf8 or utf8mb4 etc).
 */
class StateMachineInit extends AbstractMigration {

	/**
	 * @return void
	 */
	public function up() {
		$this->table('state_machine_processes')
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 150,
				'null' => false,
			])
			->addColumn('state_machine', 'string', [
				'default' => null,
				'limit' => 150,
				'null' => false,
			])
			->addColumn('created', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->create();

		$this->table('state_machine_item_states')
			->addColumn('state_machine_process_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addColumn('description', 'string', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->create();

		$this->table('state_machine_item_state_history')
			->addColumn('state_machine_item_state_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('identifier', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('created', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->create();

		$this->table('state_machine_transition_logs')
			->addColumn('state_machine_process_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('identifier', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('locked', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => false,
			])
			->addColumn('event', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('params', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('source_state', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('target_state', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('command', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('condition', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('is_error', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => false,
			])
			->addColumn('error_message', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->create();

		$this->table('state_machine_timeouts')
			->addColumn('state_machine_item_state_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('state_machine_process_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('identifier', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('event', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('timeout', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->create();

		$this->table('state_machine_locks')
			->addColumn('identifier', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('expires', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addColumn('created', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->create();
	}

	/**
	 * @return void
	 */
	public function down() {
		$this->dropTable('state_machine_processes');
		$this->dropTable('state_machine_items');
		$this->dropTable('state_machine_item_states');
		$this->dropTable('state_machine_item_state_history');
		$this->dropTable('state_machine_transition_logs');
		$this->dropTable('state_machine_timeouts');
		$this->dropTable('state_machine_locks');
	}

}
