<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

class constructor_with_mixed_property_promotion {
    /**
     * Constructor
     *
     * @param string $serverurl a Moodle URL
     * @param string $token the token used to do the web service call
     * @param string $example
     * @param string $example2
     * @param string $example3
     */
    public function __construct(
        $serverurl,
        string $token,
        /** @var string The example */
        protected string $example,
        string $example2,
        protected string $example3
    ) {
    }
}
