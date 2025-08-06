<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * StateMachineTimeouts Model
 *
 * @property \StateMachine\Model\Table\StateMachineItemStatesTable&\Cake\ORM\Association\BelongsTo $StateMachineItemStates
 * @property \StateMachine\Model\Table\StateMachineProcessesTable&\Cake\ORM\Association\BelongsTo $StateMachineProcesses
 *
 * @method \StateMachine\Model\Entity\StateMachineTimeout get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTimeout newEntity(array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineTimeout> newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTimeout|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTimeout patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineTimeout> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTimeout findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTimeout saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTimeout newEmptyEntity()
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineTimeout>|false saveMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineTimeout> saveManyOrFail(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineTimeout>|false deleteMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineTimeout> deleteManyOrFail(iterable $entities, $options = [])
 */
class StateMachineTimeoutsTable extends Table
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

        $this->setTable('state_machine_timeouts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('StateMachineItemStates', [
            'foreignKey' => 'state_machine_item_state_id',
            'joinType' => 'INNER',
            'className' => 'StateMachine.StateMachineItemStates',
        ]);
        $this->belongsTo('StateMachineProcesses', [
            'foreignKey' => 'state_machine_process_id',
            'joinType' => 'INNER',
            'className' => 'StateMachine.StateMachineProcesses',
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
            ->allowEmptyString('id');

        $validator
            ->scalar('identifier')
            //->maxLength('identifier')
            ->requirePresence('identifier', 'create')
            ->notEmptyString('identifier');

        $validator
            ->scalar('event')
            //->maxLength('event')
            ->requirePresence('event', 'create')
            ->notEmptyString('event');

        $validator
            ->dateTime('timeout')
            ->requirePresence('timeout', 'create')
            ->notEmptyString('timeout');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        //$rules->add($rules->existsIn(['state_machine_item_state_id'], 'StateMachineItemStates'));
        //$rules->add($rules->existsIn(['state_machine_process_id'], 'StateMachineProcesses'));
        return $rules;
    }
}
