<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

interface GraphInterface
{
    public const DEFAULT_GROUP = 'default';

    /**
     * @param string $name
     * @param array $attributes
     * @param string $group
     *
     * @return $this
     */
    public function addNode(string $name, array $attributes = [], string $group = self::DEFAULT_GROUP);

    /**
     * @param string $fromNode
     * @param string $toNode
     * @param array $attributes
     *
     * @return $this
     */
    public function addEdge(string $fromNode, string $toNode, array $attributes = []);

    /**
     * @param string $name
     * @param array $attributes
     *
     * @return $this
     */
    public function addCluster(string $name, array $attributes = []);

    /**
     * @param string $type
     * @param string|null $fileName
     *
     * @return string
     */
    public function render(string $type, ?string $fileName = null): string;
}
