<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// @phpcs:disable moodle.Commenting.InlineComment

// Using custom moodleTodoCommentRegex config setting.

// TODO: This is the simplest TODO comment.
/** @todo This is also the simplest, but within a phpdoc block */

// ============================================================================
// All cases below this line MUST BE CORRECT.
// ============================================================================

// TODO: This is the simplest TODO comment. CUSTOM-123
/** @todo This is also the simplest, but within a phpdoc block. CUSTOM-123 */
