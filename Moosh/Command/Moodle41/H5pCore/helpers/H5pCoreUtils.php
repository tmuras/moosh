<?php

namespace Moosh\Command\Moodle41\H5pCore;

/**
 * H5pCore utils
 * @author Michal Chruscielski <michalch775@gmail.com>
 */
class H5pCoreUtils
{
    /**
     * Check if `$haystack` ends with `$needle`. Alternative for php8 `str_ends_with`.
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function stringEndsWith($haystack, $needle) {
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }
}

