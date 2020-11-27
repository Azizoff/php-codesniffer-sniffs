<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class InvalidTypeVariableOrderPHPDocSniff implements Sniff
{
    const AVAILABLE_TAGS = [
        '@method',
        '@param',
        '@property',
        '@property-read',
        '@property-write',
        '@return',
        '@var',
    ];

    const VALID_TYPES = [
        'int',
        'bool',
        'float',
        'null',
        'mixed',
        'string',
        'array',
        'int[]',
        'bool[]',
        'float[]',
        'mixed[]',
        'string[]',
        'array[]',
    ];

    const ALTERNATIVE_TYPES = [
        'integer',
        'boolean',
        'double',
    ];

    public function register(): array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     *
     * @return int|void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $commentToken = $tokens[$stackPtr];
        $tagName = $commentToken['content'];
        if (!in_array($tagName, self::AVAILABLE_TAGS, true)) {
            return;
        }

        $infoPtr = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $stackPtr + 1);
        if (false === $infoPtr) {
            return;
        }

        $infoToken = $tokens[$infoPtr];

        if ($infoToken['line'] !== $commentToken['line']) {
            return;
        }

        $content = $infoToken['content'];

        if (is_string($content)) {
            if (strpos($content, '$') !== 0) {
                return;
            }

            if (!preg_match(
                '/^(?<variable>(?:\$[a-z0-9_]+))\s(?<type>[a-z]+(?:\[\])?(?:\|[a-z]+(?:\[\])?)*)(?:\s.*)?$/iu',
                $content,
                $matches
            )) {
                return;
            }

            $variable = $matches['variable'];

            $type = $matches['type'];

            $types = explode('|', $type);

            if (empty(array_intersect($types, self::VALID_TYPES))
                && empty(array_intersect($types, self::ALTERNATIVE_TYPES))
            ) {
                return;
            }

            $error = 'Invalid type and variable order found "%s %s" use "%s %s" instead';

            $data = [
                $variable,
                $type,
                $type,
                $variable,
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
            if ($fix) {
                $phpcsFile->fixer->replaceToken(
                    $infoPtr,
                    str_replace(
                        implode(' ', [$variable, $type]),
                        implode(' ', [$type, $variable]),
                        $content
                    )
                );
            }
        }
    }
}
