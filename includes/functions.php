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