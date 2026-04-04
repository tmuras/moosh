<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryExport51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Category ID to export (0=all)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $catId = (int) $input->getArgument('categoryid');

        if ($catId > 0) {
            $cat = $DB->get_record('course_categories', ['id' => $catId]);
            if (!$cat) {
                $output->writeln("<error>Category with ID $catId not found.</error>");
                return Command::FAILURE;
            }
        }

        $verbose->step('Exporting categories');

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<categories>\n";

        if ($catId === 0) {
            // Export all top-level categories and their children.
            $topCats = $DB->get_records('course_categories', ['parent' => 0], 'sortorder');
            foreach ($topCats as $cat) {
                $xml .= $this->exportCategory($cat, 1);
            }
        } else {
            $xml .= $this->exportCategory($cat, 1);
        }

        $xml .= "</categories>\n";

        echo $xml;

        return Command::SUCCESS;
    }

    private function exportCategory(object $cat, int $indent): string
    {
        global $DB;

        $pad = str_repeat('  ', $indent);
        $xml = $pad . "<category>\n";
        $xml .= $pad . "  <name>" . htmlspecialchars($cat->name, ENT_XML1) . "</name>\n";
        $xml .= $pad . "  <idnumber>" . htmlspecialchars($cat->idnumber ?? '', ENT_XML1) . "</idnumber>\n";
        $xml .= $pad . "  <description>" . htmlspecialchars($cat->description ?? '', ENT_XML1) . "</description>\n";
        $xml .= $pad . "  <visible>{$cat->visible}</visible>\n";

        $children = $DB->get_records('course_categories', ['parent' => $cat->id], 'sortorder');
        if (!empty($children)) {
            $xml .= $pad . "  <subcategories>\n";
            foreach ($children as $child) {
                $xml .= $this->exportCategory($child, $indent + 2);
            }
            $xml .= $pad . "  </subcategories>\n";
        }

        $xml .= $pad . "</category>\n";

        return $xml;
    }
}
