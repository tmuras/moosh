<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

class class_only {
    /** @var string Thing to do */
    protected string $thing;

    /** @var string OTher thing to do */
    protected string $otherthing;

    /** @var integer Moodle prefers int */
    protected int $moodleprefersint;

    /** @var int|string Union types are supported */
    protected int|string $uniontypes;

    /** @var int Docblock followed by an attribute is fine. */
    #[Example()]
    protected int  $attributeafterdocblock;

    /** @see Something See tag but no var */
    protected int $seetagnovar;

    protected string $hasothertag;

    /** @var \moodle_page A class var */
    protected \moodle_page $page;

    /** @var \core\formatting A compound member var */
    protected \core\formatting $formatter;

    /** @var \core\formatting A var without a type eclaration */
    protected $noTypeDeclaration;

    /** @var INT An uppercase var */
    protected int $uppercasevar;

    /** @var */
    protected array $varwithouttype;

    /** @var array */
    protected array $varwithoutdescription;
}
