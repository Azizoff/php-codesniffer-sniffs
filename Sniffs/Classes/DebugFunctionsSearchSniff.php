<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DebugFunctionsSearchSniff implements Sniff
{
    /**
     * @return array(int)
     */
    public function register(): array
    {
        return array(T_STRING);
    }

    /**
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token in the
     * stack passed in $tokens.
     *
     * @todo Improve the implementation, current version may produce false positive results.
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];
        if (in_array(strtolower($content), ['var_dump', 'dd', 'ddd', 'print_r', 'debug_print_backtrace', 'dump'], true)) {
            $error = 'Debug function found %s';
            $data = array(trim($content));
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }
    }
}
