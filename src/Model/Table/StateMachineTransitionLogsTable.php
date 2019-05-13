<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use RuntimeException;
use Tools\Model\Table\Table;

/**
 * StateMachineTransitionLogs Model
 *
 * @property \StateMachine\Model\Table\StateMachineProcessesTable|\Cake\ORM\Association\BelongsTo $StateMachineProcesses
 *
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog newEntity($data = null, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog[] newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog[] patchEntities($entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \StateMachine\Model\Entity\StateMachineTransitionLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class StateMachineTransitionLogsTable extends Table
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

        $this->setTable('state_machine_transition_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->allowEmpty('id');

        $validator
            ->scalar('identifier')
            //->maxLength('identifier')
            ->requirePresence('identifier', 'create')
            ->notEmpty('identifier');

        $validator
            ->boolean('locked')
            ->requirePresence('locked', 'create')
            ->notEmpty('locked');

        $validator
            ->scalar('event')
            //->maxLength('event')
            ->allowEmpty('event');

        $validator
            ->scalar('params')
            //->maxLength('params')
            ->allowEmpty('params');

        $validator
            ->scalar('source_state')
            //->maxLength('source_state')
            ->allowEmpty('source_state');

        $validator
            ->scalar('target_state')
            //->maxLength('target_state')
            ->allowEmpty('target_state');

        $validator
            ->scalar('command')
            //->maxLength('command')
            ->allowEmpty('command');

        $validator
            ->scalar('condition')
            //->maxLength('condition')
            ->allowEmpty('condition');

        $validator
            ->boolean('is_error')
            ->requirePresence('is_error', 'create')
            ->notEmpty('is_error');

        $validator
            ->scalar('error_message')
            ->allowEmpty('error_message');

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

    /**
     * @param \Cake\Event\EventInterface $event
     * @param \StateMachine\Model\Entity\StateMachineTransitionLog $entity
     * @param \ArrayObject $options
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $id = $entity->state_machine_item_id;
        if (!$id) {
            throw new RuntimeException('Property $state_machine_item_id on entity ' . $entity->id . ' missing after save.');
        }

        $fields = [
            'state_machine_transition_log_id' => $entity->id,
        ];
        if ($entity->target_state) {
            $fields['state'] = $entity->target_state;
        }

        $stateMachineItemsTable = TableRegistry::get('StateMachine.StateMachineItems');
        if (!$stateMachineItemsTable->updateAll($fields, ['id' => $id])) {
            throw new RuntimeException('Could not update row, StateMachineItem not found: ' . $id);
        }
    }

    /**
     * @param int $stateMachineItemId
     *
     * @return \StateMachine\Model\Entity\StateMachineTransitionLog[]
     */
    public function getLogs(int $stateMachineItemId): array
    {
        return $this->find()
            ->where(['state_machine_item_id' => $stateMachineItemId])
            ->orderDesc('id')
            ->all()->toArray();
    }
}
