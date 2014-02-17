<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../includes/functions.php'; // everything is hardcoded for now

$tests = '---
title: CI
layout: default
---

CI
========
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
	<thead>
	  <tr>
		<th></th>
		<th>Moodle 1.9</th>
		<th>Moodle 2.1</th>
		<th>Moodle 2.2</th>
		<th>Moodle 2.3</th>
		<th>Moodle 2.5</th>
		<th>Moodle 2.6</th>
		<th>Moodle 2.7</th>
	  </tr>
	</thead>
	<tbody>';

$tests .= run_tests("moodle25");
$tests .= run_tests("moodle26");

$tests .= "	</tbody>
	</table>
	</div>";

var_dump($tests);

//file_put_contents("../../jekyll/ci/index.md", $tests);