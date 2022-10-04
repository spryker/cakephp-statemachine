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
 * @property \StateMachine\Model\Table\StateMachineItemStatesTable&\Cake\ORM\Association\BelongsTo $StateMachineItemStates
 *
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog newEntity(array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineItemStateLog> newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineItemStateLog> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog findOrCreate($search, ?callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineItemStateLog newEmptyEntity()
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemStateLog>|false saveMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemStateLog> saveManyOrFail(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemStateLog>|false deleteMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\StateMachine\Model\Entity\StateMachineItemStateLog> deleteManyOrFail(iterable $entities, $options = [])
 */
class StateMachineItemStateLogsTable extends Table
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

        $this->setTable('state_machine_item_state_logs');
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
            ->notEmptyString('identifier');

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
     * @return array<\StateMachine\Model\Entity\StateMachineTransitionLog>
     */
    public function getHistory(StateMachineItem $stateMachineItem): array
    {
        if ($stateMachineItem->state_machine_transition_log === null) {
            return [];
        }

        return $this->find()
            ->contain(['StateMachineItemStates' => 'StateMachineProcesses'])
            ->where([
                'StateMachineItemStateLogs.identifier' => $stateMachineItem->identifier,
                'StateMachineProcesses.state_machine' => $stateMachineItem->state_machine,
                'StateMachineProcesses.id' => $stateMachineItem->state_machine_transition_log->state_machine_process_id,
            ])
            ->orderDesc($this->aliasField('id'))
            ->all()
            ->toArray();
    }
}
