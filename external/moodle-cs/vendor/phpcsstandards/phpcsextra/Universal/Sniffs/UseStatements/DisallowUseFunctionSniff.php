<?php
/**
 * PHPCSExtra, a collection of sniffs and standards for use with PHP_CodeSniffer.
 *
 * @package   PHPCSExtra
 * @copyright 2020 PHPCSExtra Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSExtra
 */

namespace PHPCSExtra\Universal\Sniffs\UseStatements;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\Namespaces;
use PHPCSUtils\Utils\UseStatements;

/**
 * Disallow function import `use` statements.
 *
 * Related sniffs:
 * - `Universal.UseStatements.DisallowUseClass`
 * - `Universal.UseStatements.DisallowUseConst`
 *
 * @since 1.0.0
 */
final class DisallowUseFunctionSniff implements Sniff
{

    /**
     * Name of the "Use import source" metric.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const METRIC_NAME_SRC = 'Use import statement source for functions';

    /**
     * Name of the "Use import with/without alias" metric.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const METRIC_NAME_ALIAS = 'Use import statement for functions';

    /**
     * Keep track of which file is being scanned.
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $currentFile = '';

    /**
     * Keep track of the current namespace.
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $currentNamespace = '';

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 1.0.0
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            \T_USE,
            \T_NAMESPACE,
        ];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $file = $phpcsFile->getFilename();
        if ($file !== $this->currentFile) {
            // Reset the current namespace for each new file.
            $this->currentFile      = $file;
            $this->currentNamespace = '';
        }

        $tokens = $phpcsFile->getTokens();

        // Get the name of the current namespace.
        if ($tokens[$stackPtr]['code'] === \T_NAMESPACE) {
            $namespaceName = Namespaces::getDeclaredName($phpcsFile, $stackPtr);
            if ($namespaceName !== false) {
                $this->currentNamespace = $namespaceName;
            }

            return;
        }

        // Ok, so this is a T_USE token.
        try {
            $statements = UseStatements::splitImportUseStatement($phpcsFile, $stackPtr);
        } catch (RuntimeException $e) {
            // Not an import use statement. Bow out.
            return;
        }

        if (empty($statements['function'])) {
            // No import statements for functions found.
            return;
        }

        $endOfStatement = $phpcsFile->findNext([\T_SEMICOLON, \T_CLOSE_TAG], ($stackPtr + 1));

        foreach ($statements['function'] as $alias => $fullName) {
            $reportPtr = $stackPtr;
            do {
                $reportPtr = $phpcsFile->findNext(\T_STRING, ($reportPtr + 1), $endOfStatement, false, $alias);
                if ($reportPtr === false) {
                    // Shouldn't be possible.
                    continue 2; // @codeCoverageIgnore
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($reportPtr + 1), $endOfStatement, true);
                if ($next !== false && $tokens[$next]['code'] === \T_NS_SEPARATOR) {
                    // Namespace level with same name. Continue searching.
                    continue;
                }

                break;
            } while (true);

            /*
             * Build the error message and code.
             *
             * Check whether this is a non-namespaced (global) import and check whether this is an
             * import from within the same namespace.
             *
             * Takes incorrect use statements with leading backslash into account.
             * Takes case-INsensitivity of namespaces names into account.
             *
             * The "GlobalNamespace" error code takes precedence over the "SameNamespace" error code
             * in case this is a non-namespaced file.
             */

            $error     = 'Use import statements for functions%s are not allowed.';
            $error    .= ' Found import statement for: "%s"';
            $errorCode = 'Found';
            $data      = [
                '',
                $fullName,
            ];

            $globalNamespace = false;
            $sameNamespace   = false;
            if (\strpos($fullName, '\\', 1) === false) {
                $globalNamespace = true;
                $errorCode       = 'FromGlobalNamespace';
                $data[0]         = ' from the global namespace';

                $phpcsFile->recordMetric($reportPtr, self::METRIC_NAME_SRC, 'global namespace');
            } elseif ($this->currentNamespace !== ''
                && (\stripos($fullName, $this->currentNamespace . '\\') === 0
                    || \stripos($fullName, '\\' . $this->currentNamespace . '\\') === 0)
            ) {
                $sameNamespace = true;
                $errorCode     = 'FromSameNamespace';
                $data[0]       = ' from the same namespace';

                $phpcsFile->recordMetric($reportPtr, self::METRIC_NAME_SRC, 'same namespace');
            } else {
                $phpcsFile->recordMetric($reportPtr, self::METRIC_NAME_SRC, 'different namespace');
            }

            $hasAlias = false;
            $lastLeaf = \strtolower(\substr($fullName, -(\strlen($alias) + 1)));
            $aliasLC  = \strtolower($alias);
            if ($lastLeaf !== $aliasLC && $lastLeaf !== '\\' . $aliasLC) {
                $hasAlias   = true;
                $error     .= ' with alias: "%s"';
                $errorCode .= 'WithAlias';
                $data[]     = $alias;

                $phpcsFile->recordMetric($reportPtr, self::METRIC_NAME_ALIAS, 'with alias');
            } else {
                $phpcsFile->recordMetric($reportPtr, self::METRIC_NAME_ALIAS, 'without alias');
            }

            if ($errorCode === 'Found') {
                $errorCode = 'FoundWithoutAlias';
            }

            $phpcsFile->addError($error, $reportPtr, $errorCode, $data);
        }
    }
}
