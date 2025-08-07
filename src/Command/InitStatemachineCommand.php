<?php

namespace StateMachine\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use StateMachine\FacadeAwareTrait;
use StateMachine\StateMachineConfig;

class InitStatemachineCommand extends Command
{
    use FacadeAwareTrait;

    public static function defaultName(): string {
        return 'state_machine init';
    }

    /**
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     *
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Checks timeouts of a state machine');

        $parser->addArgument('stateMachineName', [
            'help' => 'Name of the state machine',
            'required' => true,
        ]);

        $parser->addOption('overwrite', [
            'short' => 'o',
            'help' => 'Overwrite existing state machine files',
            'boolean' => true,
            'default' => false,
        ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        /** @var string $stateMachineName */
        $stateMachineName = $args->getArgument('stateMachineName');
        $stateMachine = Inflector::camelize($stateMachineName);

        $io->out('Initialize state machine ' . $stateMachine . ':');
        $processName = $stateMachine . '01';
        $config = new StateMachineConfig();
        $pathToXml = $config->getPathToStateMachineXmlFiles() . $stateMachine . DS;
        $filePath = $pathToXml . $processName . '.xml';
        if (!$args->getOption('overwrite') && file_exists($filePath)) {
            return $io->error(sprintf('State machine `%s` already exists. Use a different name.', $stateMachine));
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

        return static::CODE_SUCCESS;
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
