<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Block;
use Moosh\MooshCommand;

class BlockManage extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('manage', 'block');

        $this->addArgument('action');
        $this->addArgument('blockname');
        $this->addOption('f|force', 'force delete of block from disk');

    }

    public function execute()
    {
        global $DB;

        $action = $this->arguments[0];
        $blockname = $this->arguments[1]; // name of the block (in English)

        // Does block exists?
        if (!empty($blockname)) {
            if (!$block = $DB->get_record('block', array('name'=>$blockname))) {
                print_error('blockdoesnotexist', 'error');
            }
        }

        switch ($action) {
        case 'show':
            $DB->set_field('block', 'visible', '1', array('id'=>$block->id));      // Hide block
            break;

        case 'hide':
            $DB->set_field('block', 'visible', '0', array('id'=>$block->id));      // Hide block
            break;

        case 'delete':
            // Delete block from DB. Should we also delete it from disk?
            if ($this->expandedOptions['force']) {
                // Delete block from disk too!
            }
            break;
        }

    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
                "\n\taction[show|hide|delete] blockname";
                //"\n\n\t-f|--force delete block from disk too";
    }
}
