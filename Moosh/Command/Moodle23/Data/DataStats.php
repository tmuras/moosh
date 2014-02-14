<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Data;
use Moosh\MooshCommand;

class DataStats extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('stats', 'data');

        $this->addOption('j|json', 'generate output using json format');
    }

    public function execute() 
    {
        global $CFG;
        global $DB;

        $options = $this->expandedOptions;

        $dataroot = run_external_command("du -s $CFG->dataroot", "Couldn't find dataroot directory");
        $pattern = '/\d*/';
        preg_match($pattern, $dataroot[0], $matches);

        $filedir = run_external_command("du -s $CFG->dataroot/filedir", "Couldn't find filedir directory");
        preg_match($pattern, $filedir[0], $dir_matches);

        $sql_query = "SELECT SUM(filesize) AS total FROM {files}";
        $all_files = $DB->get_record_sql($sql_query);

        $sql_query = "SELECT DISTINCT contenthash, SUM(filesize) AS total FROM {files}";
        $distinct_contenthash = $DB->get_record_sql($sql_query);

        if ($options['json']) {
            $to_encode = array('dataroot' => $matches[0],
                            'filedir' => $dir_matches[0], 
                            'files total' => $all_files->total, 
                            'distinct files total' => $distinct_contenthash->total);

            echo json_encode($to_encode);
        } else {
            echo $matches[0] . "\n";
            echo $dir_matches[0] . "\n";
            echo $all_files->total . "\n";
            echo $distinct_contenthash->total . "\n";
        }
    }
}
