<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Entity;

use Tools\Model\Entity\Entity;

/**
 * StateMachineTransitionLog Entity
 *
 * @property int $id
 * @property int $state_machine_process_id
 * @property int $state_machine_item_id
 * @property int $identifier
 * @property bool $locked
 * @property string|null $event
 * @property string|null $params
 * @property string|null $source_state
 * @property string|null $target_state
 * @property string|null $command
 * @property string|null $condition
 * @property bool $is_error
 * @property string|null $error_message
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \StateMachine\Model\Entity\StateMachineProcess $state_machine_process
 */
class StateMachineTransitionLog extends Entity
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
    protected $_accessible = [
        'state_machine_process_id' => true,
        'identifier' => true,
        'locked' => true,
        'event' => true,
        'params' => true,
        'source_state' => true,
        'target_state' => true,
        'command' => true,
        'condition' => true,
        'is_error' => true,
        'error_message' => true,
        'created' => true,
        'state_machine_process' => true,
    ];
}
