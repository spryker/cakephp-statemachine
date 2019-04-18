<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Graph\Adapter;

use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use RuntimeException;
use StateMachine\Graph\GraphAdapterInterface;

class PhpDocumentorGraphAdapter implements GraphAdapterInterface
{
    /**
     * @var \phpDocumentor\GraphViz\Graph
     */
    private $graph;

    /**
     * @return \phpDocumentor\GraphViz\Graph
     */
    protected function createPhpDocumentorGraph(): Graph
    {
        return new Graph();
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param bool $directed
     * @param bool $strict
     *
     * @return $this
     */
    public function create(string $name, array $attributes = [], bool $directed = true, bool $strict = true)
    {
        $this->graph = $this->createPhpDocumentorGraph();
        $this->graph->setName($name);

        $type = $this->getType($directed);
        $this->graph->setType($type);
        $this->graph->setStrict($strict);

        $this->addAttributesTo($attributes, $this->graph);

        return $this;
    }

    /**
     * @param bool $directed
     *
     * @return string
     */
    protected function getType(bool $directed): string
    {
        return $directed ? self::DIRECTED_GRAPH : self::GRAPH;
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
        $node = new Node($name);
        $this->addAttributesTo($attributes, $node);

        if ($group !== self::DEFAULT_GROUP) {
            $graph = $this->getGraphByName($group);
            $graph->setNode($node);
        } else {
            $this->graph->setNode($node);
        }

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
        $edge = new Edge($this->graph->findNode($fromNode), $this->graph->findNode($toNode));
        $this->addAttributesTo($attributes, $edge);

        $this->graph->link($edge);

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
        $graph = $this->getGraphByName($name);

        $this->addAttributesTo($attributes, $graph);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return \phpDocumentor\GraphViz\Graph
     */
    protected function getGraphByName(string $name): Graph
    {
        $name = 'cluster_' . $name;

        if (!$this->graph->hasGraph($name)) {
            $graph = $this->graph->create($name);
            $this->graph->addGraph($graph);
        }

        return $this->graph->getGraph($name);
    }

    /**
     * @param string $type
     * @param string|null $fileName
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function render(string $type, ?string $fileName = null): string
    {
        if ($fileName === null) {
            $fileName = sys_get_temp_dir() . '/' . $this->generateRandomString(32);
        }
        $this->graph->export($type, $fileName);

        $result = file_get_contents($fileName);
        if ($result === false) {
            throw new RuntimeException('Rendering failed for ' . $fileName);
        }

        return $result;
    }

    /**
     * @param array $attributes
     * @param \phpDocumentor\GraphViz\Edge|\phpDocumentor\GraphViz\Node|\phpDocumentor\GraphViz\Graph $element
     *
     * @return void
     */
    protected function addAttributesTo(array $attributes, $element): void
    {
        foreach ($attributes as $attribute => $value) {
            $setter = 'set' . ucfirst($attribute);
            if (strip_tags($value) !== $value) {
                $value = '<' . $value . '>';
            }
            $element->$setter($value);
        }
    }

    /**
     * @param int $length
     *
     * @return string
     */
    protected function generateRandomString(int $length = 32): string
    {
        $tokenLength = $length / 2;
        $token = bin2hex(random_bytes($tokenLength));

        if (strlen($token) !== $length) {
            $token = str_pad($token, $length, '0');
        }

        return $token;
    }
}
