<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * StateMachineTimeoutsFixture
 */
class StateMachineTimeoutsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'state_machine_item_state_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'state_machine_process_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'identifier' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'event' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'timeout' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'timeout' => ['type' => 'index', 'columns' => ['timeout'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'identifier' => ['type' => 'unique', 'columns' => ['identifier', 'state_machine_item_state_id'], 'length' => []],
            'state_machine_process_id' => ['type' => 'foreign', 'columns' => ['state_machine_process_id'], 'references' => ['state_machine_processes', 'id'], 'update' => 'restrict', 'delete' => 'cascade', 'length' => []],
            'state_machine_item_state_id' => ['type' => 'foreign', 'columns' => ['state_machine_item_state_id'], 'references' => ['state_machine_item_states', 'id'], 'update' => 'restrict', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci',
        ],
    ];

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'state_machine_item_state_id' => 1,
                'state_machine_process_id' => 1,
                'identifier' => 1,
                'event' => 'Lorem ipsum dolor sit amet',
                'timeout' => '2019-04-22 12:59:50',
            ],
        ];
        parent::init();
    }
}
