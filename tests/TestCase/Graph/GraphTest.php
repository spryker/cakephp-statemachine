<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Graph;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use StateMachine\Graph\Adapter\PhpDocumentorGraphAdapter;
use StateMachine\Graph\Graph;

class GraphTest extends TestCase
{
    /**
     * @return void
     */
    public function testDefault(): void
    {
        Configure::write('StateMachine.graphAdapter', PhpDocumentorGraphAdapter::class);

        $graph = Graph::create('Foo');

        $result = $graph->render('svg');
        $this->assertTextContains('<g id="graph0" class="graph"', $result);
    }
}
