<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\View\Helper;

use Cake\View\Helper;
use StateMachine\Model\Entity\StateMachineItem;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class StateMachineHelper extends Helper
{
    /**
     * @var array
     */
    protected $helpers = ['Html'];

    /**
     * @param \StateMachine\Model\Entity\StateMachineItem $stateMachineItem
     * @param array $options Attributes for HtmlHelper::link()
     *
     * @return string HTML
     */
    public function itemLink(StateMachineItem $stateMachineItem, array $options = []): string
    {
        $url = $stateMachineItem->url;
        if (!$url) {
            $urlArray = ['prefix' => 'Admin', 'plugin' => 'StateMachine', 'controller' => 'StateMachineItems', 'action' => 'view', $stateMachineItem->id];

            return $this->Html->link((string)$stateMachineItem->identifier, $urlArray);
        }

        return $this->Html->link((string)$stateMachineItem->identifier, $url, $options);
    }
}
