<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Webservice;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Call a Moodle webservice function via REST.
 *
 * Canonical name: webservice:call  |  Alias: webservice-call
 */
class WebserviceCallCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Config;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('webservice:call')
            ->setDescription('Call a Moodle webservice function via REST')
            ->setHelp(<<<'HELP'
                Calls a Moodle webservice function using the REST protocol.
                Requires a valid webservice token.

                Parameters can be passed as key=value pairs after the function name,
                or as a raw query string with --raw-params.

                Examples:
                  webservice:call core_webservice_get_site_info --token abc123
                  webservice:call core_course_get_courses --token abc123 options[ids][0]=2
                  webservice:call core_user_get_users --token abc123 criteria[0][key]=email criteria[0][value]=admin@example.com
                  webservice:call core_course_get_courses --token abc123 --post
                  webservice:call core_webservice_get_site_info --token abc123 --format xml
                HELP);

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
        return new WebserviceCall52Handler();
    }
}
