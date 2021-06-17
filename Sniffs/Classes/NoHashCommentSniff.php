<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoHashCommentSniff implements Sniff
{
    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register(): array
    {
        return array(T_COMMENT);
    }

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (mb_strpos($tokens[$stackPtr]['content'], '#', 0, 'utf-8') !== 0) {
            return;
        }

        if (PHP_VERSION_ID >= 80000 && preg_match('/^#\[.*]/', $tokens[$stackPtr]['content']) === 0) {
            return;
        }

        $error = 'Hash comments are prohibited; found %s';
        $data = array(trim($tokens[$stackPtr]['content']));
        $phpcsFile->addError($error, $stackPtr, 'Found', $data);
    }
}
