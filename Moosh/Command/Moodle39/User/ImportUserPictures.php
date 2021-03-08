<?php
/**
 * Provides capability to mass import user pictures from a specific directory.
 * moosh user-import-pictures [import_dir]
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @author     Sébastien Mehr <sebastien.mehr (at) uha.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\User;

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
    private $policyid;

    /**
     * ImportUserPictures constructor.
     */
    public function __construct() {
        parent::__construct('import-pictures', 'user');

        $this->addArgument('import_dir');
        $this->addOption('overwrite', 'Replace existing user pictures', false);
        $this->addOption('i|id', 'Map image filename to user ID field');
        $this->addOption('n|idnumber', 'Map image filename to user idnumber field');
        $this->addOption('u|username', 'Map image filename to user username field');
        $this->addOption('p|policy:', 'Check policy acceptance by user before importing picture');
    }

    public function execute() {
        global $CFG;

        try {
            $this->overwrite = (bool)$this->expandedOptions['overwrite'];
            $this->userField = $this->getMapFieldName();
            $inputDir = $this->arguments[0];

            require_once($CFG->libdir . '/uploadlib.php');
            require_once($CFG->libdir . '/adminlib.php');
            require_once($CFG->libdir . '/gdlib.php');

            if ($this->parsedOptions->has('policy')) {

                $this->policyid = $this->parsedOptions['policy']->value;

                if (!ctype_digit($this->policyid)) {
                    throw new \RuntimeException(sprintf("Policy id must be an integer, given '%s'.", $this->policyid));
                }

                if (!$this->isPolicy($this->policyid)) {
                    throw new \RuntimeException(sprintf("Policy with id '%s' does not exist.", $this->policyid));
                }
            }

            // Check if input directory exists.
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
    private function getMapFieldName() {
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
    private function processDirectory($dir) {
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
    private function processFile($filename) {
        global $DB;

        // Obtain file info.
        $pathParts = pathinfo(cleardoubleslashes($filename));
        // Ignore file extension.
        $basename = $pathParts['filename'];
        $extension = strtolower($pathParts['extension']);

        if (!in_array($extension, $this->supportedFileTypes)) {
            $this->log(sprintf("Ignoring unsupported file type '%s'", $filename), self::LOG_VERBOSE);
            return;
        }

        // Load user by mapped field name.
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

        if ($this->policyid) {
            if (!$this->isPolicyAccepted($user, $this->policyid)) {
                $this->log(
                    sprintf(
                        "user with ID %s has not accepted the policy with ID %s",
                        $user->id,
                        $this->policyid
                    )
                );
                return;
            }
        }

        // Picture field might not have been cached by moodle so query database again.
        $hasPicture = $DB->get_field('user', 'picture', array('id' => $user->id));
        if ($hasPicture && !$this->overwrite) {
            $this->log(
                sprintf("Refusing to overwrite user ID:%s picture with file '%s'", $user->id, $filename),
                self::LOG_VERBOSE
            );
            return;
        }

        // Grab user's context and process image file.
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
    private function log($message, $level = self::LOG_MESSAGE) {
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
    private function logError($message) {
        fwrite(STDERR, "ERROR: $message\n");
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_FULL;
    }

    /**
     * Check if a user has accepted the policy
     *
     * @param object $user object User
     * @param int $policyid policy's id to check
     * @return bool $result
     */
    private function isPolicyAccepted($user, $policyid) {
        global $DB;
        $result = false;

        if ($DB->record_exists_sql('SELECT * FROM {tool_policy_acceptances} pol_acceptances
                                    JOIN {tool_policy} pol ON pol_acceptances.policyversionid = pol.currentversionid
                                    WHERE pol.id = ?
                                    AND pol_acceptances.userid = ?
                                    AND pol_acceptances.status = ?',
                                    [$policyid, $user->id, 1])) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check if the policy exists
     *
     * @param int $policyid policy's id to check
     * @return bool $result
     */
    private function isPolicy($policyid) {
        global $DB;
        $result = false;

        if ($DB->record_exists('tool_policy', array('id'=>$policyid))) {
            $result = true;
        }

        return $result;
    }
}