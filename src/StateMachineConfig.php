<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Core\Configure;

class StateMachineConfig
{
    /**
     * @var string
     */
    public const GRAPH_NAME = 'Statemachine';

    /**
     * @return array
     */
    public function getGraphDefaults(): array
    {
        return [
            'fontname' => 'Verdana',
            'labelfontname' => 'Verdana',
            'nodesep' => 0.6,
            'ranksep' => 0.8,
        ];
    }

    /**
     * @return string
     */
    public function getStateMachineItemLockExpirationInterval(): string
    {
        return '1 minute';
    }

    /**
     * @return string
     */
    public function getPathToStateMachineXmlFiles(): string
    {
        return Configure::read('StateMachine.pathToXml', ROOT . DS . 'config' . DS . 'StateMachines' . DS);
    }

    /**
     * @return string
     */
    public function getGraphDefaultFormat(): string
    {
        return 'svg';
    }

    /**
     * @return int
     */
    public function getGraphDefaultFontSize(): int
    {
        return 14;
    }

    /**
     * @return string[]
     */
    public function getGraphFormatContentTypes(): array
    {
        return [
            'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
        ];
    }

    /**
     * @return string
     */
    public function getSubProcessPrefixDelimiter(): string
    {
        return ' - ';
    }
}
