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
            $this->abort(sprintf('State machine "%s" already exists. Use a different name.', $stateMachine));
        }

        if (!is_dir($pathToXml)) {
            mkdir($pathToXml, 0770, true);
        }
        $xml = $this->xml($processName);
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
            $this->abort(sprintf('State machine "%s" was not found.', $stateMachineName));
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

    /**
     * @param string $processName
     *
     * @return string
     */
    protected function xml(string $processName): string
    {
        return <<<XML
<?xml version="1.0"?>
<statemachine xmlns="http://static.spryker.com/release-app/state-machine-01.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema">

    <process name="$processName" main="true">
        <states>
            <state name="init" display="initialized"/>
        </states>

        <transitions>
        </transitions>

        <events>
        </events>

    </process>

</statemachine>

XML;
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return string
     */
    protected function handler(string $stateMachineName, string $processName): string
    {
        return <<<PHP
<?php

namespace App\StateMachine;

use StateMachine\Dependency\StateMachineHandlerInterface;
use StateMachine\Dto\StateMachine\ItemDto;

class {$stateMachineName}StateMachineHandler implements StateMachineHandlerInterface
{
    public const NAME = '$stateMachineName';

    public const STATE_INIT = 'init';

    /**
     * {@inheritDoc]
     *
     * @return string[]
     */
    public function getCommands(): array
    {
        return [
        ];
    }

    /**
     * {@inheritDoc]
     *
     * @return string[]
     */
    public function getConditions(): array
    {
        return [
        ];
    }

    /**
     * {@inheritDoc]
     *
     * @return string
     */
    public function getStateMachineName(): string
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @return string[]
     */
    public function getActiveProcesses(): array
    {
        return [
            '$processName',
        ];
    }

        /**
     * {@inheritDoc]
     *
     * @param string \$processName
     *
     * @return string
     */
    public function getInitialStateForProcess(\$processName): string
    {
        return static::STATE_INIT;
    }

    /**
     * {@inheritDoc]
     *
     * @param \StateMachine\Dto\StateMachine\ItemDto \$itemDto
     *
     * @return bool
     */
    public function itemStateUpdated(ItemDto \$itemDto): bool
    {
        return true;
    }

    /**
     * {@inheritDoc]
     *
     * @param int[] \$stateIds
     *
     * @return \StateMachine\Dto\StateMachine\ItemDto[]
     */
    public function getStateMachineItemsByStateIds(array \$stateIds = []): array
    {
        return [];
    }
}

PHP;
    }
}
