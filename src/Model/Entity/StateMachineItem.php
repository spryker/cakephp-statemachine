<?php declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * StateMachineItem Entity
 *
 * @property int $id
 * @property int $identifier
 * @property string $state_machine
 * @property string|null $process
 * @property string|null $state
 * @property int|null $state_machine_transition_log_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \StateMachine\Model\Entity\StateMachineTransitionLog $state_machine_transition_log !
 * @property array|null $url
 */
class StateMachineItem extends Entity
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
        'id' => false,
        '*' => true,
    ];

    /**
     * @return array|null
     */
    protected function _getUrl(): ?array
    {
        if (!$this->identifier || !$this->state_machine) {
            return null;
        }

        $mapElement = Configure::read('StateMachine.map.' . $this->state_machine);
        if (!$mapElement) {
            return null;
        }

        $defaults = [
            'prefix' => false,
            'plugin' => false,
            'action' => 'view',
        ];

        if (is_bool($mapElement)) {
            $mapElement = $this->state_machine;
        }
        if (is_string($mapElement)) {
            $url = [
                'controller' => $mapElement,
            ] + $defaults;
        } else {
            $url = $mapElement + $defaults;
        }

        return $url + [
            $this->identifier,
        ];
    }
}
