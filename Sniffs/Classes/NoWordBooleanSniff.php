<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoWordBooleanSniff implements Sniff
{
    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register(): array
    {
        return array(T_LOGICAL_OR, T_LOGICAL_AND);
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
        if (in_array(strtolower($tokens[$stackPtr]['content']), ['or', 'and'], true)) {
            $error = 'Logical operator is prohibited; found "%s"';
            $data = array(trim($tokens[$stackPtr]['content']));
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }
    }
}
