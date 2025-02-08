<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// All the return types in this class are correct.
class correct_methods {
    public function return_type_nullable_ok(): ?int {
        return 1;
    }
}

function return_type_nullable_ok(): ?int {
    return 1;
}

// All the return types in this class have problems..
class incorrect_methods {
    public function return_type_nullable_wrong(): ? int {
        return 1;
    }
}

function all_type_nullable_wrong(? int $one, ? int $two): ? int {
    return 1;
}
