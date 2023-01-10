<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Entity;

use Cake\ORM\Entity;

/**
 * StateMachineItemStateLogs Entity
 *
 * @property int $id
 * @property int $state_machine_item_state_id
 * @property int $identifier
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \StateMachine\Model\Entity\StateMachineItemState $state_machine_item_state
 */
class StateMachineItemStateLog extends Entity
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
        'state_machine_item_state_id' => true,
        'identifier' => true,
        'created' => true,
        'state_machine_item_state' => true,
    ];
}
