<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Audit;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Detect brute-force login attempts and potential breaches.
 *
 * Canonical name: audit:bruteforce  |  Aliases: audit-bruteforce, security-check-bruteforce
 */
class AuditBruteforceCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::FullNoAdminCheck;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('audit:bruteforce')
            ->setDescription('Detect brute-force login attempts and potential breaches')
            ->setHelp(<<<'HELP'
                Analyzes failed login attempts from the log store and cross-references
                with successful logins to detect potential brute-force attacks.

                Examples:
                  audit:bruteforce
                  audit:bruteforce --days=90 --min-attempts=5
                  audit:bruteforce --ip=192.168.1.100
                  audit:bruteforce --password-policy
                  audit:bruteforce --targeted-users -o csv
                  audit:bruteforce -o json
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
        return new AuditBruteforce52Handler();
    }
}
