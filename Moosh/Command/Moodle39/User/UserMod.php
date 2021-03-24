<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2017 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\User;
use Moosh\MooshCommand;

class UserMod extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('mod', 'user');

        $this->addOption('all', 'modify all users');
        $this->addOption('i|id', 'use id to match a user');

        $this->addOption('a|auth:', 'auth');
        $this->addOption('p|password:', 'password');
        $this->addOption('e|email:','email address');

        $this->addOption('g|global', 'user(s) to be set as global admin.', false);
        $this->addOption('n|ignorepolicy', 'ignore password policy.', false);

        $this->addArgument('user');
        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/user/lib.php';
        $options = $this->expandedOptions;

        if ($options['ignorepolicy']) {
            unset($CFG->passwordpolicy);
        }

        if ($this->parsedOptions->has('password') && !check_password_policy($this->parsedOptions['password']->value, $error)) {
            echo strip_tags($error) . " \n";
            exit(0);
        }

        if($options['all']) {
            //run on the whole mdl_user table

            $sql = "UPDATE {user} SET ";
            $sqlFragment = array();
            $parameters = array();
            //we want to use the options that were actually provided on the commandline
            if($this->parsedOptions->has('password')) {
                require_once($CFG->libdir . '/moodlelib.php');
                $sqlFragment[] = 'password = ?';
                $parameters['password'] = hash_internal_user_password($this->parsedOptions['password']->value);
            }
            if($this->parsedOptions->has('email')) {
                $sqlFragment[] = 'email = ?';
                $parameters['email'] = $this->parsedOptions['email']->value;
            }
            if($this->parsedOptions->has('auth')) {
                $sqlFragment[] = 'auth = ?';
                $parameters['auth'] = $this->parsedOptions['auth']->value;
            }

            if(count($sqlFragment) == 0) {
                cli_error('You need to provide at least one option for updating a profile field (password or email)');
            }
            $sql .= implode(' , ',$sqlFragment);
            $DB->execute($sql,$parameters);
            exit(0);
        }

        foreach ($this->arguments as $argument) {

            if($options['id']) {
                $user = $DB->get_record('user',array('id'=>$argument));
            } else {
                $user = $DB->get_record('user',array('username'=>$argument));
            }

            if(!$user) {
                cli_problem("User '$argument' not found'");
                continue;
            }

            if($this->parsedOptions->has('password')) {
                require_once($CFG->libdir . '/moodlelib.php');
                $user->password = hash_internal_user_password($this->parsedOptions['password']->value);
            }
            if($this->parsedOptions->has('email')) {
                $user->email = $this->parsedOptions['email']->value;
            }
            if($this->parsedOptions->has('auth')) {
                $user->auth = $this->parsedOptions['auth']->value;
            }

            if ($this->parsedOptions->has('global')) {
                foreach(explode(',', $CFG->siteadmins) as $admin) {
                    $admin = (int)$admin;
                    if ($admin) {
                        $admins[$admin] = $admin;
                    }
                }

                if (!isset($admins[$user->id])) {
                    $admins[$user->id]=$user->id;
                }
                set_config('siteadmins', implode(',', $admins));
                purge_all_caches();
            }

            echo $DB->update_record('user',$user) . "\n";
        }
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }
}
