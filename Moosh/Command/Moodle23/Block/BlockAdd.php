<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Block;
use Moosh\MooshCommand;
use context_coursecat;
use context_course;
use context_block;

class BlockAdd extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('add', 'block');

        $this->addArgument('mode');
        $this->addArgument('id');
        $this->addArgument('blocktype');
        $this->addArgument('pagetypepattern');
        $this->addArgument('region');
        $this->addArgument('weight');
        //$this->addArgument('showinsubcontexts');
        $this->addOption('s|showinsubcontexts', 'Display block on all sub contexts');

    }

    public function execute()
    {
        global $CFG, $DB;

        //require_once($CFG->dirroot . '/lib/accesslib.php');

        $mode = $this->arguments[0];
        $id = $this->arguments[1];
        $blocktype = $this->arguments[2]; // name of the block (in English)
        $pagetypepattern = $this->arguments[3]; // in which page types it will be available ('course-*' , 'mod-*' ...)
        $region = $this->arguments[4]; // into which page region it will be inserted ('side-pre' , 'side-post' ...)
        $weight = $this->arguments[5]; // sort/insert order

        //$showinsubcontexts = $this->arguments[6]; // show block in sub context?
        if ($this->expandedOptions['showinsubcontexts']) {
            $showinsubcontexts = true;
        } else {
            $showinsubcontexts = false;
        }

        // Make sure $blocktype is a valid block name.
        $DB->get_record('block', ['name'=>$blocktype], '*', MUST_EXIST);

        switch ($mode) {
            case 'category':
                $context = context_coursecat::instance($id /* categoryid */, MUST_EXIST);
                self::blockAdd($context->id /* categorycontextid */,$blocktype,$pagetypepattern,$region,$weight,$showinsubcontexts);
                break;
            case 'course':
                $context = context_course::instance($id /* courseid */, MUST_EXIST);
                self::blockAdd($context->id,$blocktype,$pagetypepattern,$region,$weight,$showinsubcontexts);
                break;

            case 'categorycourses':
                //get all courses in category (recursive)
                $courselist = get_courses($id);
                foreach ($courselist as $course) {
                    $context = context_course::instance($course->id /* courseid */, MUST_EXIST);
                    self::blockAdd($context->id,$blocktype,$pagetypepattern,$region,$weight,$showinsubcontexts);
                    echo "debug: courseid=$course->id \n";
                }
                break;
        }

    }

    private function blockAdd($context,$blocktype,$pagetypepattern,$region,$weight,$showinsubcontexts=true){
        global $CFG,$DB;
        require_once($CFG->dirroot . '/lib/blocklib.php');

        // Allow invisible blocks because this is used when adding default page blocks, which
        // might include invisible ones if the user makes some default blocks invisible
        // todo: validity checks
        //$this->check_known_block_type($blocktype, true);
        //$this->check_region_is_known($region);

        if (empty($pagetypepattern)) {
            $pagetypepattern = '*';
        }

        $blockinstance = new \stdClass();
        $blockinstance->blockname = $blocktype;
        $blockinstance->parentcontextid = $context;
        $blockinstance->showinsubcontexts = !empty($showinsubcontexts);
        $blockinstance->pagetypepattern = $pagetypepattern;
        $blockinstance->subpagepattern = NULL;
        $blockinstance->defaultregion = $region;
        $blockinstance->defaultweight = $weight;
        $blockinstance->configdata = '';
        $blockinstance->timecreated = $blockinstance->timemodified = time();
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

        // Ensure the block context is created.
        context_block::instance($blockinstance->id);

        // If the new instance was created, allow it to do additional setup
        if ($block = block_instance($blocktype, $blockinstance)) {
            $block->instance_create();
            //print_object($block);
            echo "debug: success (blockid={$block->context->id})\n";
        }

    }
    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
                "\n\tcourse courseid blocktype pagetypepattern region weight".
                "\n\tcategorycourses categoryid[all] blocktype pagetypepattern region weight".
                "\n\tcategory categoryid blocktype pagetypepattern region weight".
                "\n\n\tpagetypepattern = *|course-view-*|mod-*-view|site-index|...".
                "\n\tregion = side-pre|side-post|content|...";
    }
}
