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
use Moosh\Badge\BadgesRemove;
use Moosh\MooshCommand;

class BadgeDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'badge');

        $this->addArgument('sqlcriteria');
        $this->addOption('n|no-action', "Don't delete records, just show what is to be removed");

        $this->minArguments = 1;
        $this->maxArguments = 1;
    }

    public function execute()
    {
        $sqlcriteria = $this->arguments[0];
        $noaction = $this->parsedOptions->has('no-action');

        $badgeremover = new BadgesRemove($sqlcriteria, $noaction);
        $badgeremover->remove();

        exit(0);
    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
            "\n\tsqlcriteria - a string with SQL fragment that selects the records from ".
            "\n\t\tmdl_bagdes table. The same idea as with moosh user-list command.".
            "\n\nOUTPUT:".
            "\n\tBadge:".
            "\n\t\tid\tname(21)\tdesc(21)\ttimecreated\tusercreated";
    }
}
