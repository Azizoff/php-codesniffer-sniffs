<?php

namespace Azizov\CodeSniffer\Sniffs\Classes;


use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ExitConstructionsFoundSniff implements Sniff
{
    /**
     * @return array(int)
     */
    public function register(): array
    {
        return array(T_EXIT);
    }

    /**
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token in the
     * stack passed in $tokens.
     *
     * @return void
     * @todo Улучшить реализацию определения функций, текущий вариант может выдавать ложно-положительные результаты
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];
        if (strtolower($content) === 'die') {
            $error = 'Exit function found %s';
            $data = array(trim($content));
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }
    }
}
