<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Entity;

use Tools\Model\Entity\Entity;

/**
 * StateMachineItemState Entity
 *
 * @property int $id
 * @property int $state_machine_process_id
 * @property string $name
 * @property string|null $description
 *
 * @property \StateMachine\Model\Entity\StateMachineProcess $state_machine_process
 * @property \StateMachine\Model\Entity\StateMachineItemStateLog[] $state_machine_item_state_logs
 * @property \StateMachine\Model\Entity\StateMachineTimeout[] $state_machine_timeouts
 */
class StateMachineItemState extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'state_machine_process_id' => true,
        'name' => true,
        'description' => true,
        'state_machine_process' => true,
        'state_machine_item_state_logs' => true,
        'state_machine_timeouts' => true,
    ];
}
