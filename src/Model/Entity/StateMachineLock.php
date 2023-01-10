<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Entity;

use Cake\ORM\Entity;

/**
 * StateMachineLock Entity
 *
 * @property int $id
 * @property string $identifier
 * @property \Cake\I18n\DateTime $expires
 * @property \Cake\I18n\DateTime|null $created
 */
class StateMachineLock extends Entity
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
        'identifier' => true,
        'expires' => true,
        'created' => true,
    ];
}
