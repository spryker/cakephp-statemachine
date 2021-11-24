<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Illuminator\Task;

use Cake\Utility\Xml;
use RuntimeException;
use StateMachine\StateMachineConfig;

/**
 * Reads the states of the matching XML and sets the class constants here to be used inside code.
 */
class EventTask extends StateMachineHandlerTask
{
    /**
     * @var string
     */
    public const PREFIX = 'EVENT_';

    /**
     * @param string $content
     * @param string $path Path to file.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function run(string $content, string $path): string
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

        $xml = Xml::build($pathToXml, ['readFile' => true]);
        $elements = $this->getElements(Xml::toArray($xml), 'event');

        $file = $this->getFile('', $content);

        $classIndex = $file->findNext(T_CLASS, 0);
        if (!$classIndex) {
            return $content;
        }
        $tokens = $file->getTokens();

        $existingConstants = $this->getConstants($tokens, $tokens[$classIndex]['scope_opener'], $tokens[$classIndex]['scope_closer']);
        if ($existingConstants) {
            $elements = array_diff_key($elements, $existingConstants);
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

        return $this->addClassConstants($file, $elements, $index, $addToExisting, 0) ?: $content;
    }
}
