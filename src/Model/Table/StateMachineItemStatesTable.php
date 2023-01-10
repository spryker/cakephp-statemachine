<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * StateMachineItemStates Model
 *
 * @property \StateMachine\Model\Table\StateMachineProcessesTable&\Cake\ORM\Association\BelongsTo $StateMachineProcesses
 * @property \StateMachine\Model\Table\StateMachineItemStateLogsTable&\Cake\ORM\Association\HasMany $StateMachineItemStateLogs
 * @property \StateMachine\Model\Table\StateMachineTimeoutsTable&\Cake\ORM\Association\HasMany $StateMachineTimeouts
 *
 * @method \StateMachine\Model\Entity\StateMachineItemState get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemState newEntity(array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineItemState> newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemState|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemState patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineItemState> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemState findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemState saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemState newEmptyEntity()
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemState>|false saveMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemState> saveManyOrFail(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemState>|false deleteMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemState> deleteManyOrFail(iterable $entities, $options = [])
 */
class StateMachineItemStatesTable extends Table
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

        $this->setTable('state_machine_item_states');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('StateMachineProcesses', [
            'foreignKey' => 'state_machine_process_id',
            'joinType' => 'INNER',
            'className' => 'StateMachine.StateMachineProcesses',
        ]);
        $this->hasMany('StateMachineItemStateLogs', [
            'foreignKey' => 'state_machine_item_state_id',
            'className' => 'StateMachine.StateMachineItemStateLogs',
        ]);
        $this->hasMany('StateMachineTimeouts', [
            'foreignKey' => 'state_machine_item_state_id',
            'className' => 'StateMachine.StateMachineTimeouts',
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
            ->allowEmpty('id');

        $validator
            ->scalar('name')
            //->maxLength('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            //->maxLength('description')
            ->notEmptyString('description');

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
        //$rules->add($rules->existsIn(['state_machine_process_id'], 'StateMachineProcesses'));
        return $rules;
    }
}
