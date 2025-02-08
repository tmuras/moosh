<?php

/**
 * Example class.
 */
#[Route()]
class Example {
    /** @var moodle_database $db */
    #[Inject(
        name: 'moodle_database'
    )]
    protected \moodle_database $db;

    /**
     * Do something with multiple attributes.
     */
    #[Attribute()]
    #[Attribute()]
    #[Attribute()]
    public function doSomething() {
        // Do something.
    }
}
