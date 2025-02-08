<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

/**
 * No docblocks on constructor.
 */
class NoDocblockOnConstructor {
    public function __constructor() {
        return;
    }
}

class DocBlockOnNonConstructor {
    public function __constructor() {
        return;
    }

    /**
     * @return void
     */
    public function non__constructor() {
        return;
    }
}

class DocBlockOnConstructorNoReturn {
    /**
     * Example constructor docblock
     *
     * @param string $example
     */
    public function __constructor(string $example) {
        return;
    }
}

class DocBlockOnConstructorHasReturn {
    /**
     * Example constructor docblock
     *
     * @param string $example Some value
     * @return void
     * @todo This is a todo
     */
    public function __constructor(string $example) {
        return;
    }
}

class DocBlockOnConstructorHasInlineReturn {
    /** @return self */
    public function __constructor(string $example) {
        return;
    }
}

class DocBlockOnConstructorHasNearlyInlineReturn {
    /**
     * @return self */
    public function __constructor(string $example) {
        return;
    }
}

class DocBlockOnConstructorHasReturnNoExtraTag {
    /**
     * Example constructor docblock
     *
     * @param string $example Some value
     * @return void
     */
    public function __constructor(string $example) {
        return;
    }
}
