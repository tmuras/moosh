#!/usr/bin/php
<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../includes/functions.php'; // everything is hardcoded for now

function get_commands_list($moodle_ver) {
    run_external_command("cp ../scripts/config$moodle_ver.sh config.sh", "Couldn't copy tests config file");
    $file = file_get_contents("config.sh");

    $moosh = __DIR__ . '/../../moosh.php';
    preg_match("/(?<=MOODLEDIR=)(.*)/", $file, $moodledir);
    exec("cd $moodledir[0] && $moosh", $output);

    $commands_list = array();

    foreach ($output as $line) {
        if(preg_match("/(?<=\t)(.*)/", $line, $result)) {
            $commands_list[] = $result[0];
        }
    }
    return $commands_list;
}

function run_tests(array $commands) {

    $results = array();
    foreach ($commands as $command) {

        if ($command == null) { // skip empty lines
            continue;
        }

        //check if test exists for command
        $testfile = $command .'.sh';

        if(!file_exists($testfile)) {
            echo "No test for ". $command. "\n";
            $results[$command] = "unknown";
            continue;
        }

        $output = NULL;
        echo "Executing '$testfile' in ". getcwd() ."\n";
        exec("./$testfile", $output, $ret);
        echo "Return: $ret\n";

        if($ret == 127) {
            die("File not found? That should not happen.\n");
        }

        if ($ret == 0) {
            $results[$command] = "pass";
        } else {
            $results[$command] = "fail";
            var_dump($output);
            echo "\n";
            die();
            continue;
        }
    }
    return $results;
};


$support_versions = array('27');
chdir('../commands');

$out = '---
title: CI
layout: default
---

CI
========
';
$out .= '<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th>Function</th>
        ';

foreach ($support_versions as $version) {
    $out .= "\t\t<th>Moodle " . $version . "</th>";
} 
$out .='</tr>
    </thead>
    <tbody>
    ';

$all_commands = get_commands_list("27"); // this is ugly, disregard

$results = array();
foreach($support_versions as $version) {
    $results[$version] = array();
    foreach($all_commands as $command) {
        $results[$version][$command] = 'not implemented';
    }
}

/*
echo "tests for moodle 2.6\n";
$moodle26 = run_tests(get_commands_list("26"));
foreach($moodle26 as $k=>$v) {
    $results['26'][$k] = $v;
}

echo "tests for moodle 2.5\n";
$moodle25 = run_tests(get_commands_list("25"));
foreach($moodle25 as $k=>$v) {
    $results['25'][$k] = $v;
}
*/
echo "tests for moodle 2.7\n";
$moodle27 = run_tests(get_commands_list("27"));
foreach($moodle27 as $k=>$v) {
    $results['27'][$k] = $v;
}

sort($all_commands);


foreach ($all_commands as $command) {

	$out .= "\t<tr>\n\t\t<td><a href=\"/commands/#$command \">$command</td>\n";


	foreach ($support_versions as $moodle) {
        //if($results[$moodle][])
        // $out .=  '<td>' .$results[$moodle][$command] .'</td>';
        $result = $results[$moodle][$command];
		if ($result == "pass") {
			$out .= "\t\t<td><i class=\"fa fa-check\"></i></td>\n";
		} else if ($result == "fail") {
			$out .= "\t\t<td><i class=\"fa fa-times\"></i></td>\n";
		} else {
            $out .= "\t\t<td><i class=\"fa fa-ban\"></i></td>\n";
        }


	}
    $out .= "\t</tr>\n";
}

$out .= "</tbody>
	</table>
	</div>";

// add footer

$out .= '
=======
Table legend:
<div class="table-responsive">
<table>
    <tr>
        <td><i class="fa fa-check"></td>
        <td>Test passed</td>
    </tr>
    <tr>
        <td><i class="fa fa-times"></td>
        <td>Test failed</td>
    </tr>
    <tr>
        <td><i class="fa fa-ban"></td>
        <td>Test not provided</td>
    </tr>
</table></div>
';

file_put_contents("../../www/ci/index.md", $out);
