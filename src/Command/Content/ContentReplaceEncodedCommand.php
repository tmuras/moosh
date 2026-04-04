<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Content;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContentReplaceEncodedCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('content:replace-encoded')
            ->setDescription('Find and replace text inside base64-encoded serialized data')
            ->setHelp(<<<'HELP'
                Searches a specific database table and column for base64-encoded serialized
                PHP data, decodes it, performs string replacement within the object properties,
                then re-encodes and writes it back.

                This is useful for fixing URLs or text inside encoded configuration or content
                blocks that are not reachable by a plain-text database search.

                Examples:
                  content:replace-encoded "http://old.example.com" "https://new.example.com" block_instances configdata
                  content:replace-encoded "old text" "new text" customfield_data value --run
                HELP);
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $v): BaseHandler
    {
        return new ContentReplaceEncoded51Handler();
    }
}
