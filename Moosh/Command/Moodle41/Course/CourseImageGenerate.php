<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Course;

use local_ai_connector\ai\ai;
use Moosh\MooshCommand;
use Moosh\MoodleMetaData;
use curl;

class CourseImageGenerate extends MooshCommand
{


    public function __construct()
    {
        global $DB;
        parent::__construct('image-generate', 'course');

        $this->addArgument('courseid');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        global $CFG, $DB, $USER;
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        require_once($CFG->libdir . '/filelib.php');

        $options = $this->expandedOptions;
        $courseid = $this->arguments[0];

        // extract image file from course image
        $course = $DB->get_record('course', array('id' => $courseid));
        if (!$course) {
            cli_error('Invalid course id: ' . $courseid);
        }
        $coursecontext = \context_course::instance($course->id);
        $courseimage = $DB->get_record_sql("SELECT * FROM {files} WHERE filename <> ? AND contextid = ? AND 
component= 'course' AND filearea = 'overviewfiles' AND 'itemid' = 0 LIMIT 1", array('.', $coursecontext->id));
        if (!$courseimage) {
            cli_error('No course image found for course id: ' . $courseid);
        }
        $courseimagepath = $CFG->dataroot . '/filedir/' . substr($courseimage->contenthash, 0, 2) . '/' . substr($courseimage->contenthash, 2, 2) . '/' . $courseimage->contenthash;
        if (!file_exists($courseimagepath)) {
            cli_error('Course image file not found: ' . $courseimagepath);
        }

        $tempdir = $CFG->tempdir . '/moosh/';
        if (!file_exists($tempdir)) {
            mkdir($tempdir);
        }

        $image = new \Imagick($courseimagepath);

        $height = $image->getImageHeight();
        $width = $image->getImageWidth();
        $size = min($width, $height);
        $image->cropThumbnailImage($size, $size);

        unlink($tempdir . '/mainfile.png');
        // Save the image.
        $image->writeImage($tempdir . '/mainfile.png');

        $headers = [
            "Authorization: Bearer xxx",
            "Content-Type: multipart/form-data;charset=utf-8"
        ];

        // Get all the sections of Moodle course
        $sections = $DB->get_records('course_sections', array('course' => $courseid), 'section ASC');
        foreach ($sections as $section) {
            $description = strip_tags($section->summary);

            echo "Processing section " . $section->section . ":\n";

            $ask = "You are now a DALLE-2 prompt generation tool that will generate a suitable prompt for me to generate a picture 
            based on the elearning course description, generate a prompt that gives the DALLE-2 AI text to generate a picture model,
            please narrate in English. The elearning course description is:\n";
            $ask .= '"' . $description . '"' . "\n";

            $ai = new ai();
            echo "Asking ChatGPT for:\n";
            echo $ask . "\n";

            $response = $ai->prompt_completion($ask);
            $prompt = $response['content'];

            echo "Prompt received:\n";
            echo $prompt . "\n";

            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => [
                    'prompt' => $prompt,
                    'size' => "256x256",
                    'image' => curl_file_create($tempdir . '/mainfile.png'),
                ]
            ];
            $curl = curl_init('https://api.openai.com/v1/images/edits');

            curl_setopt_array($curl, $options);
            $response = curl_exec($curl);
            $response = json_decode($response, true);
            $info = curl_getinfo($curl);
//            print_r($response);
//            print_r($info);

            // Download generated file into temp using Moodle function
            $file = file_get_contents($response['data'][0]['url']);
            $filename = 'dalle_' . $courseid . '_' . time() . '.png';
            $temppath = $tempdir . '/' . $filename;
            echo "Saving new image as $temppath\n";
            file_put_contents($temppath, $file);

            // Add image to Moodle course files
            $filerecord = new \stdClass();
            $filerecord->contextid = $coursecontext->id;
            $filerecord->component = 'course';
            $filerecord->filearea = 'section';
            $filerecord->itemid = $section->id;
            $filerecord->filepath = '/';
            $filerecord->filename = $filename;
            $filerecord->userid = $USER->id;
            $filerecord->mimetype = 'image/png';

            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $temppath);
//            unlink($temppath);

            $section->summary = '<p dir="ltr" style="text-align: left;"><img src="@@PLUGINFILE@@/'. $filename . '" alt="AI Generated" class="img-fluid atto_image_button_text-bottom" width="256" height="256"><br></p>' .
                $section->summary;

            // Inject into course section
            $DB->update_record('course_sections', $section);
        }



    }
}
