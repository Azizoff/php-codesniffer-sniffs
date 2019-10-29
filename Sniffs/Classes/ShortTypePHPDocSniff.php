<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ShortTypePHPDocSniff implements Sniff
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

    const INVALID_TYPES = [
        'integer' => 'int',
        'boolean' => 'bool',
        'double'  => 'float',
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
            $parts = array_filter(explode(' ', $content, 3));
            $parts = array_merge(
                ...array_map(
                    static function ($item) {
                        return explode('|', $item);
                    },
                    array_slice($parts, 0, 2)
                )
            );

            $wrong = array_values(array_intersect(array_keys(self::INVALID_TYPES), $parts));
            if ($wrong !== []) {
                $error = 'Invalid type name %s found, use %s instead';
                $type = array_first($wrong);
                $data = [
                    $type,
                    self::INVALID_TYPES[$type],
                ];
                $phpcsFile->addError($error, $stackPtr, 'Found', $data);
            }
        }
    }
}
