#!/usr/bin/env php
<?php

require_once __DIR__ . '/../../includes/functions.php';

function assert_same($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, "$message\nExpected: " . var_export($expected, true)
            . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

function assert_moosh_cli_works($moodle_dir, $message) {
    $moosh = realpath(__DIR__ . '/../../moosh.php');
    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($moosh)
        . ' -p ' . escapeshellarg($moodle_dir) . ' --list-commands';
    exec($command, $output, $exit_code);
    if ($exit_code !== 0 || !in_array('course-list', $output, true)) {
        fwrite(STDERR, "$message\n");
        exit(1);
    }
}

function create_moodle_fixture($dir, $branch, $layout) {
    $moodle_dir = $layout === 'public' ? $dir . '/public' : $dir;
    mkdir($moodle_dir . '/install', 0777, true);
    mkdir($moodle_dir . '/lib', 0777, true);
    file_put_contents($moodle_dir . '/install.php', "<?php\n");
    file_put_contents($moodle_dir . '/version.php', "<?php\n\$branch = '$branch';\n");
    file_put_contents($dir . '/config.php', "<?php\n"
        . "\$CFG = new stdClass();\n"
        . "\$CFG->layout = '$layout';\n"
        . "require_once(__DIR__ . '/lib/setup.php');\n");

    if ($layout === 'public') {
        file_put_contents($moodle_dir . '/config.php', "<?php\n"
            . "require_once(__DIR__ . '/../config.php');\n");
    }

    return $moodle_dir;
}

function remove_fixture($dir) {
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        if (is_link($path)) {
            unlink($path);
        } else if (is_dir($path)) {
            remove_fixture($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

$fixture_root = sys_get_temp_dir() . '/moosh-directory-layout-' . bin2hex(random_bytes(8));
$legacy_root = $fixture_root . '/moodle45';
$legacy_public_root = $fixture_root . '/legacy-parent/public';
$legacy_special_root = $fixture_root . '/moodle 45;id;#';
$modern_root = $fixture_root . '/moodle52';

try {
    $legacy_dir = create_moodle_fixture($legacy_root, '405', 'legacy');
    $legacy_public_dir = create_moodle_fixture($legacy_public_root, '405', 'legacy-public-directory');
    $legacy_special_dir = create_moodle_fixture($legacy_special_root, '405', 'legacy-special-path');
    file_put_contents(dirname($legacy_public_root) . '/config.php', "<?php\n"
        . "\$CFG = new stdClass();\n"
        . "\$CFG->layout = 'wrong-parent';\n");
    $modern_dir = create_moodle_fixture($modern_root, '502', 'public');
    mkdir($legacy_dir . '/mod/example', 0777, true);
    mkdir($modern_dir . '/mod/example', 0777, true);
    mkdir($modern_root . '/scripts', 0777, true);

    assert_same($legacy_dir, find_top_moodle_dir($legacy_root),
        'Legacy Moodle root was not detected.');
    assert_same($legacy_dir, find_top_moodle_dir($legacy_dir . '/mod/example'),
        'Legacy Moodle root was not detected from a nested directory.');
    assert_same($modern_dir, find_top_moodle_dir($modern_root),
        'Modern Moodle public directory was not detected from the installation root.');
    assert_same($modern_dir, find_top_moodle_dir($modern_dir),
        'Modern Moodle public directory was not detected directly.');
    assert_same($modern_dir, find_top_moodle_dir($modern_dir . '/mod/example'),
        'Modern Moodle public directory was not detected from a nested directory.');
    assert_same($modern_dir, find_top_moodle_dir($modern_root . '/scripts'),
        'Modern Moodle public directory was not detected from an installation tool directory.');
    assert_same('405', moosh_moodle_version($legacy_dir),
        'Legacy Moodle version was not detected.');
    assert_same('502', moosh_moodle_version($modern_dir),
        'Modern Moodle version was not detected.');

    eval_config($legacy_dir);
    assert_same('legacy', $CFG->layout, 'Legacy config.php was not evaluated.');
    eval_config($legacy_public_dir);
    assert_same('legacy-public-directory', $CFG->layout,
        'A legacy Moodle installed in a directory named public used the wrong config.php.');
    eval_config($legacy_special_dir);
    assert_same('legacy-special-path', $CFG->layout,
        'A Moodle path containing spaces or shell metacharacters was not handled safely.');
    eval_config($modern_dir);
    assert_same('public', $CFG->layout, 'Modern config.php outside public was not evaluated.');

    assert_moosh_cli_works($legacy_root,
        'moosh CLI did not accept the legacy Moodle installation root.');
    assert_moosh_cli_works($legacy_special_root,
        'moosh CLI did not safely accept a legacy root containing shell metacharacters.');
    assert_moosh_cli_works($modern_root,
        'moosh CLI did not normalize the modern Moodle installation root.');
    assert_moosh_cli_works($modern_dir,
        'moosh CLI did not accept the modern Moodle public directory.');

    echo "Moodle directory layout tests passed.\n";
} finally {
    if (is_dir($fixture_root)) {
        remove_fixture($fixture_root);
    }
}
