<?php
/**
 * !!! Auto generated file. Do not directly modify this file. !!!
 * You can either version control this or generate the file on the fly prior to usage/deployment.
 */

namespace StateMachine\Dto\StateMachine;

/**
 * StateMachine/Process DTO
 *
 * @property string|null $processName
 * @property string|null $stateMachineName
 */
class ProcessDto extends \CakeDto\Dto\AbstractDto {

	public const FIELD_PROCESS_NAME = 'processName';
	public const FIELD_STATE_MACHINE_NAME = 'stateMachineName';

	/**
	 * @var string|null
	 */
	protected $processName;

	/**
	 * @var string|null
	 */
	protected $stateMachineName;

	/**
	 * Some data is only for debugging for now.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	protected array $_metadata = [
		'processName' => [
			'name' => 'processName',
			'type' => 'string',
			'required' => false,
			'defaultValue' => null,
			'dto' => null,
			'collectionType' => null,
			'associative' => false,
			'key' => null,
			'serialize' => null,
			'factory' => null,
		],
		'stateMachineName' => [
			'name' => 'stateMachineName',
			'type' => 'string',
			'required' => false,
			'defaultValue' => null,
			'dto' => null,
			'collectionType' => null,
			'associative' => false,
			'key' => null,
			'serialize' => null,
			'factory' => null,
		],
	];

	/**
	* @var array<string, array<string, string>>
	*/
	protected array $_keyMap = [
		'underscored' => [
			'process_name' => 'processName',
			'state_machine_name' => 'stateMachineName',
		],
		'dashed' => [
			'process-name' => 'processName',
			'state-machine-name' => 'stateMachineName',
		],
	];

	/**
	 * @param string|null $processName
	 *
	 * @return $this
	 */
	public function setProcessName(?string $processName) {
		$this->processName = $processName;
		$this->_touchedFields[self::FIELD_PROCESS_NAME] = true;

		return $this;
	}

	/**
	 * @param string $processName
	 *
	 * @throws \RuntimeException If value is not present.
	 *
	 * @return $this
	 */
	public function setProcessNameOrFail(string $processName) {
		$this->processName = $processName;
		$this->_touchedFields[self::FIELD_PROCESS_NAME] = true;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getProcessName(): ?string {
		return $this->processName;
	}

	/**
	 * @throws \RuntimeException If value is not set.
	 *
	 * @return string
	 */
	public function getProcessNameOrFail(): string {
		if ($this->processName === null) {
			throw new \RuntimeException('Value not set for field `processName` (expected to be not null)');
		}

		return $this->processName;
	}

	/**
	 * @return bool
	 */
	public function hasProcessName(): bool {
		return $this->processName !== null;
	}

	/**
	 * @param string|null $stateMachineName
	 *
	 * @return $this
	 */
	public function setStateMachineName(?string $stateMachineName) {
		$this->stateMachineName = $stateMachineName;
		$this->_touchedFields[self::FIELD_STATE_MACHINE_NAME] = true;

		return $this;
	}

	/**
	 * @param string $stateMachineName
	 *
	 * @throws \RuntimeException If value is not present.
	 *
	 * @return $this
	 */
	public function setStateMachineNameOrFail(string $stateMachineName) {
		$this->stateMachineName = $stateMachineName;
		$this->_touchedFields[self::FIELD_STATE_MACHINE_NAME] = true;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getStateMachineName(): ?string {
		return $this->stateMachineName;
	}

	/**
	 * @throws \RuntimeException If value is not set.
	 *
	 * @return string
	 */
	public function getStateMachineNameOrFail(): string {
		if ($this->stateMachineName === null) {
			throw new \RuntimeException('Value not set for field `stateMachineName` (expected to be not null)');
		}

		return $this->stateMachineName;
	}

	/**
	 * @return bool
	 */
	public function hasStateMachineName(): bool {
		return $this->stateMachineName !== null;
	}

}
