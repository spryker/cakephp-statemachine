<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Illuminator\Task;

use Cake\Utility\Inflector;
use Cake\Utility\Xml;
use IdeHelper\Annotator\Traits\FileTrait;
use IdeHelper\Illuminator\Task\AbstractTask;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use RuntimeException;
use StateMachine\StateMachineConfig;

/**
 * Reads the states of the matching XML and sets the class constants here to be used inside code.
 */
class StateTask extends AbstractTask
{
    use FileTrait;

    public const PREFIX = 'STATE_';

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'visibility' => 'public',
    ];

    /**
     * @param string $path
     *
     * @return bool
     */
    public function shouldRun($path): bool
    {
        $className = pathinfo($path, PATHINFO_FILENAME);
        if (strpos($path, 'src' . DS . 'StateMachine' . DS) === false || substr($className, -strlen('StateMachineHandler')) !== 'StateMachineHandler') {
            return false;
        }

        return true;
    }

    /**
     * @param string $content
     * @param string $path Path to file.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function run($content, $path): string
    {
        $className = pathinfo($path, PATHINFO_FILENAME);

        $namespace = $this->getNamespace($content);
        $fullClassName = $namespace . '\\' . $className;
        if (!class_exists($fullClassName)) {
            throw new RuntimeException('Cannot load classname `' . $fullClassName . '`');
        }

        /** @var \StateMachine\Dependency\StateMachineHandlerInterface $handler */
        $handler = new $fullClassName();

        $stateMachineName = $handler->getStateMachineName();

        $processes = $handler->getActiveProcesses();
        $processName = array_pop($processes);

        $config = new StateMachineConfig();
        $pathToXml = $config->getPathToStateMachineXmlFiles() . $stateMachineName . DS . $processName . '.xml';
        if (!file_exists($pathToXml)) {
            throw new RuntimeException('XML Path not found: `' . $pathToXml . '`');
        }

        $xml = Xml::build($pathToXml);
        $states = $this->getStates(Xml::toArray($xml));

        $file = $this->_getFile('', $content);

        $classIndex = $file->findNext(T_CLASS, 0);
        if (!$classIndex) {
            return $content;
        }
        $tokens = $file->getTokens();

        $existingConstants = $this->getStateConstants($tokens, $tokens[$classIndex]['scope_opener'], $tokens[$classIndex]['scope_closer']);
        if ($existingConstants) {
            $states = array_diff_key($states, $existingConstants);
            $existingConstant = array_pop($existingConstants);
            $index = $existingConstant['index'];
            $addToExisting = true;
        } else {
            $index = $file->findPrevious(T_WHITESPACE, $tokens[$classIndex]['scope_closer'] + -1, $tokens[$classIndex]['scope_opener'], true);
            if ($index === false) {
                $index = $tokens[$classIndex]['scope_opener'];
            }
            $addToExisting = false;
        }

        return $this->addClassConstants($file, $states, $index, $addToExisting, 0) ?: $content;
    }

    /**
     * @param array $xml
     *
     * @return array
     */
    protected function getStates(array $xml): array
    {
        $stateToProcessMap = [];

        $xmlProcesses = $xml['statemachine']['process'];
        if (isset($xmlProcesses['@name'])) {
            $xmlProcesses = [$xmlProcesses];
        }

        foreach ($xmlProcesses as $xmlProcess) {
            if (empty($xmlProcess['states']['state'])) {
                continue;
            }

            $xmlStates = $xmlProcess['states']['state'];

            foreach ($xmlStates as $xmlState) {
                if (empty($xmlState['@name'])) {
                    continue;
                }

                $state = $xmlState['@name'];
                $constant = strtoupper(Inflector::underscore(str_replace(' ', '-', $state)));
                $stateToProcessMap[$state] = [
                    'name' => $state,
                    'constant' => static::PREFIX . $constant,
                ];
            }
        }

        return $stateToProcessMap;
    }

    /**
     * @param string $content
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function getNamespace(string $content): string
    {
        preg_match('#\bnamespace\s+(.+);#', $content, $matches);
        if (!$matches) {
            throw new RuntimeException('Cannot find namespace in handler class.');
        }

        return $matches[1];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param array $states
     * @param int $index Index of first token of previous line
     * @param bool $addToExisting
     * @param int $level
     *
     * @return string|null
     */
    protected function addClassConstants(File $file, array $states, int $index, bool $addToExisting, int $level = 1): ?string
    {
        if (!$states) {
            return null;
        }

        $tokens = $file->getTokens();

        $line = $tokens[$index]['line'];

        $i = $index;
        while ($tokens[$i + 1]['line'] === $line) {
            $i++;
        }

        $lastTokenOfLastLine = $i;

        $whitespace = '';
        $firstOfLine = $index;
        while ($tokens[$firstOfLine - 1]['line'] === $tokens[$index]['line']) {
            $firstOfLine--;
            $whitespace .= $tokens[$firstOfLine]['content'];
        }
        if ($level < 1) {
            $whitespace = str_repeat(' ', 4);
        }

        $beginIndex = $lastTokenOfLastLine;
        $visibility = $this->getConfig('visibility') ? $this->getConfig('visibility') . ' ' : '';

        $fixer = $this->_getFixer($file);

        $fixer->beginChangeset();

        if (!$addToExisting) {
            $fixer->addNewline($beginIndex);
        }

        foreach ($states as $state) {
            $fixer->addContent($beginIndex, $whitespace . $visibility . 'const ' . $state['constant'] . ' = \'' . $state['name'] . '\';');
            $fixer->addNewline($beginIndex);
        }

        $fixer->endChangeset();

        return $fixer->getContents();
    }

    /**
     * @param array $tokens
     * @param int $startIndex
     * @param int $endIndex
     *
     * @return array
     */
    protected function getStateConstants(array $tokens, int $startIndex, int $endIndex): array
    {
        $constants = [];

        for ($i = $startIndex + 1; $i < $endIndex; $i++) {
            if ($tokens[$i]['code'] !== T_CONST) {
                continue;
            }
            $index = $i + 1;
            if ($tokens[$index]['code'] === T_WHITESPACE) {
                $index++;
            }
            if ($tokens[$index]['code'] !== T_STRING) {
                continue;
            }

            $constant = $tokens[$index]['content'];

            $pos = strpos($constant, '_') ?: null;
            $prefix = substr($constant, 0, $pos);
            if ($prefix . '_' !== static::PREFIX) {
                continue;
            }

            $value = $tokens[$index + 4]['content'];
            $state = substr($value, 1, -1);

            $constants[$state] = [
                'index' => $i,
                'prefix' => $prefix,
                'name' => $state,
                'constant' => $constant,
            ];
        }

        return $constants;
    }
}
