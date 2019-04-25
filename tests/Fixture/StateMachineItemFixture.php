<?php
namespace StateMachine\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * StateMachineItemFixture
 */
class StateMachineItemFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'identifier' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null],
        'state_machine' => ['type' => 'string', 'length' => 90, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'identifier' => ['type' => 'unique', 'columns' => ['identifier', 'state_machine'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'identifier' => 1,
                'state_machine' => 'Lorem ipsum dolor sit amet',
                'created' => '2019-04-20 22:34:41'
            ],
        ];
        parent::init();
    }
}
