<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Bootstrap;

use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles Moodle require/login at the correct bootstrap level.
 *
 * Replaces the large switch/if block in the original moosh.php.
 */
final class MoodleBootstrapper
{
    private string $moodleDir;
    private MoodleVersion $version;
    private OutputInterface $output;
    private VerboseLogger $verbose;

    public function __construct(string $moodleDir, MoodleVersion $version, OutputInterface $output)
    {
        $this->moodleDir = $moodleDir;
        $this->version = $version;
        $this->output = $output;
        $this->verbose = new VerboseLogger($output);
    }

    public function getMoodleDir(): string
    {
        return $this->moodleDir;
    }

    public function getVersion(): MoodleVersion
    {
        return $this->version;
    }

    /**
     * Bootstrap Moodle to the requested level.
     *
     * @param BootstrapLevel $level   How deep to bootstrap.
     * @param string|null    $user    Username to log in as (null = admin).
     * @param bool           $noLogin Skip user login entirely.
     */
    public function bootstrap(
        BootstrapLevel $level,
        ?string $user = null,
        bool $noLogin = false,
    ): void {
        if ($level === BootstrapLevel::None) {
            $this->verbose->skip('Bootstrap level is None — skipping Moodle initialisation');
            return;
        }

        $this->verbose->section('Moodle Bootstrap');
        $this->verbose->detail('Level', $level->name);
        $this->verbose->detail('User', $user ?? 'admin (default)');
        $this->verbose->detail('No-login', $noLogin ? 'yes' : 'no');

        if ($level === BootstrapLevel::DbOnly) {
            $this->verbose->step('Loading database-only bootstrap');
            $this->bootstrapDbOnly();
            $this->verbose->done('Database connection established');
            $this->verbose->end();
            return;
        }

        $this->bootstrapFull($level, $user, $noLogin);
        $this->verbose->end();
    }

    /**
     * Minimal bootstrap: just enough to talk to the DB.
     */
    private function bootstrapDbOnly(): void
    {
        global $CFG;

        $configFile = $this->moodleDir . '/config.php';
        if (!file_exists($configFile)) {
            throw new \RuntimeException("config.php not found in {$this->moodleDir}");
        }

        $this->verbose->step('Defining constants: MOODLE_INTERNAL, ABORT_AFTER_CONFIG, CLI_SCRIPT');
        define('MOODLE_INTERNAL', true);
        define('ABORT_AFTER_CONFIG', true);
        define('CLI_SCRIPT', true);

        $this->verbose->step('Loading config.php');
        require_once($configFile);

        $libdir = $this->moodleDir . '/lib';
        $this->verbose->step('Loading database libraries (dmllib, setuplib, moodlelib, weblib)');
        require_once($libdir . '/dmllib.php');
        require_once($libdir . '/setuplib.php');
        require_once($libdir . '/moodlelib.php');
        require_once($libdir . '/weblib.php');

        $this->verbose->step('Calling setup_DB()');
        setup_DB();
    }

    /**
     * Full (or config-only) bootstrap via config.php / lib/setup.php.
     */
    private function bootstrapFull(
        BootstrapLevel $level,
        ?string $user,
        bool $noLogin,
    ): void {
        global $CFG;

        if ($level === BootstrapLevel::FullNoCli) {
            $this->verbose->step('Setting up browser-context server globals (FullNoCli)');
            $_SERVER['REMOTE_ADDR'] = 'localhost';
            $_SERVER['SERVER_PORT'] = 80;
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP 1.1';
            $_SERVER['SERVER_SOFTWARE'] = 'PHP/' . phpversion() . ' Development Server';
            $_SERVER['REQUEST_URI'] = '/';
            $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        } else {
            $this->verbose->step('Defining CLI_SCRIPT constant');
            if (!defined('CLI_SCRIPT')) {
                define('CLI_SCRIPT', true);
            }
        }

        if ($level === BootstrapLevel::Config) {
            $this->verbose->step('Defining ABORT_AFTER_CONFIG (config-only bootstrap)');
            if (!defined('ABORT_AFTER_CONFIG')) {
                define('ABORT_AFTER_CONFIG', true);
            }
        }

        if (!defined('MOODLE_INTERNAL')) {
            define('MOODLE_INTERNAL', true);
        }

        $this->verbose->step('Loading Moodle config.php');
        require_once($this->moodleDir . '/config.php');
        $this->verbose->done('config.php loaded — $CFG populated');

        $this->verbose->step('Enabling full debug output (E_ALL)');
        $CFG->debug = E_ALL;
        $CFG->debugdisplay = 1;
        @error_reporting(E_ALL);
        @ini_set('display_errors', '1');

        if (
            $level !== BootstrapLevel::Config
            && $level !== BootstrapLevel::FullNoAdminCheck
            && !$noLogin
        ) {
            $this->verbose->step('Logging in user: ' . ($user ?? 'admin'));
            $this->loginUser($user);
            $this->verbose->done('User session established');
        } else {
            $reason = match (true) {
                $level === BootstrapLevel::Config => 'config-only bootstrap',
                $level === BootstrapLevel::FullNoAdminCheck => 'no-admin-check mode',
                $noLogin => '--no-login flag',
            };
            $this->verbose->skip('Skipping user login — ' . $reason);
        }
    }

    /**
     * Log in as the given user (or admin if null).
     */
    private function loginUser(?string $user): void
    {
        global $DB;

        if ($user !== null) {
            $userRecord = $DB->get_record('user', ['username' => $user]);
            if (!$userRecord) {
                throw new \RuntimeException("User '$user' not found");
            }
        } else {
            $userRecord = get_admin();
            if (!$userRecord) {
                throw new \RuntimeException('No admin account found');
            }
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        \complete_user_login($userRecord);
    }
}
