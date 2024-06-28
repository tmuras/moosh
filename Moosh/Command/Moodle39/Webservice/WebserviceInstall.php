<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright 2021 unistra {@link http://unistra.fr}
 * @author 2021 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Webservice;
use Moosh\MooshCommand;

class WebserviceInstall extends MooshCommand
{
    public function __construct() {
        parent::__construct('install', 'webservice');
        $this->addArgument('servicename');
        $this->addArgument('capabilities');
        $this->addOption('m|mail:', 'mail');
        $this->addOption('u|username:', 'user name');
        $this->addOption('r|rolename:', 'role name');
        $this->addOption('i|iprestriction:',' ip restriction', '');
        $this->addOption('v|validuntil:', 'valid until', 0);
    }

    public function execute()
    {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/user/lib.php');
        require_once($CFG->dirroot.'/webservice/lib.php');
        $servicename = $this->arguments[0];
        $service = $DB->get_record('external_services', array('shortname' => $servicename));
        if(!$service) {
            echo "service $servicename does not exist".PHP_EOL;
            return;
        }
        $capabilities = explode(',', $this->arguments[1]);
        $rolename = $this->expandedOptions['rolename'] ? $this->expandedOptions['rolename'] : 'role_'.$servicename;
        $username=  $this->expandedOptions['username'] ? $this->expandedOptions['username'] : 'user_'.$servicename;
        $iprestriction = $this->expandedOptions['iprestriction'];
        $validuntil = $this->expandedOptions['validuntil'];
        // Create user.
        $email = null;
        if ($this->expandedOptions['mail']) {
            $email = $this->expandedOptions['mail'];
        } else {
            $noreplyuser = \core_user::get_noreply_user();
            $email= $servicename.'_'.$noreplyuser->email;
        }
        // Check if user exits.
        $wsuserid = 0;
        $userobject = $DB->get_record('user', array('username' => $username));
        if($userobject) {
            $wsuserid = $userobject->id;
            echo "user $username already exists so will use it".PHP_EOL;
        } else {
            $user = new \stdClass();
            $user->username = $username;
            $user->firstname = $username;
            $user->lastname = $username;
            $user->email = $email;
            $user->confirmed=1;
            $user->policyagreed=1;
            $user->mnethostid = $CFG->mnet_localhost_id;
            $wsuserid = user_create_user($user);
        }
        // Create Role
        $systemcontext = \context_system::instance();
        $rolerecord = $DB->get_record('role', array('shortname' => $rolename));
        $wsroleid = 0;
        if ($rolerecord) {
            $wsroleid = $rolerecord->id;
            echo "role $rolename already exists, we'll use it".PHP_EOL;
        } else {
            $wsroleid = create_role(
                $rolename,
                $rolename,
                $rolename
            );
        }
        // Assign necessary capabilities
        foreach($capabilities as $capability){
            $capability = trim($capability);
            $capaobject = $DB->get_record('capabilities', array('name' => $capability));
            if(!$capability){
                echo "capability $capability not exists".PHP_EOL;
                continue;
            }
            assign_capability($capability, CAP_ALLOW,
                $wsroleid, $systemcontext->id, true);
        }
        // Allow role assignment on system.
        set_role_contextlevels($wsroleid, array(10 => 10));
        // Assign role to user.
        role_assign($wsroleid, $wsuserid, $systemcontext->id);
        // Assign user to webservice.
        $webservicemanager = new \webservice();
        $serviceuser = new \stdClass();
        $serviceuser->externalserviceid = $service->id;
        $serviceuser->userid = $wsuserid;
        $serviceuser->iprestriction = $iprestriction;

        $webservicemanager->add_ws_authorised_user($serviceuser);
        // Trigger events.
        $params = array(
            'objectid' => $serviceuser->externalserviceid,
            'relateduserid' => $serviceuser->userid
        );
        $event = \core\event\webservice_service_user_added::create($params);
        $event->trigger();
        // Take admin role
        // Generate Token.
        $token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service->id, $wsuserid, $systemcontext->id, $validuntil, $iprestriction);
        echo "Webservice $servicename installed : user $username and role $rolename".PHP_EOL;
        echo "Token generated $token";
    }
    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Install a webservice ";
        $help .= "\ncapabilities are seperated by a commas.\n\n";
        $help .= "\nif user is not defined, user_servicename will be created";
        $help .= "\nif role is not defined, role_servicename will be created";

        return $help;
    }
}