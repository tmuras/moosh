<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

if (1=1) {
    $somevar = true;
  $anothervar = true; // Bad aligned PHP.
      $anothervar = true; // Bad aligned PHP.
    // Next lines (PHP close, inline HTML and PHP start should be skipped.
?>
<div>
    <p>
        <span>some page content</span>
    </p>
</div>
<?php
    // Back to work, incorrect indenting should be detected again.
    $somevar = true;
  $anothervar = true; // Bad aligned PHP.
      $anothervar = true; // Bad aligned PHP.
}
// This must not throw any indentation error, and running the Sniff
// under "exact mode" causes that, so we need to run it in unexact mode.
// Note 8 spaces indentation is the correct one for any line wrap, both
// for normal and control structure lines. (Thanks Tim for remembering it here).
if ($condition) {
    execute_one_function_having_a_very_long_description('and a lot of params', $like, $these,
            $causing, $us, to $split);
}
