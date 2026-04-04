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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryImport52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to XML file')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Import under this parent category ID (0=top level)', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $filePath = $input->getArgument('file');
        $parentId = (int) $input->getOption('parent');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $xmlContent = file_get_contents($filePath);
        if (!$xmlContent) {
            $output->writeln("<error>Empty or unreadable file: $filePath</error>");
            return Command::FAILURE;
        }

        $xml = simplexml_load_string($xmlContent);
        if ($xml === false) {
            $output->writeln('<error>Invalid XML format.</error>');
            return Command::FAILURE;
        }

        // Count categories in the XML.
        $count = $this->countCategories($xml);

        if (!$runMode) {
            $output->writeln("<info>Dry run — would import $count category(ies) under parent $parentId (use --run to execute).</info>");
            $this->previewCategories($xml, $output, 0);
            return Command::SUCCESS;
        }

        $verbose->step("Importing categories from '$filePath'");

        require_once $CFG->dirroot . '/course/lib.php';

        $created = 0;
        $skipped = 0;

        foreach ($xml->category as $catNode) {
            $this->importCategory($catNode, $parentId, $created, $skipped, $verbose, $output);
        }

        fix_course_sortorder();
        $output->writeln("Imported $created category(ies), skipped $skipped existing.");

        return Command::SUCCESS;
    }

    private function importCategory(\SimpleXMLElement $node, int $parentId, int &$created, int &$skipped, VerboseLogger $verbose, OutputInterface $output): void
    {
        global $DB;

        $name = (string) $node->name;
        $idnumber = (string) $node->idnumber;
        $description = (string) $node->description;
        $visible = isset($node->visible) ? (int) $node->visible : 1;

        // Check if category already exists at this level.
        $existing = $DB->get_record('course_categories', ['name' => $name, 'parent' => $parentId]);
        if ($existing) {
            $verbose->info("Skipping existing category: $name (ID={$existing->id})");
            $skipped++;
            $newParentId = $existing->id;
        } else {
            $data = new \stdClass();
            $data->name = $name;
            $data->parent = $parentId;
            $data->description = $description;
            $data->descriptionformat = FORMAT_HTML;
            $data->visible = $visible;
            if (!empty($idnumber)) {
                $data->idnumber = $idnumber;
            }

            $newCat = \core_course_category::create($data);
            $verbose->info("Created category: $name (ID={$newCat->id})");
            $created++;
            $newParentId = $newCat->id;
        }

        // Process subcategories recursively.
        if (isset($node->subcategories)) {
            foreach ($node->subcategories->category as $childNode) {
                $this->importCategory($childNode, $newParentId, $created, $skipped, $verbose, $output);
            }
        }
    }

    private function countCategories(\SimpleXMLElement $xml): int
    {
        $count = 0;
        foreach ($xml->category as $cat) {
            $count++;
            if (isset($cat->subcategories)) {
                $count += $this->countCategoriesInNode($cat->subcategories);
            }
        }
        return $count;
    }

    private function countCategoriesInNode(\SimpleXMLElement $node): int
    {
        $count = 0;
        foreach ($node->category as $cat) {
            $count++;
            if (isset($cat->subcategories)) {
                $count += $this->countCategoriesInNode($cat->subcategories);
            }
        }
        return $count;
    }

    private function previewCategories(\SimpleXMLElement $xml, OutputInterface $output, int $depth): void
    {
        foreach ($xml->category as $cat) {
            $pad = str_repeat('  ', $depth);
            $output->writeln("  {$pad}" . (string) $cat->name);
            if (isset($cat->subcategories)) {
                $this->previewCategories($cat->subcategories, $output, $depth + 1);
            }
        }
    }
}
