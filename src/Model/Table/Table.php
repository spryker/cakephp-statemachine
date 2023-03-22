<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Model\Table;

use Cake\Core\Exception\CakeException;
use Cake\ORM\Table as CakeTable;

abstract class Table extends CakeTable
{
    /**
     * @throws \Cake\Core\Exception\CakeException
     *
     * @return void
     */
    public function truncate(): void
    {
        /** @var \Cake\Database\Schema\SqlGeneratorInterface $schema */
        $schema = $this->getSchema();
        if ($this->_connection === null) {
            throw new CakeException('Cannot load connection');
        }

        $sql = $schema->truncateSql($this->_connection);
        foreach ($sql as $snippet) {
            $this->_connection->execute($snippet);
        }
    }
}
