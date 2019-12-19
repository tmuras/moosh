<?php
/**
 * `moosh admin-login`
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Admin;
use Moosh\MooshCommand;

class AdminLogin extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('login', 'admin');
        $this->maxArguments = 2;
    }

    public function bootstrapLevel() {
      # set to no client. when CLI_SCRIPT is defined, moodle creates an empty session
      return self::$BOOTSTRAP_FULL_NOCLI;
    }

    public function execute() {
        admin_login("verbose");
    }
}
