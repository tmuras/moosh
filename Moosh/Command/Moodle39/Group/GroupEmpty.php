<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2022 onwards Florent Lartet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Group;
use Moosh\MooshCommand;

class GroupEmpty extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('empty', 'group');

        $this->addOption('g|group', 'argument will be id of group');
        $this->addOption('n|name', 'argument will be name of group or regexp of name');
        $this->addOption('i|idnumber', 'argument will be idnumber of group or regexp of idnumber');
        $this->addOption('f|fake', 'fake empty to check what will be done');

        $this->addArgument('arg');
        $this->maxArguments = 255;

    }

    protected function getArgumentsHelp() {
        $ret = "\nThis command removes all members of the targeted groups";
        $ret .= "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= implode(' ', $this->argumentNames);
        $ret .= "\n\tString representing the group under the chosen option mode,\n\tex: 1254\n\tex: groupname.*008";
        $ret .= "\n\n\tCommand supports multiple arguments";

        return $ret;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';
        require_once $CFG->dirroot . '/lib/grouplib.php';

        $options = $this->expandedOptions;
        foreach ($this->arguments as $arg) {
            $groups = [];
            echo "get all groups with filter: ";
            if (!empty($options['name'])) {
                $select = $DB->sql_like('name', ':pattern', false);
                $params = ['pattern' => $arg];
                $groups = $DB->get_records_select('groups', $select, $params, 'idnumber ASC', 'id,courseid,idnumber,name');
                echo 'name ilike '.$arg."\n";
            }
            elseif (!empty($options['idnumber'])) {
                $select = $DB->sql_like('idnumber', ':pattern', false);
                $params = ['pattern' => $arg];
                $groups = $DB->get_records_select('groups', $select, $params, 'idnumber ASC', 'id,courseid,idnumber,name');
                echo 'idnumber ilike '.$arg."\n";
            }
            elseif (!empty($options['group'])) {
                $groups = $DB->get_records('groups', array('id' => $arg), 'idnumber ASC', 'id,courseid,idnumber,name');
                echo 'id = '.$arg."\n";
            }
            echo "Found: ".count($groups)."\n";
            foreach ($groups as $group) {
                echo 'Processing '.$group->name.' id('.$group->id.'), courseid ('.$group->courseid.'), idnumber ('.$group->idnumber.")\n";
                $members = groups_get_members($group->id, 'u.id');
                foreach ($members as $member) {
                    echo "\tremove userid (".$member->id.') from groupid ('.$group->id.")\n";
                    if (empty($options['fake'])) {
                        groups_remove_member($group->id, $member->id);
                    }
                }
            }
        }
        return true;
    }
}
