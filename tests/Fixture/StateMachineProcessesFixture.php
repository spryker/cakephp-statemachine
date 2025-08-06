<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\Fixture;

use Cake\Test\Fixture\TestFixture;

class StateMachineProcessesFixture extends TestFixture
{
    /**
     * @var string
     */
    public const DEFAULT_TEST_STATE_MACHINE_NAME = 'TestingSm';

    /**
     * @var string
     */
    public const PROCESS_NAME_1 = 'TestProcess';

    /**
     * @var string
     */
    public const PROCESS_NAME_2 = 'Process2';

    /**
     * Fields
     *
     * @var array
     */
    public array $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 90, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'state_machine' => ['type' => 'string', 'length' => 90, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'name' => ['type' => 'unique', 'columns' => ['name', 'state_machine'], 'length' => []],
        ],
        '_indexes' => [
            'state_machine' => ['type' => 'index', 'columns' => ['state_machine'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci',
        ],
    ];

    /**
     * Records
     *
     * @var array
     */
    public array $records = [
            [
                'id' => 1,
                'name' => self::PROCESS_NAME_1,
                'state_machine' => self::DEFAULT_TEST_STATE_MACHINE_NAME,
                'created' => '2018-06-08 22:35:57',
                'modified' => '2018-06-08 22:35:57',
            ],
        ];
}
