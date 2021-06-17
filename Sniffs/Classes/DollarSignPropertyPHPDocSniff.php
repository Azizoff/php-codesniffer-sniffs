<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DollarSignPropertyPHPDocSniff implements Sniff
{
    const AVAILABLE_TAGS = [
        '@param',
        '@property',
        '@property-read',
        '@property-write',
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

            if (count($parts) > 1) {
                if (isset($parts[1]) && strpos($parts[1], '$') !== 0) {
                    $phpcsFile->addError('Use $ notation for variable %s', $stackPtr, 'Found', [$parts[1]]);
                }
            }
        }
    }
}
