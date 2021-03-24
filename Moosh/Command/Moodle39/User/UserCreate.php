<?php
/**
 * moosh user-create [--password=<password> --email=<email>
 *                   --city=<city> --country=<CN>
 *                   --firstname=<firstname> --lastname=<lastname>
                     --department=<department> --institution=<institution>]
 *                   <username1> [<username2> ...]
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\User;
use Moosh\MooshCommand;

class UserCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'user');
        $this->addOption('a|auth:', 'authentication plugin, e.g. ldap');
        $this->addOption('p|password:', 'password (NONE for a blank password)');
        $this->addOption('e|email:','email address');
        $this->addOption('c|city:','city');
        $this->addOption('C|country:','country');
        $this->addOption('f|firstname:','first name');
        $this->addOption('l|lastname:','last name');
        $this->addOption('i|idnumber:','idnumber');
        $this->addOption('d|digest:', 'mail digest type as int (0=No digest, 1=Complete, 2=Subjects)');
        $this->addOption('I|institution:','institution');
        $this->addOption('D|department:','department');

        $this->addArgument('username');
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/user/lib.php';
        unset($CFG->passwordpolicy);

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;
            $user = new \stdClass();
            if($options['auth']){
                $user->auth = $options['auth'];
            }
            // new version of GetOptionKit does not allow a blank string as input
            // to -p or --password, so 'magic' value NONE is needed to allow
            // an explicitly blank password be specified, which needs to be 
            // possible when specifying an auth type of ldap - Bart Busschots 2 Sep 2013
            $password = $options['password'];
            if($password == 'NONE'){ // needed to stop errors when trying to set empty PW
                $password = '';
            }
            $user->password = $password;
            $user->email = $options['email'];
            $maildigest = 0;
            if($options['digest'] && is_numeric($options['digest']) && $options['digest'] > 0 && $options['digest'] <= 2){
                $maildigest = $options['digest'];
            }
            $user->maildigest = $maildigest;
            $user->city = $options['city'];
            $user->country = $options['country'];
            $user->firstname = $options['firstname'];
            $user->lastname = $options['lastname'];
            $user->idnumber = $options['idnumber'];
            $user->institution = $options['institution'];
            $user->department = $options['department'];
            $user->timecreated = time();
            $user->timemodified = $user->timecreated;
            $user->username = $argument;

            $user->confirmed = 1;
            $user->mnethostid = $CFG->mnet_localhost_id;
            
            // to prevent errors about insufficiently strong passwords, use a
            // direct DB insert rather than an API call when adding a user
            // with external auth and no password specified
            if($options['auth'] && $options['auth'] != "manual" && !$password){
                $newuserid = $DB->insert_record('user', $user);
            }else{
                $newuserid = user_create_user($user);
            }

            echo "$newuserid\n";
        }
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }
}
