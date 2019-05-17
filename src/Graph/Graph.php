<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph;

use Cake\Core\Configure;
use Exception;
use StateMachine\Graph\Adapter\PhpDocumentorGraphAdapter;

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
    public function __construct(GraphAdapterInterface $adapter, string $name, array $attributes = [], bool $directed = true, bool $strict = true)
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
     * @return static
     */
    public static function create(string $name, array $attributes = [], bool $directed = true, bool $strict = true)
    {
        $adapter = Configure::read('StateMachine.graphAdapter') ?: PhpDocumentorGraphAdapter::class;
        /** @var \StateMachine\Graph\GraphAdapterInterface $object */
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
    public function addNode(string $name, array $attributes = [], string $group = self::DEFAULT_GROUP)
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
    public function addEdge(string $fromNode, string $toNode, array $attributes = [])
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
    public function addCluster(string $name, array $attributes = [])
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
    public function render(string $type, ?string $fileName = null): string
    {
        return $this->adapter->render($type, $fileName);
    }
}
