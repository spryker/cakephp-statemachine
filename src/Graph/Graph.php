<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

use Cake\Core\Configure;
use Exception;

class Graph implements GraphInterface
{
    /**
     * @var \StateMachine\Graph\GraphAdapterInterface
     */
    private $adapter;

    /**
     * @param \StateMachine\Graph\GraphAdapterInterface $adapter
     * @param string $name
     * @param array $attributes
     * @param bool $directed
     * @param bool $strict
     */
    public function __construct(GraphAdapterInterface $adapter, $name, array $attributes = [], $directed = true, $strict = true)
    {
        $this->adapter = $adapter;
        $this->adapter->create($name, $attributes, $directed, $strict);
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param bool $directed
     * @param bool $strict
     *
     * @throws \Exception
     *
     * @return $this
     */
    public static function create($name, array $attributes = [], $directed = true, $strict = true)
    {
        $adapter = Configure::read('StateMachine.graphAdapter');
        if (!$adapter) {
            throw new Exception('Invalid graph adapter - config missing');
        }
        $object = new $adapter();
        if (!($object instanceof GraphAdapterInterface)) {
            throw new Exception('Invalid graph adapter: ' . $adapter . ' - not instance of ' . GraphAdapterInterface::class);
        }

        return new static($object, $name, $attributes, $directed, $strict);
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param string $group
     *
     * @return $this
     */
    public function addNode($name, $attributes = [], $group = self::DEFAULT_GROUP)
    {
        $this->adapter->addNode($name, $attributes, $group);

        return $this;
    }

    /**
     * @param string $fromNode
     * @param string $toNode
     * @param array $attributes
     *
     * @return $this
     */
    public function addEdge($fromNode, $toNode, $attributes = [])
    {
        $this->adapter->addEdge($fromNode, $toNode, $attributes);

        return $this;
    }

    /**
     * @param string $name
     * @param array $attributes
     *
     * @return $this
     */
    public function addCluster($name, $attributes = [])
    {
        $this->adapter->addCluster($name, $attributes);

        return $this;
    }

    /**
     * @param string $type
     * @param string|null $fileName
     *
     * @return string
     */
    public function render($type, $fileName = null)
    {
        return $this->adapter->render($type, $fileName);
    }
}
