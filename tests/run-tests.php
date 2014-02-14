<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../includes/functions.php'; // everything is hardcoded for now

exec("cd /var/www && moosh", $output);

$commands = array();

foreach ($output as $line) {
	preg_match("/(?<=\t)(.*)/", $line, $commands[]);
}

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

foreach ($commands as $command) {

	if ($command == null) {
		continue;
	}

	exec("./$command[0].sh", $output, $ret);

	if ($ret == 0) {
		$tests .= "<tr>
					<td>$command[0]</td>
					<td><i class=\"fa fa-check\"></i></td>
				</tr>\n";
	} else {
		$tests .= "<tr>
					<td>$command[0]</td>
					<td><i class=\"fa fa-times\"></i></td>
				</tr>\n";
	}
}

$tests .= "	</tbody>
	</table>
	</div>";

file_put_contents("../../jekyll/ci/index.md", $tests);