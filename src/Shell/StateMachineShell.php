<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Utility\Inflector;
use StateMachine\FacadeAwareTrait;
use StateMachine\StateMachineConfig;

class StateMachineShell extends Shell
{
    use FacadeAwareTrait;

    /**
     * @return string
     */
    public static function defaultName(): string
    {
        return 'state_machine';
    }

    /**
     * @param string $stateMachine
     *
     * @return void
     */
    public function init(string $stateMachine): void
    {
        $stateMachine = Inflector::camelize($stateMachine);

        $this->out('Initialize state machine ' . $stateMachine . ':');
        $processName = $stateMachine . '01';
        $config = new StateMachineConfig();
        $pathToXml = $config->getPathToStateMachineXmlFiles() . $stateMachine . DS;
        $filePath = $pathToXml . $processName . '.xml';
        if (!$this->param('overwrite') && file_exists($filePath)) {
            $this->abort(sprintf('State machine `%s` already exists. Use a different name.', $stateMachine));
        }

        if (!is_dir($pathToXml)) {
            mkdir($pathToXml, 0770, true);
        }
        $xml = $this->xml($stateMachine, $processName);
        file_put_contents($filePath, $xml);
        $this->out('- created ' . $filePath);

        $this->out('Initialize handler class ' . $stateMachine . 'StateMachineHandler:');

        $pathToPhp = APP . 'StateMachine' . DS;
        $filePath = $pathToPhp . $stateMachine . 'StateMachineHandler.php';
        if (!is_dir($pathToPhp)) {
            mkdir($pathToPhp, 0770, true);
        }
        $php = $this->handler($stateMachine, $processName);
        file_put_contents($filePath, $php);
        $this->out('- created ' . $filePath);

        $this->out('Enable it through config and you can then modify this file and verify the changes in real time in the admin backend.');
    }

    /**
     * @param string $stateMachine
     *
     * @return void
     */
    public function checkTimeouts(string $stateMachine): void
    {
        $this->validateStateMachineName($stateMachine);

        $affected = $this->getFacade()->checkTimeouts($stateMachine);

        $this->verbose('Affected: ' . $affected);
    }

    /**
     * @param string $stateMachine
     *
     * @return void
     */
    public function checkConditions(string $stateMachine): void
    {
        $this->validateStateMachineName($stateMachine);

        $affected = $this->getFacade()->checkConditions($stateMachine);

        $this->verbose('Affected: ' . $affected);
    }

    /**
     * @return void
     */
    public function clearLocks(): void
    {
        $this->getFacade()->clearLocks();
    }

    /**
     * @param string $stateMachineName
     *
     * @return void
     */
    protected function validateStateMachineName(string $stateMachineName): void
    {
        if (!$this->getFacade()->stateMachineExists($stateMachineName)) {
            $this->abort(sprintf('State machine `%s` was not found.', $stateMachineName));
        }
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('init', [
            'help' => 'Initialize a new state machine XML.',
            'parser' => [
                'arguments' => [
                    'stateMachine' => [
                        'help' => 'State machine name',
                        'required' => true,
                    ],
                ],
                'options' => [
                    'overwrite' => [
                        'help' => 'Overwrite if it already exists.',
                        'boolean' => true,
                        'short' => 'o',
                    ],
                ],
            ],
        ]);

        $parser->addSubcommand('checkConditions', [
            'help' => 'Check if any conditions match.',
            'parser' => [
                'arguments' => [
                    'stateMachine' => [
                        'help' => 'State machine name',
                        'required' => true,
                    ],
                ],
            ],
        ]);

        $parser->addSubcommand('checkTimeouts', [
            'help' => 'Check if any timeouts match.',
            'parser' => [
                'arguments' => [
                    'stateMachine' => [
                        'help' => 'State machine name',
                        'required' => true,
                    ],
                ],
            ],
        ]);

        $parser->addSubcommand('clearLocks', [
            'help' => 'Clear expired locks from lock table.',
        ]);

        return $parser;
    }
}
