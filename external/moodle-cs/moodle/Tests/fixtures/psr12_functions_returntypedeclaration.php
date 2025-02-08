<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// All the return types in this class are correct.
class correct_methods {
    public function no_return_type() {
        return;
    }

    public function return_void(): void {
        return null;
    }

    public function return_type(): int {
        return 1;
    }

    public function return_type_nullable(): ?int {
        return 1;
    }

    public function return_type_union(): int|string|null {
        return null;
    }
}

// All the return types in this class have problems..
class incorrect_methods {

    public function return_wrong_colon_many_spacing()   : int {
        return 1;
    }

    public function return_wrong_type_many_spacing():   int {
        return 1;
    }

    public function return_wrong_type_no_spacing():int {
        return 1;
    }

    // Note this corresponds to the PSR12.Functions.NullableTypeDeclaration Sniff, so it's correct for this fixture.
    public function return_wrong_nullable_many_spacing(): ?   int {
        return 1;
    }
}
