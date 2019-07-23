<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle27\UserProfileFields;

use Moosh\MooshCommand;

class UserProfileFieldsImport extends MooshCommand
{
    public function __construct() {
        parent::__construct('import', 'userprofilefields');

        $this->addArgument('path');
    }

    public function execute() {
        global $CFG, $DB;

        require_once($CFG->libdir . '/csvlib.class.php');      
        require_once($CFG->libdir . '/moodlelib.php');

        $csvfilepath = $this->arguments[0];
        if ($csvfilepath[0] != '/') {
            $csvfilepath = $this->cwd . DIRECTORY_SEPARATOR . $csvfilepath;
        }
        $iid = \csv_import_reader::get_new_iid('userprofile');
        $type = 'userprofile';
        $csvreader = new \csv_import_reader($iid, $type);

        if (false === $csvfile = file_get_contents($csvfilepath)) {
            cli_error('Unable to load csv file. '. error_get_last()['message']);
        }
        
        if (!$csvreader->load_csv_content($csvfile, 'utf-8', 'comma')) {
            cli_error('Unalbe to parse csv file. '.$csvreader->get_error());
        }
        if (!$csvreader->init()) {
            cli_error('Unable to initialise csv reading');
        }
        $columns = $csvreader->get_columns();
        $columnsids = array_flip($columns);

        while(false !== $row = $csvreader->next()) {
            $category   = $this->get_or_create_category($row[$columnsids['categoryname']],
                                                 $row[$columnsids['categorysortorder']]);
            $userfield = new \stdClass();
            $userfield->shortname           = $row[$columnsids['shortname']];
            $userfield->name                = $row[$columnsids['name']];
            $userfield->datatype            = $row[$columnsids['datatype']];
            $userfield->description         = $row[$columnsids['description']];
            $userfield->descriptionformat   = $row[$columnsids['descriptionformat']];
            $userfield->categoryid          = $category->id;
            $userfield->sortorder           = $row[$columnsids['sortorder']];
            $userfield->required            = $row[$columnsids['required']];
            $userfield->locked              = $row[$columnsids['locked']];
            $userfield->visible             = $row[$columnsids['visible']];
            $userfield->forceunique         = $row[$columnsids['forceunique']];
            $userfield->signup              = $row[$columnsids['signup']];
            $userfield->defaultdata         = $row[$columnsids['defaultdata']];
            $userfield->defaultdataformat   = $row[$columnsids['defaultdataformat']];
            $userfield->param1              = $row[$columnsids['param1']];
            $userfield->param2              = $row[$columnsids['param2']];
            $userfield->param3              = $row[$columnsids['param3']];
            $userfield->param4              = $row[$columnsids['param4']];
            $userfield->param5              = $row[$columnsids['param5']];

            $this->get_or_create_userfield($userfield);
        }
    }

    protected function get_or_create_category($category, $categorysortorder) {
        global $DB;

        $result = $DB->get_record('user_info_category', array('name' => $category));
        if ($result) {
            return $result;
        }
        else {
            $userinfocategory = new \stdClass();
            $userinfocategory->name         = $category;
            $userinfocategory->sortorder    = $categorysortorder;
            $DB->insert_record('user_info_category', $userinfocategory);
            $result = $DB->get_record('user_info_category', array('name' => $category));
            echo "Added category: ".$result->name."\n";
            return $result;
        }

    }

    protected function get_or_create_userfield($userfield) {
        global $DB;

        $userfieldresult = $DB->get_record('user_info_field', 
                                array('shortname' => $userfield->shortname));
        if ($userfieldresult) {
            echo "Skipped: ".$userfield->shortname."\n";
        }
        else {
            $DB->insert_record('user_info_field', $userfield);
            echo "Uploaded: ".$userfield->shortname."\n";
        }
    }
}
