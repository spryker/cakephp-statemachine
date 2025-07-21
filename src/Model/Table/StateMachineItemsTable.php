<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\Validation\Validator;

/**
 * StateMachineItems Model
 *
 * @method \StateMachine\Model\Entity\StateMachineItem get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItem newEntity(array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineItem> newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItem|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItem saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineItem> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItem findOrCreate($search, ?callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \StateMachine\Model\Table\StateMachineTransitionLogsTable&\Cake\ORM\Association\BelongsTo $StateMachineTransitionLogs
 * @method \StateMachine\Model\Entity\StateMachineItem newEmptyEntity()
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItem>|false saveMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItem> saveManyOrFail(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItem>|false deleteMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItem> deleteManyOrFail(iterable $entities, $options = [])
 */
class StateMachineItemsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('state_machine_items');
        $this->setDisplayField('state');
        $this->setPrimaryKey('id');

        $this->belongsTo('StateMachineTransitionLogs', [
            'className' => 'StateMachine.StateMachineTransitionLogs',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('identifier')
            ->requirePresence('identifier', 'create')
            ->allowEmptyString('identifier');

        $validator
            ->scalar('state_machine')
            ->maxLength('state_machine', 90)
            ->requirePresence('state_machine', 'create')
            ->allowEmptyString('state_machine');

        return $validator;
    }
}
