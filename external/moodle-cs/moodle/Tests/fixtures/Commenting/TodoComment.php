<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// @phpcs:disable moodle.Commenting.InlineComment

// Using default settings: commentRequiredRegex = 'MDL-[0-9]

// TODO: This is the simplest TODO comment.

/** @todo This is also the simplest, but within a phpdoc block */

// TODO: Can be multi-line
// and spread over multiple lines
// without too much limit.

/** @todo Can also be multi-line
 * and spread over multiple lines
 * without too much limit.
 */

/**
 * @something Any tag.
 * @todo In the middle of a block (take 1).
 * @somethingelse Any tag.
 * @todo In the middle of a block (take 2)
 *
 * @todo In the middle of a block (take 3).
 */

// Using a custom regex: commentRequiredRegex = 'CONTRIB-[0-9]+|https'
// phpcs:set moodle.Commenting.TodoComment commentRequiredRegex CONTRIB-[0-9]+|https

// TODO: This is the simplest TODO comment.
/** @todo This is also the simplest, but within a phpdoc block */

// ============================================================================
// All cases below this line MUST BE CORRECT.
// ============================================================================

# This is not processed by the Sniff.

// Using a custom, empty regex: commentRequiredRegex = ''
// phpcs:set moodle.Commenting.TodoComment commentRequiredRegex

// TODO: This is the simplest TODO comment.
/** @todo This is also the simplest, but within a phpdoc block */

// Using a custom regex: commentRequiredRegex = 'CONTRIB-[0-9]+|https'
// phpcs:set moodle.Commenting.TodoComment commentRequiredRegex CONTRIB-[0-9]+|https

// TODO: This is the simplest TODO comment. CONTRIB-123

/** @todo This is also the simplest, but within a phpdoc block https://example.com */

// TODO: Can be multi-line
// and spread over multiple lines
// without too much limit. CONTRIB-123

/** @todo Can also be multi-line
 * and spread over multiple lines
 * without too much limit. https://example.com
 */

/**
 * @something Any tag.
 * @todo In the middle of a block (take 1). CONTRIB-123
 * @somethingelse Any tag.
 * @todo In the middle of a block (take 2) https://example.com
 *
 * @todo In the middle of a block (take 3).
 * CONTRIB-123
 */
