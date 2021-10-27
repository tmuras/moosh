<?php
/**
 * Detect Badge duplicates and delete them.
 * moosh badge-delete-duplicates [-n, --no-action]
 *
 * Just show all duplicates:
 * @example moosh badge-delete-duplicates --no-action
 *
 * Delete all duplicates:
 * @example moosh badge-delete-duplicates
 *
 * @copyright  2021 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jakub Kleban <jakub.kleban@enovation.ie>
 * @introduced 2021-10-15
 */

namespace Moosh\Command\Moodle39\Badge;
use Moosh\Badge\BadgesRemove;
use Moosh\MooshCommand;

class BadgeDeleteDuplicates extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete-duplicates', 'badge');

        $this->addOption('n|no-action', "Don't delete records, just show what is to be removed");
    }

    public function execute()
    {
        global $DB;
        $noaction = $this->parsedOptions->has('no-action');

        // Get duplicates.
        $duplicates = $DB->get_records_sql('SELECT MIN(id) AS num, count(*) as c, timecreated, courseid, usercreated FROM {badge} WHERE status=0 GROUP BY timecreated, courseid, usercreated having c > 1 ORDER BY c DESC');
        foreach($duplicates as $duplicate) {
            $sql = 'status=0 AND timecreated=' . $duplicate->timecreated . ' AND courseid=' . $duplicate->courseid;
            $badgesremover = new BadgesRemove($sql, $noaction);
            $badgesremover->remove(true); // Remove duplicates without badge with the smallest ID.
        }

        exit(0);
    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
            "\n\nOUTPUT:".
            "\n\tBadge:".
            "\n\t\tid\tname(21)\tdesc(21)\ttimecreated\tusercreated";
    }
}
