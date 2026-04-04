<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Log;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Restore IDs in a compact log export CSV using metadata.json.
 *
 * Canonical name: log:unpack  |  Alias: log-unpack
 */
class LogUnpackCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::None;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('log:unpack')
            ->setDescription('Restore IDs in a compact log export CSV using metadata.json')
            ->setHelp(
                "Reads a compact CSV (without id column) produced by log:export --compact\n" .
                "and the accompanying metadata.json, then writes a new CSV with IDs restored.\n\n" .
                "Examples:\n" .
                "  moosh log:unpack /tmp/logs.csv /tmp/logs_full.csv\n" .
                "  moosh log:unpack /tmp/compact/logs.csv /tmp/restored.csv"
            );

        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler
    {
        return $this->handler;
    }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $verbose->step('Delegating to handler: ' . get_class($this->handler));
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $moodleVersion): BaseHandler
    {
        return new LogUnpack52Handler();
    }
}
