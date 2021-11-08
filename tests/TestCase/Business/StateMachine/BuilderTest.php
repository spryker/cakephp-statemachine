<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\EventInterface;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\ProcessInterface;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\StateInterface;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\Process\TransitionInterface;
use StateMachine\Business\StateMachine\Builder;
use StateMachine\Business\StateMachine\BuilderInterface;
use StateMachine\Dto\StateMachine\ProcessDto;
use StateMachine\StateMachineConfig;

class BuilderTest extends TestCase
{
    /**
     * @var int
     */
    protected const STATES_COUNT = 14;

    /**
     * @var string
     */
    protected const STATE_NAME = 'completed';

    /**
     * @var int
     */
    protected const TRANSITION_COUNT = 22;

    /**
     * @var int
     */
    protected const SUBPROCESSES_COUNT = 2;

    /**
     * @var string
     */
    protected const STATEMACHINE_NAME = 'TestingSm';

    /**
     * @var string
     */
    protected const PROCESS_NAME = 'TestProcess';

    /**
     * @return void
     */
    public function testCreateProcessShouldReturnProcessInstance(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $process = $builder->createProcess($processDto);

        $this->assertInstanceOf(ProcessInterface::class, $process);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllStatesFromXml(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $process = $builder->createProcess($processDto);

        $this->assertCount(static::STATES_COUNT, $process->getStates());
        $this->assertInstanceOf(State::class, $process->getStates()[static::STATE_NAME]);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllTransitions(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $process = $builder->createProcess($processDto);

        $this->assertCount(static::TRANSITION_COUNT, $process->getTransitions());
        $this->assertInstanceOf(Transition::class, $process->getTransitions()[0]);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllSubProcesses(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $process = $builder->createProcess($processDto);

        $this->assertCount(static::SUBPROCESSES_COUNT, $process->getSubProcesses());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldFlagMainProcess(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $process = $builder->createProcess($processDto);

        $this->assertTrue($process->getIsMain());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldThrowExceptionWhenStateMachineXmlFileNotFound(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $processDto->setStateMachineName('Random');

        $this->expectException(StateMachineException::class);

        $builder->createProcess($processDto);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldThrowExceptionWhenProcessXmlFileNotFound(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $processDto->setProcessName('Random');

        $this->expectException(StateMachineException::class);

        $builder->createProcess($processDto);
    }

    /**
     * @return void
     */
    public function testSubProcessPrefixIsApplied(): void
    {
        $builder = $this->createBuilder();
        $processDto = $this->createProcessDto();
        $process = $builder->createProcess($processDto);

        $manualEventsBySource = $process->getManuallyExecutableEventsBySource();

        $this->assertSame('Foo 1 - action', $manualEventsBySource['Foo 1 - sub process state'][0]);
        $this->assertSame('Leave Sub-process 2', $manualEventsBySource['Foo 1 - done'][0]);
    }

    /**
     * @return \StateMachine\Business\StateMachine\BuilderInterface
     */
    protected function createBuilder(): BuilderInterface
    {
        return new Builder(
            $this->createEvent(),
            $this->createState(),
            $this->createTransition(),
            $this->createProcess(),
            $this->createStateMachineConfig(),
        );
    }

    /**
     * @return \StateMachine\Business\Process\EventInterface
     */
    protected function createEvent(): EventInterface
    {
        return new Event();
    }

    /**
     * @return \StateMachine\Business\Process\StateInterface
     */
    protected function createState(): StateInterface
    {
        return new State();
    }

    /**
     * @return \StateMachine\Business\Process\TransitionInterface
     */
    protected function createTransition(): TransitionInterface
    {
        return new Transition();
    }

    /**
     * @return \StateMachine\Business\Process\ProcessInterface
     */
    protected function createProcess(): ProcessInterface
    {
        return new Process();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\StateMachineConfig
     */
    protected function createStateMachineConfig(): StateMachineConfig
    {
        $stateMachineConfigMock = $this->getMockBuilder(StateMachineConfig::class)->getMock();

        $pathToStateMachineFixtures = realpath(__DIR__ . '/../../../test_files') . DIRECTORY_SEPARATOR;
        $stateMachineConfigMock->method('getPathToStateMachineXmlFiles')->willReturn($pathToStateMachineFixtures);
        $stateMachineConfigMock->method('getSubProcessPrefixDelimiter')->willReturn(' - ');

        return $stateMachineConfigMock;
    }

    /**
     * @return \StateMachine\Dto\StateMachine\ProcessDto
     */
    protected function createProcessDto(): ProcessDto
    {
        $processDto = new ProcessDto();
        $processDto->setProcessName(static::PROCESS_NAME);
        $processDto->setStateMachineName(static::STATEMACHINE_NAME);

        return $processDto;
    }
}
