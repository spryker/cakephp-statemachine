<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace StateMachine\Illuminator\Task;

use Cake\Utility\Inflector;
use IdeHelper\Annotator\Traits\FileTrait;
use IdeHelper\Illuminator\Task\AbstractTask;
use PHP_CodeSniffer\Files\File;
use RuntimeException;

abstract class StateMachineHandlerTask extends AbstractTask
{
    use FileTrait;

    /**
     * @var string
     */
    public const PREFIX = '';

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'visibility' => 'public',
    ];

    /**
     * @param string $path
     *
     * @return bool
     */
    public function shouldRun(string $path): bool
    {
        $className = pathinfo($path, PATHINFO_FILENAME);
        if (strpos($path, 'src' . DS . 'StateMachine' . DS) === false || substr($className, -strlen('StateMachineHandler')) !== 'StateMachineHandler') {
            return false;
        }

        return true;
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
            throw new RuntimeException('Cannot find namespace in Handler class.');
        }

        return $matches[1];
    }

    /**
     * @param array $xml
     * @param string $type
     *
     * @return array<string, array<string, string>>
     */
    protected function getElements(array $xml, string $type): array
    {
        $list = [];

        $xmlProcesses = $xml['statemachine']['process'];
        if (isset($xmlProcesses['@name'])) {
            $xmlProcesses = [$xmlProcesses];
        }

        foreach ($xmlProcesses as $xmlProcess) {
            if (empty($xmlProcess[$type . 's'][$type])) {
                continue;
            }

            $elements = $xmlProcess[$type . 's'][$type];

            foreach ($elements as $element) {
                if (empty($element['@name'])) {
                    continue;
                }

                /** @var string $name */
                $name = $element['@name'];
                $constant = strtoupper(Inflector::underscore(str_replace(' ', '-', $name)));
                $list[$name] = [
                    'name' => $name,
                    'constant' => static::PREFIX . $constant,
                ];
            }
        }

        return $list;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param array $elements
     * @param int $index Index of first token of previous line
     * @param bool $addToExisting
     * @param int $level
     *
     * @return string|null
     */
    protected function addClassConstants(File $file, array $elements, int $index, bool $addToExisting, int $level = 1): ?string
    {
        if (!$elements) {
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

        $fixer = $this->getFixer($file);

        $fixer->beginChangeset();

        if (!$addToExisting) {
            $fixer->addNewline($beginIndex);
        }

        foreach ($elements as $element) {
            $fixer->addContent($beginIndex, $whitespace . $visibility . 'const ' . $element['constant'] . ' = \'' . $element['name'] . '\';');
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
    protected function getConstants(array $tokens, int $startIndex, int $endIndex): array
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

            $pos = strpos($constant, '_') ?: 0;
            $prefix = substr($constant, 0, $pos) ?: '';
            if ($prefix . '_' !== static::PREFIX) {
                continue;
            }

            $value = $tokens[$index + 4]['content'];
            $name = substr($value, 1, -1);

            $constants[$name] = [
                'index' => $i,
                'prefix' => $prefix,
                'name' => $name,
                'constant' => $constant,
            ];
        }

        return $constants;
    }
}
