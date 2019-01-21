<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell;

use Cake\Console\Shell;

/**
 * @author Mark Scherer
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @property \StateMachine\Model\Table\StateMachinesTable $StateMachines
 */
class StateMachineShell extends Shell
{
    /**
     * @var string
     */
    public $modelClass = 'StateMachine.StateMachines';

    /**
     * @param string|null $name
     * @return void
     */
    public function init($name = null)
    {
        if (!$name) {
            $name = $this->in('Name', null, $name);
        }

        $file = '....xml'
        $this->out('Generated: ' . $file);
    }

    /**
     * @return void
     */
    public function checkTimeouts()
    {
    }

    /**
     * @return void
     */
    public function checkConditions()
    {
    }

    /**
     * @return void
     */
    public function clearLogs()
    {
    }

    /**
     * @return void
     */
    public function reset()
    {
        if (!$this->param('quiet')) {
            $in = $this->in('Sure?', ['Y', 'n'], 'n');
            if ($in !== 'Y') {
                $this->abort('Aborted!');
            }
        }

        //$this->StateMachines->truncate();
        $this->info('Reset done');
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('checkConditions', [
            'help' => 'Check if any conditions match',
        ]);
        $parser->addSubcommand('checkTimeouts', [
            'help' => 'Check if any timeouts match',
        ]);
        $parser->addSubcommand('reset', [
            'help' => 'Resets the database, truncates all data. Use -q to skip confirmation.',
        ]);

        return $parser;
    }
}
