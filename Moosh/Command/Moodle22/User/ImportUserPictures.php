<?php
/**
 * Provides capability to mass import user pictures from a specific directory.
 * moosh user-import-pictures [import_dir]
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle22\User;

use Moosh\MooshCommand;

/**
 * Class ImportUserPictures
 * @package Moosh\Command\Generic\User
 */
class ImportUserPictures extends MooshCommand
{
    const LOG_MESSAGE = 0;
    const LOG_VERBOSE = 1;

    private $supportedFileTypes = array('jpg', 'jpeg', 'gif', 'png');
    private $overwrite;
    private $userField;

    /**
     * ImportUserPictures constructor.
     */
    public function __construct()
    {
        parent::__construct('import-pictures', 'user');

        $this->addArgument('import_dir');
        $this->addOption('overwrite', 'Replace existing user pictures', false);
        $this->addOption('i|id', 'Map image filename to user ID field');
        $this->addOption('n|idnumber', 'Map image filename to user idnumber field');
        $this->addOption('u|username', 'Map image filename to user username field');
    }

    public function execute()
    {
        global $CFG;

        try {
            $this->overwrite = (bool)$this->expandedOptions['overwrite'];
            $this->userField = $this->getMapFieldName();
            $inputDir = $this->arguments[0];

            require_once($CFG->libdir . '/uploadlib.php');
            require_once($CFG->libdir . '/adminlib.php');
            require_once($CFG->libdir . '/gdlib.php');

            // check if input directory exists
            if (!file_exists($inputDir)) {
                throw new \RuntimeException(sprintf("Input directory '%s' does not exist.", $inputDir));
            }

            $this->processDirectory($inputDir);
        } catch (\Exception $ex) {
            $this->logError($this->verbose ? $ex->__toString() : $ex->getMessage());
        }
    }

    /**
     * Returns user record field name for mapping image filename to Moodle user.
     *
     * @return string id, idnumber or username.
     * @throws \Exception If field name is not set by user.
     */
    private function getMapFieldName()
    {
        $selectedFieldName = null;

        foreach (array('id', 'idnumber', 'username') as $fieldName) {
            if (empty($this->expandedOptions[$fieldName])) {
                continue;
            }

            if ($selectedFieldName) {
                throw new \Exception('Only one user field mapping can be used');
            }

            $selectedFieldName = $fieldName;
        }

        if (!$selectedFieldName) {
            throw new \RuntimeException('Please specify mapped user field with available options');
        }

        return $selectedFieldName;
    }

    /**
     * Process directory contents recursively.
     *
     * @param string $dir Directory absolute path
     */
    private function processDirectory($dir)
    {
        if (!($handle = opendir($dir))) {
            throw new \RuntimeException(sprintf("Cannot open import dir '%s'", $dir));
        }

        while (false !== ($item = readdir($handle))) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $filename = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item;

            if (is_dir($filename)) {
                $this->processDirectory($filename);
            } else if (is_file($filename)) {
                $this->processFile($filename);
            } else {
                throw new \RuntimeException(sprintf("Could not process file '%s'", $filename));
            }
        }

        closedir($handle);
    }

    /**
     * Finds Moodle user and imports picture.
     *
     * @param string $filename Image filename
     */
    private function processFile($filename)
    {
        global $DB;

        // obtain file info
        $path_parts = pathinfo(cleardoubleslashes($filename));
        $basename = $path_parts['filename']; // ignore file extension
        $extension = strtolower($path_parts['extension']);

        if (!in_array($extension, $this->supportedFileTypes)) {
            $this->log(sprintf("Ignoring unsupported file type '%s'", $filename), self::LOG_VERBOSE);
            return;
        }

        // load user by mapped field name
        $user = $DB->get_record('user', array($this->userField => $basename, 'deleted' => 0));

        if (!$user) {
            $this->logError(
                sprintf(
                    "Cannot find user by %s value '%s' for filename '%s'",
                    $this->userField,
                    $basename,
                    $filename
                )
            );
            return;
        }

        // picture field might not have been cached by moodle so query database again
        $hasPicture = $DB->get_field('user', 'picture', array('id' => $user->id));
        if ($hasPicture && !$this->overwrite) {
            $this->log(
                sprintf("Refusing to overwrite user ID:%s picture with file '%s'", $user->id, $filename),
                self::LOG_VERBOSE
            );
            return;
        }

        // grab user's context and process image file
        $context = \context_user::instance($user->id);
        $fileId = process_new_icon($context, 'user', 'icon', 0, $filename);

        if ($fileId) {
            $DB->set_field('user', 'picture', $fileId, array('id' => $user->id));
            $this->log(sprintf("Updated user id:%s picture '%s'", $user->id, $filename));
        } else {
            $this->logError(
                sprintf("Cannot process image '%s' for user ID:%s", $filename, $user->id)
            );
        }
    }

    /**
     * Outputs message to stdout.
     *
     * @param string $message Output message.
     * @param int $level Verbosity level, see self:LOG_* constants.
     */
    private function log($message, $level = self::LOG_MESSAGE)
    {
        if ($level == self::LOG_VERBOSE && !$this->verbose) {
            return;
        }

        echo "$message\n";
    }

    /**
     * Outputs error message to stderr.
     *
     * @param string $message Output message.
     */
    private function logError($message)
    {
        fwrite(STDERR, "ERROR: $message\n");
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL;
    }
}
