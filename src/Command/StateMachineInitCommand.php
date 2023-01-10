<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use StateMachine\StateMachineConfig;

/**
 * Command class for initializing.
 */
class StateMachineInitCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'state_machine init';
    }

    /**
     * @param \Cake\Console\Arguments $args
     * @param \Cake\Console\ConsoleIo $io
     *
     * @return int|null|void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $stateMachine = (string)$args->getArgumentAt(0);
        $stateMachine = Inflector::camelize($stateMachine);

        $io->out('Initialize state machine ' . $stateMachine . ':');
        $processName = $stateMachine . '01';
        $config = new StateMachineConfig();
        $pathToXml = $config->getPathToStateMachineXmlFiles() . $stateMachine . DS;
        $filePath = $pathToXml . $processName . '.xml';
        if (!$args->getOption('overwrite') && file_exists($filePath)) {
            $io->err(sprintf('State machine `%s` already exists. Use a different name.', $stateMachine));
            $this->abort();
        }

        if (!is_dir($pathToXml)) {
            mkdir($pathToXml, 0770, true);
        }
        $xml = $this->xml($stateMachine, $processName);
        file_put_contents($filePath, $xml);
        $io->out('- created ' . $filePath);

        $io->out('Initialize handler class ' . $stateMachine . 'StateMachineHandler:');

        $pathToPhp = APP . 'StateMachine' . DS;
        $filePath = $pathToPhp . $stateMachine . 'StateMachineHandler.php';
        if (!is_dir($pathToPhp)) {
            mkdir($pathToPhp, 0770, true);
        }
        $php = $this->handler($stateMachine, $processName);
        file_put_contents($filePath, $php);
        $io->out('- created ' . $filePath);

        $io->out('Enable it through config and you can then modify this file and verify the changes in real time in the admin backend.');
    }

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return string
     */
    protected function xml(string $stateMachineName, string $processName): string
    {
        $namespace = Inflector::dasherize(Configure::read('App.namespace'));
        $processSlug = Inflector::dasherize($stateMachineName) . '-01';
        $url = '../../../vendor/spryker/cakephp-statemachine/config/state-machine-01.xsd';

        return <<<XML
<?xml version="1.0"?>
<statemachine xmlns="$namespace:$processSlug" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="$namespace:$processSlug $url">

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
