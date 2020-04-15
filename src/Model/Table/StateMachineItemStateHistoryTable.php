<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use StateMachine\Model\Entity\StateMachineItem;
use Tools\Model\Table\Table;

/**
 * StateMachineItemStateHistory Model
 *
 * @property \StateMachine\Model\Table\StateMachineItemStatesTable&\Cake\ORM\Association\BelongsTo $StateMachineItemStates
 *
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory newEntity(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[] newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory findOrCreate($search, ?callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory newEmptyEntity()
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateHistory[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class StateMachineItemStateHistoryTable extends Table
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

        $this->setTable('state_machine_item_state_history');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('StateMachineItemStates', [
            'foreignKey' => 'state_machine_item_state_id',
            'joinType' => 'INNER',
            'className' => 'StateMachine.StateMachineItemStates',
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
            ->scalar('identifier')
            //->maxLength('identifier')
            ->requirePresence('identifier', 'create')
            ->notEmpty('identifier');

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
        return $rules;
    }

    /**
     * @param \StateMachine\Model\Entity\StateMachineItem $stateMachineItem
     *
     * @return array
     */
    public function getHistory(StateMachineItem $stateMachineItem): array
    {
        return $this->find()
            ->contain(['StateMachineItemStates' => 'StateMachineProcesses'])
            ->where([
                'StateMachineItemStateHistory.identifier' => $stateMachineItem->identifier,
                'StateMachineProcesses.state_machine' => $stateMachineItem->state_machine,
                'StateMachineProcesses.id' => $stateMachineItem->state_machine_transition_log->state_machine_process_id,
            ])
            ->orderDesc($this->aliasField('id'))
            ->all()->toArray();
    }
}
