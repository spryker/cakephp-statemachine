<?php

namespace StateMachine\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use StateMachine\FacadeAwareTrait;

class CheckTimeoutsStatemachineCommand extends Command
{
    use FacadeAwareTrait;

    public static function defaultName(): string {
        return 'state_machine check_conditions';
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

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        /** @var string $stateMachine */
        $stateMachine = $args->getArgument('stateMachineName');
        if (!$this->getFacade()->stateMachineExists($stateMachine)) {
            return $io->error(sprintf('State machine `%s` was not found.', $stateMachine));
        }

        $affected = $this->getFacade()->checkTimeouts($stateMachine);

        $io->verbose('Affected: ' . $affected);

        return static::CODE_SUCCESS;
    }
}
