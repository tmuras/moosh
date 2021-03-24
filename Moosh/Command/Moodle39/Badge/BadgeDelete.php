<?php
/**
 * Deletes badges by sql criteria
 * moosh badge-delete [-n, --no-action] <criteria>
 *
 * Show all badges without deleting:
 * @example moosh badge-delete --no-action "1 = 1"
 *
 * Delete badge with id=4:
 * @example moosh badge-delete "id = 4"
 *
 * Delete all badges with courseid=433 and status=0:
 * @example moosh badge-delete "courseid=433 AND status=0"
 *
 * @copyright  2021 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Moodle39\Badge;
use Moosh\MooshCommand;

class BadgeDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'badge');

        $this->addArgument('criteria');
        $this->addOption('n|no-action', "Don't delete records, just show what is to be removed");

        $this->minArguments = 1;
        $this->maxArguments = 1;
    }

    public function execute()
    {
        global $DB;
        global $CFG;
        $criteria = $this->arguments[0];
        $do_action = !($this->parsedOptions->has('no-action'));

        require_once($CFG->dirroot.'/lib/badgeslib.php');

        $sql = 'SELECT * FROM {badge} WHERE '.$criteria;
        $badge_records = $DB->get_records_sql($sql);

        if (empty($badge_records)){
            cli_error("Error: no badges with criteria='$criteria' found");
        }

        $count = 0;
        foreach ($badge_records as &$badge_record){
            //print_r($badge_record);

            $badge = new \badge($badge_record->id);  //if invalid badge then print_error('error:nosuchbadge', 'badges', $badgeid);

            $message = $this->getMessage($badge_record);

            if ($do_action){
                $badge->delete(false); //can throw...
                echo "Deleted: $message\n";

                $count++;
                continue;
            }

            echo "$message\n";
            $count++;
        }

        $this->echoSummary($count, $do_action);
        exit(0);
    }

    private function getMessage($badge_record) : string {
        global $DB;

        $fullname = $badge_record->name;
        $name = $this->stringShortening($fullname);

        $fulldescription = $badge_record->description;
        $description = $this->stringShortening($fulldescription);

        $userdate = userdate($badge_record->timecreated, get_string('strftimedatetime', 'core_langconfig'));

        $userrec = $DB->get_record('user', array('id' => $badge_record->usercreated));
        $usercreated = "$userrec->firstname $userrec->lastname";

        return "$badge_record->id\t$name\t$description\t$userdate\t$usercreated";
    }

    private function echoSummary(int $count, bool $do_action){
        if ($count == 1)
            $badge_name = 'badge';
        else
            $badge_name = 'badges';

        if ($do_action)
            echo "Deleted $count $badge_name\n";
        else
            echo "Ready to delete $count $badge_name\n";
    }

    private function stringShortening(string $string) : string {
        $length = 21;
        return strlen($string) > $length ? substr($string,0,$length)."..." : $string;
    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
            "\n\tcriteria - a string with SQL fragment that selects the records from ".
            "\n\t\tmdl_bagdes table. The same idea as with moosh user-list command.".
            "\n\nOUTPUT:".
            "\n\tBadge:".
            "\n\t\tid\tname(21)\tdesc(21)\ttimecreated\tusercreated";
    }
}
