<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Forum;
use Moosh\MooshCommand;

/**
 * Adds forum discussions.
 *
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ForumNewdiscussion extends MooshCommand
{

    protected $discussionoptions = array(
        'subject' => array('s|subject:', 'discussion subject'),
        'message' => array('m|message:', 'message'),
    );

    public function __construct()
    {
        parent::__construct('newdiscussion', 'forum');

        $this->addArgument('course');
        $this->addArgument('forum');
        $this->addArgument('user');

        // Adding options.
        foreach ($this->discussionoptions as $option) {
            list($spec, $description) = $option;
            $this->addOption($spec, $description);
        }
    }

    /**
     * Adds a new discussion to the specified forum.
     *
     * @return the discussion id
     */
    public function execute()
    {
        // Forum functions needed.
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Getting moodle's data generator.
        $generator = get_data_generator();

        // Compulsory and optional attrs.
        $discussiondata = new \stdClass();
        $discussiondata->course = $this->arguments[0];
        $discussiondata->forum = $this->arguments[1];
        $discussiondata->userid = $this->arguments[2];
        foreach ($this->discussionoptions as $key => $values) {
            if (!empty($this->expandedOptions[$key])) {
                $discussiondata->{$key} = $this->expandedOptions[$key];
            }
        }
        $discussiondata->name = $discussiondata->subject;

        $forumgenerator = $generator->get_plugin_generator('mod_forum');
        $record = $forumgenerator->create_discussion($discussiondata);

        if ($this->verbose) {
            echo "Discussion {$record->name} successfully added\n";
        }

        echo "{$record->id}\n";
    }

}
