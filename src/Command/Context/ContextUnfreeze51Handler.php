<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Context;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * context:unfreeze implementation for Moodle 5.1.
 */
class ContextUnfreeze51Handler extends BaseHandler
{
    use ContextLevelTrait;

    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'contextid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Context ID(s), or instance ID(s) when --level is used',
        );

        $command->addOption(
            'level',
            null,
            InputOption::VALUE_REQUIRED,
            'Context level name (system, user, coursecat, course, module, block). When set, IDs are instance IDs.',
        );

        $command->addOption(
            'children',
            'c',
            InputOption::VALUE_NONE,
            'Also unfreeze all child contexts',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $ids = $input->getArgument('contextid');
        $level = $input->getOption('level');
        $withChildren = $input->getOption('children');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';

        // Resolve all target contexts.
        $contexts = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid ID: $id</error>");
                return Command::FAILURE;
            }

            try {
                $context = $this->resolveContext($id, $level);
            } catch (\InvalidArgumentException $e) {
                $output->writeln("<error>{$e->getMessage()}</error>");
                return Command::FAILURE;
            } catch (\Throwable $e) {
                $output->writeln("<error>Context not found for ID $id" . ($level ? " (level: $level)" : '') . ".</error>");
                return Command::FAILURE;
            }

            $contexts[] = $context;

            if ($withChildren) {
                $childIds = $this->getChildContextIds($context);
                foreach ($childIds as $childId) {
                    $contexts[] = \context::instance_by_id($childId, MUST_EXIST);
                }
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following contexts would be unfrozen (use --run to execute):</info>');
            foreach ($contexts as $context) {
                $locked = $context->locked ? 'currently frozen' : 'already unfrozen';
                $levelName = $this->getLevelName($context->contextlevel);
                $output->writeln("  ID={$context->id} ({$levelName}) {$context->get_context_name()} — {$locked}");
            }
            $output->writeln(sprintf('<info>Total: %d context(s)</info>', count($contexts)));
            return Command::SUCCESS;
        }

        $verbose->step('Unfreezing ' . count($contexts) . ' context(s)');
        $unfrozen = 0;
        $skipped = 0;

        foreach ($contexts as $context) {
            $levelName = $this->getLevelName($context->contextlevel);

            if (!$context->locked) {
                $verbose->info("Already unfrozen: ID={$context->id} ({$levelName}) {$context->get_context_name()}");
                $skipped++;
                continue;
            }

            $context->set_locked(false);
            $verbose->done("Unfrozen: ID={$context->id} ({$levelName}) {$context->get_context_name()}");
            $output->writeln("Unfrozen {$levelName} context ID={$context->id}: {$context->get_context_name()}");
            $unfrozen++;
        }

        $output->writeln(sprintf('<info>Done: %d unfrozen, %d already unfrozen.</info>', $unfrozen, $skipped));

        return Command::SUCCESS;
    }
}
