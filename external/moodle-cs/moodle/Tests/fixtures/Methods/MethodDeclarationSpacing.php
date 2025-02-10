<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// All the methods in this class are correct.
class correct_methods {
    abstract public static function method1();

    final public static function method2() {
        return true;
    }

    public function method3() {
        return true;
    }

    public /* comment */ function method4() {
        // We don't do anything with with closures.
        $a = function () {
            return true;
        };
        return true;
    }
}

class nested_function_correct {
    public function getAnonymousClass() {
        return new class() {
            public function nested_function() {}
        };
    }
}

// We don't do anything with with global scope functions.
function   global_function  (  ) {
    return true;
}

// All the methods in this class are incorrect.
// (note that the MethodDeclarationSpacing DOES NOT fix all the
// keywords in this class, because some are handled by other sniffs.
// Take a look to the class documentation for more information).
class incorrect_methods {
    abstract  public  static  function  method3  (  );

    final  public  static  function  method2  (  ) {
        return  true;
    }

    public  function  method3  (  ) {
        return  true;
    }

    public function method4 chocolate() {
        return true;
    }
}

class nested_function_incorrect {
    public function getAnonymousClass() {
        return new class() {
            public function  nested_function  (  ) {}
        };
    }
}
