<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Entity;

use Cake\ORM\Entity;

/**
 * StateMachineProcess Entity
 *
 * @property int $id
 * @property string $name
 * @property string $state_machine
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property array<\StateMachine\Model\Entity\StateMachineItemState> $state_machine_item_states
 * @property array<\StateMachine\Model\Entity\StateMachineTimeout> $state_machine_timeouts
 * @property array<\StateMachine\Model\Entity\StateMachineTransitionLog> $state_machine_transition_logs
 */
class StateMachineProcess extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'state_machine' => true,
        'created' => true,
        'modified' => true,
        'state_machine_item_states' => true,
        'state_machine_timeouts' => true,
        'state_machine_transition_logs' => true,
    ];
}
