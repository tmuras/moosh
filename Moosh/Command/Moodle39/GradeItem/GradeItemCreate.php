<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\GradeItem;
use Moosh\MooshCommand;

use GetOptionKit\Argument;

class GradeItemCreate extends MooshCommand {
    public function __construct() {

        parent::__construct('create', 'gradeitem');

        $this->addOption('t|itemtype:', 'mod/manual/"" etc', 'manual');
        $this->addOption('n|itemname:', 'item name', 'Grade');
        $this->addOption('m|grademax:', 'maximum grade', '100');
        $this->addOption('g|gradetype:', 'grade type (0 = none, 1 = value, 2 = scale, 3 = text)', '1');
        $this->addOption('c|calculation:', 'grade calculation from other items', null);
        $this->addOption('o|options:', 'any other options that should be passed for grade item creation', null);

        $this->addArgument('courseid');
        $this->addArgument('categoryid');

        $this->minArguments = 2;
    }

    public function execute() {
        global $CFG, $DB;

        require_once($CFG->libdir . '/grade/grade_item.php');
        require_once($CFG->libdir . '/gradelib.php');


        $itemdata = new \stdClass();
        $itemdata->courseid = $this->arguments[0];
        $itemdata->categoryid = $this->arguments[1];
        ## $itemdata->itemtype = 'manual';

        $options = $this->expandedOptions;

        foreach ($options as $k => $v) {
            if ($k == 'options' && !empty($v)) {
                $item_options = preg_split( '/\s+(?=--)/', $v);
                foreach ( $item_options as $option ) {
                    $arg = new Argument( $option );
                    $name = $this->getOptionName($arg);
                    $value = $arg->getOptionValue();
                    $itemdata->$name = $value;
                }
            }
            else { $itemdata->$k = $v; }
        }

        // $params = NULL;

        $grade_item = new \grade_item($itemdata, false);

        if ($this->verbose) {
            echo print_r($itemdata) . "\n";
        }
        $source = 'manual';
        $grade_item->insert($source);

        echo $grade_item->id . "\n";
    }

    private function getOptionName($arg)
    {
        if (preg_match('/^[-]+([_a-zA-Z0-9-]+)/', $arg->arg, $regs)) {
            return $regs[1];
        }
    }
}
