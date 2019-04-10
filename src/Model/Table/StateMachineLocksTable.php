<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StateMachineLocks Model
 *
 * @method \StateMachine\Model\Entity\StateMachineLock get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock newEntity($data = null, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock[] newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock[] patchEntities($entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StateMachineLocksTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('state_machine_locks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('identifier')
            ->maxLength('identifier', 50)
            ->requirePresence('identifier', 'create')
            ->allowEmptyString('identifier', false);

        $validator
            ->dateTime('expires')
            ->requirePresence('expires', 'create')
            ->allowEmptyDateTime('expires', false);

        return $validator;
    }
}
