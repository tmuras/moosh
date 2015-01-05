<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

/// Three slashes are incorrect.

//// four are also wrong. Not to talk about the missing upper and final dot

//And no-space, uhm, bad, bad!

// None of the following phpdocs should be causing problems.

/** This is an interface comment */
interface commented_interface {}

/** This is a class comment */
class commented_class {

    /** A const comment */
    const commented = true;
    /** A private comment */
    private $aprivate = true;
    /** A protected comment */
    protected $aprotected = true;
    /** A public comment */
    public $apublic = true;
    /** A static comment */
    static $astatic = true;
    /** A var comment, this is wrong! */
    var $avar = true;

    /** A function comment */
    function afunction() {}

    /** An abstract comment */
    abstract function afunction() {}

    /** A final comment */
    final function afunction() {}

    /** A define comment */
    define('ADEFINE', true);
}

/** A defined comment, this is wrong! */
defined('ADEFINED', true);

// Comment separators are allowed if pure (from 20 to 120 chars). All below are correct.

// --------------------

// ------------------------------------------------------------------------------------------------------------------------

// But not if mixed with text or punctuations or smaller than 20 chars or after code. All below are wrong.

// -------------------

// -------------------------------------------------------------------------------------------------------------------------

// ---------- nonono ----------

// -----------.......----------

// .----------------------------

// ----------------------------.

// .---------------------------.

echo 'hello'; // --------------------------

echo 'hello'; // A--------------.
