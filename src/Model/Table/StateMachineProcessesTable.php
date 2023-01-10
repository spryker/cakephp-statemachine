<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\Validation\Validator;

/**
 * StateMachineProcesses Model
 *
 * @property \StateMachine\Model\Table\StateMachineItemStatesTable&\Cake\ORM\Association\HasMany $StateMachineItemStates
 * @property \StateMachine\Model\Table\StateMachineTimeoutsTable&\Cake\ORM\Association\HasMany $StateMachineTimeouts
 * @property \StateMachine\Model\Table\StateMachineTransitionLogsTable&\Cake\ORM\Association\HasMany $StateMachineTransitionLogs
 *
 * @method \StateMachine\Model\Entity\StateMachineProcess get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineProcess newEntity(array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineProcess> newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineProcess|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineProcess patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineProcess> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineProcess findOrCreate($search, ?callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \StateMachine\Model\Entity\StateMachineProcess saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineProcess newEmptyEntity()
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineProcess>|false saveMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineProcess> saveManyOrFail(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineProcess>|false deleteMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineProcess> deleteManyOrFail(iterable $entities, $options = [])
 */
class StateMachineProcessesTable extends Table
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

        $this->setTable('state_machine_processes');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('StateMachineItemStates', [
            'foreignKey' => 'state_machine_process_id',
            'className' => 'StateMachine.StateMachineItemStates',
        ]);
        $this->hasMany('StateMachineTimeouts', [
            'foreignKey' => 'state_machine_process_id',
            'className' => 'StateMachine.StateMachineTimeouts',
        ]);
        $this->hasMany('StateMachineTransitionLogs', [
            'foreignKey' => 'state_machine_process_id',
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
            ->allowEmptyString('id');

        $validator
            ->scalar('name')
            //->maxLength('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('state_machine')
            ->maxLength('state_machine', 90)
            ->requirePresence('state_machine', 'create')
            ->notEmptyString('state_machine');

        return $validator;
    }
}
