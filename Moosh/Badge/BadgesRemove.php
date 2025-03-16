<?php
/**
 * Deletes badges by SQL criteria.
 *
 * @copyright  2021 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jakub Kleban <jakub.kleban@enovation.ie>
 * @introduced 2021-10-15
 */

namespace Moosh\Badge;

/**
 * Class to handle Badges removing.
 *
 * Functionality:
 * - Remove badges by SQL criteria
 * - List all badges to be removed
 * - Keep Badge with the smallest ID from SQL criteria.
 */
class BadgesRemove
{
    private $sqlcriteria;
    private $noaction;

    public function __construct($sqlcriteria, $noaction = false) {
        $this->sqlcriteria = $sqlcriteria;
        $this->noaction = $noaction;
    }

    /**
     * Remove badges
     */
    public function remove($keepfirst = false) {
        global $DB;
        global $CFG;

        require_once($CFG->dirroot.'/lib/badgeslib.php');

        $sql = 'SELECT * FROM {badge} WHERE ' . $this->sqlcriteria;
        $badge_records = $DB->get_recordset_sql($sql);

        if ($keepfirst) {
            $index = min(array_keys($badge_records));
            unset($badge_records[$index]);
        }

        if (empty($badge_records)) {
            cli_error("Error: no badges with criteria='$this->sqlcriteria' found");
        }

        $count = 0;
        foreach ($badge_records as $badge_record){
            $badge = new \badge($badge_record->id);  //if invalid badge then print_error('error:nosuchbadge', 'badges', $badgeid);

            $message = $this->getMessage($badge_record);

            if (!$this->noaction){
                $badge->delete(false); //can throw...
                echo "Deleted: $message\n";

                $count++;
                continue;
            }

            echo "$message\n";
            $count++;
        }

        $this->echoSummary($count);
    }

    /**
     * Get information about badge
     *
     * @param $badge_record badge Badge object
     * @return string Informations about that badge
     * @throws \coding_exception
     */
    private function getMessage($badge_record) : string {
        global $DB;

        $fullname = $badge_record->name;
        $name = $this->stringShortening($fullname);

        $fulldescription = $badge_record->description;
        $description = $this->stringShortening($fulldescription);

        $userdate = userdate($badge_record->timecreated, get_string('strftimedatetime', 'core_langconfig'));

        if ($DB->record_exists('user', ['id' => $badge_record->usercreated])) {
            $userrec = $DB->get_record('user', array('id' => $badge_record->usercreated));
            $usercreated = "$userrec->firstname $userrec->lastname";
        } else {
            $usercreated = $badge_record->usercreated;
        }


        return "$badge_record->id\t$name\t$description\t$userdate\t$usercreated";
    }

    /**
     * Print summary at the end
     *
     * @param int $count numbers of badges
     */
    private function echoSummary(int $count){
        if ($count == 1)
            $badge_name = 'badge';
        else
            $badge_name = 'badges';

        if ($this->noaction)
            echo "Ready to delete $count $badge_name\n";
        else
            echo "Deleted $count $badge_name\n";
    }

    /**
     * If the string is longer than 21 chars, make it shorter.
     *
     * @param string $string String to process
     * @return string Shorter version with dots at the end
     */
    private function stringShortening(string $string) : string {
        $length = 21;
        return strlen($string) > $length ? substr($string,0,$length)."..." : $string;
    }
}