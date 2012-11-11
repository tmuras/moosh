<?php
/**
 * @license GPL v3
 */

function home_dir()
{
    // getenv('HOME') isn't set on windows and generates a Notice.
    $home = getenv('HOME');
    if (empty($home)) {
        if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        }
    }
    return empty($home) ? NULL : $home;
}


/**
 * Write error notification
 * @param $text
 * @return void
 */
function cli_problem($text) {
    fwrite(STDERR, $text."\n");
}

/**
 * Write to standard out and error with exit in error.
 *
 * @param string $text
 * @param int $errorcode
 * @return void (does not return)
 */
function cli_error($text, $errorcode=1) {
    fwrite(STDERR, $text);
    fwrite(STDERR, "\n");
    die($errorcode);
}
