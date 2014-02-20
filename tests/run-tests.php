<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../includes/functions.php'; // everything is hardcoded for now

function get_commands_list($moodle_ver) {
    run_external_command("cp $moodle_ver.sh config.sh", "Couldn't copy tests config file");
    $file = file_get_contents("config.sh");

    preg_match("/(?<=MOODLEDIR=)(.*)/", $file, $moodledir);
    exec("cd $moodledir[0] && moosh", $output);

    $commands_list = array();

    foreach ($output as $line) {
        preg_match("/(?<=\t)(.*)/", $line, $commands_list[]);
    }
    return $commands_list;
};

function run_tests(Array $commands) {

	$results = array();
    foreach ($commands as $command) {

        if ($command == null) { // skip empty lines
            continue;
        }
        exec("./$command[0].sh", $output, $ret);     

        if ($ret == 0) {
            $results[$command[0]] = "pass";
        } else {
            $results[$command[0]] = "fail";
        }
    }
    return $results;
};

$table = '---
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
		<th>Moodle 2.6</th>
		<th>Moodle 2.5</th>
		<th>Moodle 2.3</th>
		<th>Moodle 2.2</th>
		<th>Moodle 2.1</th>
		<th>Moodle 1.9</th>
	  </tr>
	</thead>
	<tbody>';

$all_commands = get_commands_list("moodle26"); // this is ugly, disregard
$moodle26 = run_tests(get_commands_list("moodle26"));
$moodle25 = run_tests(get_commands_list("moodle25"));

$moodle_ver = array($moodle26,
	$moodle25);

foreach ($all_commands as $command) {

	if ($command == null) {
        continue;
    }

	$table .= "<tr><td>$command[0]</td>";

	foreach ($moodle_ver as $moodle) {
		if (array_key_exists($command[0], $moodle)) {
			$result = $moodle[$command[0]];

			if ($result == "pass") {
				$table .= "<td><i class=\"fa fa-check\"></i></td>\n";
			} else {
				$table .= "<td><i class=\"fa fa-times\"></i></td>\n";
			}
		} else {
			$table .= "<td></td>";
		}
	}
	$table .= "</tr>";
}

$table .= "	</tbody>
	</table>
	</div>";

file_put_contents("../../jekyll/ci/index.md", $table);