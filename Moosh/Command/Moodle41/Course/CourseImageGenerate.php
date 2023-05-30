<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Course;

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
        global $CFG, $DB;
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
        if(!$course) {
            cli_error('Invalid course id: ' . $courseid);
        }
        $coursecontext = \context_course::instance($course->id);
        $courseimage = $DB->get_record_sql("SELECT * FROM {files} WHERE filename <> ? AND contextid = ? AND 
component= 'course' AND filearea = 'overviewfiles' AND 'itemid' = 0 LIMIT 1", array('.', $coursecontext->id));
        if(!$courseimage) {
            cli_error('No course image found for course id: ' . $courseid);
        }
        $courseimagepath = $CFG->dataroot . '/filedir/' . substr($courseimage->contenthash, 0, 2) . '/' . substr($courseimage->contenthash,2, 2) . '/' . $courseimage->contenthash;
        if(!file_exists($courseimagepath)) {
            cli_error('Course image file not found: ' . $courseimagepath);
        }

        $image = new \Imagick($courseimagepath);

        $height = $image->getImageHeight();
        $width = $image->getImageWidth();
        $size = min($width, $height);
        $image->cropThumbnailImage($size, $size);
        // Save the image.
        $image->writeImage($CFG->dataroot . '/temp/new.png');

        $headers = [
            "Authorization: Bearer xxx",
            "Content-Type: multipart/form-data;charset=utf-8"
        ];

        $curl = new \curl();
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS     => [
                'prompt' => 'circle, square, triangle',
                'size' => "256x256" ,
//                'image' => new \CURLFile($courseimagepath),
                'image' => curl_file_create($CFG->dataroot . '/temp/new.png'),
            ]
        ];
        $curl = curl_init('https://api.openai.com/v1/images/edits');

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);

        $info           = curl_getinfo($curl);
        $this->curlInfo = $info;
print_r($response);
var_dump($info);
        curl_close($curl);
die('ok');
//        $data = [
//            'prompt' => 'circle, square, triangle',
//            'size' => "256x256" ,
//            'image' => file_get_contents('/home/tmuras/Projects/2023_ai_image_generation/frog41t_ms.png'),
//        ];
        //var_dump(json_encode($data));
        $response = $curl->post('https://api.openai.com/v1/images/edits', '', $options);
        if (json_decode($response) == null) {
            return ['curl_error' => $response];
        }
        var_dump(json_decode($response, true));

        die();
        $ai = new \local_ai_connector\ai\ai();
        // Generate any image.
        $url = $ai->prompt_dalle('Imagine a vibrant and engaging educational cover image for an online mathematics course specifically designed for highly talented children. The course ends with a focus on integrals, indicating a high level of complexity. This specific chapter is about Fractions, which includes the following subtopics:
- Ordinary Fractions
- Comparing Fractions
- Addition and Subtraction of Fractions
- Multiplying Fractions by Natural Numbers.
    The image should be colorful, fun, and stimulate curiosity while still being clearly relevant to the topic. It might include various types of fractions and their visual representations, maybe as part of some exciting mathematical landscape or adventure. Also, consider including some elements related to integrals to reflect the overall course theme. Remember, this is for exceptionally talented children, so the image should not be overly simplified.
    The image should have no text, no written words."
');
        var_dump($url);

        // Check if URL is valid.
        if(strpos($url, 'https://') !== 0) {
            cli_error('Invalid response from dalle: ' . $url);
        }

        // Fetch the image into Moodle's temp directory.
        $tempdir = $CFG->tempdir . '/moosh/';
        if(!file_exists($tempdir)) {
            mkdir($tempdir);
        }
        $temppath = $tempdir . '/dalle_' .  $courseid . '_' . time() . '.png';
        file_put_contents($temppath, file_get_contents($url));

        // Add it into course files.


    }
}
