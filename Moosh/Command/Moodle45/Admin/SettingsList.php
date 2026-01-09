<?php

namespace Moosh\Command\Moodle45\Admin;

use Moosh\MooshCommand;
use ReflectionClass;

class SettingsList extends MooshCommand {

    public function __construct() {
        parent::__construct('settings-list', 'admin');
    }

    public function execute() {
        global $CFG;

        // We make sure that the administration library is loaded
        if (file_exists($CFG->libdir . '/adminlib.php')) {
            require_once($CFG->libdir . '/adminlib.php');
        } else {
            die("adminlib.php cannot be found. Run the command inside the Moodle directory.\n");
        }

        // Exceptions
        $excludesetting = [
            'admin_setting_courselist_frontpage',
            'admin_setting_special_frontpagedesc',
            'admin_setting_sitesetselect',
            'admin_setting_sitesetcheckbox',
            'admin_setting_gradecat_combo',
            'admin_setting_users_with_capability'
        ];

        $classes = get_declared_classes();
        sort($classes);

        printf("%-50s | %s\n", "Class Name", "Constructor Parameters");
        echo str_repeat("-", 120) . "\n";

        foreach ($classes as $class) {
            if (strpos($class, 'admin_setting_') !== 0 || in_array($class, $excludesetting)) {
                continue;
            }

            try {
                $ref = new ReflectionClass($class);
                if ($ref->isAbstract()) {
                    continue;
                }

                $constructor = $ref->getConstructor();
                $params_list = [];

                if ($constructor) {
                    foreach ($constructor->getParameters() as $param) {
                        $p_name = '$' . $param->getName();
                        if ($param->isDefaultValueAvailable()) {
                            $def = $param->getDefaultValue();
                            $p_name .= ' = ' . $this->format_default($def);
                        }
                        $params_list[] = $p_name;
                    }
                }

                printf("%-50s | %s\n", $class, implode(', ', $params_list));

            } catch (\Exception $e) {
                continue;
            }
        }
    }

    private function format_default($def) {
        if (is_null($def)) return 'null';
        if (is_bool($def)) return $def ? 'true' : 'false';
        if (is_string($def)) return "'$def'";
        if (is_array($def)) return "array()";
        return (string)$def;
    }
}