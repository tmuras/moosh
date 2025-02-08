<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

class type_check {
    /** @var INT An uppercase var */
    protected int $uppercasevar;

    /** @var String A string */
    protected string $casestring;

    /** @var double This should be a float */
    protected float $doublevar;

    /** @var real This should be a float */
    protected float $realvar;

    /** @var array This should be a array */
    protected array $anarray;

    /** @var ARRAY() This should be a array */
    protected array $anotherarray;

    /** @var string[] This should be a array */
    protected array $arrayofstrings;

    /** @var boolean This should be a bool */
    protected bool $somebool;

    /** @var boolean[] This should be a bool[] */
    protected array $arrayofbool;

    /** @var array(int => bool) Mapping of ints to bools */
    protected array $arrayofthings;

    /** @var array(int > bool) List of ints */
    protected array $arrayofints;

    /** @var array(int) List of ints */
    protected array $notalistarray;
}
