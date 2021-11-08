<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\Validation\Validator;
use Tools\Model\Table\Table;

/**
 * StateMachineLocks Model
 *
 * @method \StateMachine\Model\Entity\StateMachineLock get($primaryKey, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock newEntity(array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineLock> newEntities(array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\StateMachine\Model\Entity\StateMachineLock> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock findOrCreate($search, ?callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \StateMachine\Model\Entity\StateMachineLock newEmptyEntity()
 * @method \StateMachine\Model\Entity\StateMachineLock[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \StateMachine\Model\Entity\StateMachineLock[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
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
    public function initialize(array $config): void
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
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('identifier')
            ->requirePresence('identifier', 'create')
            ->allowEmptyString('identifier');

        $validator
            ->dateTime('expires')
            ->requirePresence('expires', 'create')
            ->allowEmptyDateTime('expires');

        return $validator;
    }
}
