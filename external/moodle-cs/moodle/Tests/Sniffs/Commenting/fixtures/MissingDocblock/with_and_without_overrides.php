<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;


/**
 * Class level docblock.
 */
class base_class {
    #[\Override]
    public function has_override(): void {}

    public function no_override(): void {}
}

/**
 * Base interface.
 */
interface base_interface {
    #[\Override]
    public function has_override(): void {}

    public function no_override(): void {}
}

/**
 * Class which extends another class.
 */
class child_class extends base_class {
    #[\Override]
    public function has_override(): void {}

    public function no_override(): void {}
}

/**
 * Interface which extends another interface.
 */
interface child_interface extends base_interface {
    #[\Override]
    public function has_override(): void {}

    public function no_override(): void {}
}

/**
 * Class which implements an interface.
 */
class child_class_implements_interface implements base_interface {
    #[\Override]
    public function has_override(): void {}

    public function no_override(): void {}
}
