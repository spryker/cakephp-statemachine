<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace StateMachine\Test\TestCase\Business\StateMachine;

use Cake\TestSuite\TestCase;
use StateMachine\Business\Exception\StateMachineException;
use StateMachine\Business\Process\Event;
use StateMachine\Business\Process\Process;
use StateMachine\Business\Process\State;
use StateMachine\Business\Process\Transition;
use StateMachine\Business\StateMachine\Builder;
use StateMachine\StateMachineConfig;
use StateMachine\Transfer\StateMachineProcessTransfer;

class BuilderTest extends TestCase
{
    protected const STATES_COUNT = 14;
    protected const STATE_NAME = 'complete';

    protected const TRANSITION_COUNT = 12;

    protected const SUBPROCESSES_COUNT = 2;

    protected const STATEMACHINE_NAME = 'TestingSm';
    protected const PROCESS_NAME = 'TestProcess';

    /**
     * @return void
     */
    public function testCreateProcessShouldReturnProcessInstance(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertInstanceOf(Process::class, $process);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllStatesFromXml(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertCount(static::STATES_COUNT, $process->getStates());
        $this->assertInstanceOf(State::class, $process->getStates()[static::STATE_NAME]);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllTransitions(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertCount(static::TRANSITION_COUNT, $process->getTransitions());
        $this->assertInstanceOf(Transition::class, $process->getTransitions()[0]);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllSubProcesses(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertCount(static::SUBPROCESSES_COUNT, $process->getSubProcesses());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldFlagMainProcess(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertTrue($process->getIsMain());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldThrowExceptionWhenStateMachineXmlFileNotFound(): void
    {
        $this->expectException(StateMachineException::class);

        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $stateMachineProcessTransfer->setStateMachineName('Random');
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertTrue($process->getIsMain());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldThrowExceptionWhenProcessXmlFileNotFound(): void
    {
        $this->expectException(StateMachineException::class);

        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName('Random');
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertTrue($process->getIsMain());
    }

    /**
     * @return void
     */
    public function testSubProcessPrefixIsApplied(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $manualEventsBySource = $process->getManuallyExecutableEventsBySource();

        $this->assertEquals('Foo 1 - action', $manualEventsBySource['Foo 1 - sub process state'][0]);
        $this->assertEquals('Leave Sub-process 2', $manualEventsBySource['Foo 1 - done'][0]);
    }

    /**
     * @return \StateMachine\Business\StateMachine\Builder
     */
    protected function createBuilder(): Builder
    {
        return new Builder(
            $this->createEvent(),
            $this->createState(),
            $this->createTransition(),
            $this->createProcess(),
            $this->createStateMachineConfig()
        );
    }

    /**
     * @return \StateMachine\Business\Process\Event
     */
    protected function createEvent(): Event
    {
        return new Event();
    }

    /**
     * @return \StateMachine\Business\Process\State
     */
    protected function createState(): State
    {
        return new State();
    }

    /**
     * @return \StateMachine\Business\Process\Transition
     */
    protected function createTransition(): Transition
    {
        return new Transition();
    }

    /**
     * @return \StateMachine\Business\Process\Process
     */
    protected function createProcess(): Process
    {
        return new Process();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\StateMachine\StateMachineConfig
     */
    protected function createStateMachineConfig()
    {
        $stateMachineConfigMock = $this->getMockBuilder(StateMachineConfig::class)->getMock();

        $pathToStateMachineFixtures = realpath(__DIR__ . '/../../../test_files');
        $stateMachineConfigMock->method('getPathToStateMachineXmlFiles')->willReturn($pathToStateMachineFixtures);
        $stateMachineConfigMock->method('getSubProcessPrefixDelimiter')->willReturn(' - ');

        return $stateMachineConfigMock;
    }

    /**
     * @return \StateMachine\Transfer\StateMachineProcessTransfer
     */
    protected function createStateMachineProcessTransfer(): StateMachineProcessTransfer
    {
        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName(static::PROCESS_NAME);
        $stateMachineProcessTransfer->setStateMachineName(static::STATEMACHINE_NAME);

        return $stateMachineProcessTransfer;
    }
}
