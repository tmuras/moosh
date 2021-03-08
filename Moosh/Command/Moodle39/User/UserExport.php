<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */


namespace Moosh\Command\Moodle39\User;
use Moosh\MooshCommand;

class UserExport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('export', 'user');

        $this->addArgument('username');

        $this->addOption('n|name:', 'name of exported csv file', "user_bulk_download.csv");
    }

    public function execute()
    {   
        global $CFG, $DB;

        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->libdir . '/csvlib.class.php');

        $username = $this->arguments[0];
        $filename = $this->expandedOptions['name'];
        $user = get_user_by_name($username);
        if (!$user) {
            cli_error("User not found.");
        } else {
            $userid = $user->id;
        }

        $fields = array(
            'id'        => 'id',
            'username'  => 'username',
            'email'     => 'email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'idnumber'  => 'idnumber',
            'institution' => 'institution',
            'department' => 'department',
            'phone1'    => 'phone1',
            'phone2'    => 'phone2',
            'city'      => 'city',
            'url'       => 'url',
            'icq'       => 'icq',
            'skype'     => 'skype',
            'aim'       => 'aim',
            'yahoo'     => 'yahoo',
            'msn'       => 'msn',
            'country'   => 'country',
        );
        if ($extrafields = $DB->get_records('user_info_field')) {
            foreach ($extrafields as $n=>$v){
                $fields['profile_field_'.$v->shortname] = 'profile_field_'.$v->shortname;
            }
        }

        $csvexport = new \csv_export_writer();
        $csvexport->set_filename($filename);
        $csvexport->add_data($fields);
        $row = array();

        profile_load_data($user);
        $userprofiledata = array();
        foreach ($fields as $field=>$unused) {
            if (is_array($user->$field)) {
                $userprofiledata[] = reset($user->$field);
            } else {
                $userprofiledata[] = $user->$field;
            }
        }
        $csvexport->add_data($userprofiledata);

        file_put_contents($filename, $csvexport->print_csv_data(true));
        
        echo "User ".$user->username." successfully downloaded\n";
    }
}
