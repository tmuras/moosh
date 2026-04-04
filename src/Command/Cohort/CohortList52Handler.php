<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cohort;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CohortList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display IDs only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $verbose->step('Fetching cohorts');

        $sql = "SELECT c.*, (SELECT COUNT(*) FROM {cohort_members} cm WHERE cm.cohortid = c.id) AS members
                  FROM {cohort} c
                 ORDER BY c.name";
        $cohorts = $DB->get_records_sql($sql);

        if (empty($cohorts)) {
            $output->writeln('No cohorts found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $cohorts, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'name', 'idnumber', 'contextid', 'visible', 'members'];
        $rows = [];
        foreach ($cohorts as $c) {
            $rows[] = [$c->id, $c->name, $c->idnumber ?? '', $c->contextid, $c->visible, $c->members];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
