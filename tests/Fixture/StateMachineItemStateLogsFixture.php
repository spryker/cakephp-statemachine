<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class StateMachineItemStateLogsFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'state_machine_item_state_logs';

    /**
     * Fields
     *
     * @var array
     */
    public array $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'state_machine_item_state_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'identifier' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'identifier' => ['type' => 'index', 'columns' => ['identifier', 'state_machine_item_state_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'state_machine_item_state_id' => ['type' => 'foreign', 'columns' => ['state_machine_item_state_id'], 'references' => ['state_machine_item_states', 'id'], 'update' => 'restrict', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci',
        ],
    ];

    /**
     * Records
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'state_machine_item_state_id' => 1,
                'identifier' => 1,
                'created' => '2019-04-22 12:59:45',
            ],
        ];
        parent::init();
    }
}
