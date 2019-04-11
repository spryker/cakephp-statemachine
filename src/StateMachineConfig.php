<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine;

use Cake\Core\Configure;

class StateMachineConfig
{
    public const GRAPH_NAME = 'Statemachine';

    /**
     * @return array
     */
    public function getGraphDefaults()
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
    public function getStateMachineItemLockExpirationInterval()
    {
        return '1 minutes';
    }

    /**
     * @return string
     */
    public function getPathToStateMachineXmlFiles()
    {
        return Configure::read('StateMachine.pathToXml', ROOT . DS . 'config' . DS . 'StateMachines' . DS);
    }

    /**
     * @return string
     */
    public function getGraphDefaultFormat()
    {
        return 'svg';
    }

    /**
     * @return string
     */
    public function getGraphDefaultFontSize()
    {
        return '14';
    }

    /**
     * @return string[]
     */
    public function getGraphFormatContentTypes()
    {
        return [
            'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
        ];
    }

    /**
     * @return string
     */
    public function getSubProcessPrefixDelimiter()
    {
        return ' - ';
    }
}
