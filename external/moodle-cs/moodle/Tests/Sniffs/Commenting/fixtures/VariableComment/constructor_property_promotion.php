<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

class cpp_only {
    public function __construct(
        protected string $thing,
        /** @var string The other thing */
        public string $otherthing,
        /**
         * Yes or no?
         *
         * @var BOOLEAN
         */
        public bool $yesorno,
        /**
         * The page to do it on.
         *
         * @deprecated
         * @var \moodle_page
         */
        public \moodle_page $page,
        /** @var string An attribute */
        #[\Attribute]
        public string $attribute,
    ) {
        $this->attribute = 'example';
    }

    public function someStandardMethod(
        string $example,
        bool $otherExample,
        \moodle_page $page,
    ): void {
    }
}
