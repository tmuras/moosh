<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Group;

use Moosh\MooshCommand;

/**
 * Adds member to the specified group. It may be done using member username or id. If course id is specified addition
 * is based on usernames, otherwise on user ids.
 * {@code moosh group-memberadd [-g, --group] [-c, --course] <username|id>}
 *
 * @example 1: Adds user with id 111 to the group with id 333.
 * moosh group-memberadd -g 333 111
 *
 * @example 2: Adds users with ids 1, 2 and 3 to the group with id 333
 * moosh group-memberadd -g 333 1 2 3
 *
 * @example 3: Adds user with username `example_username` enrolled in course with id 5 to the group with id 1
 * moosh group-memberadd -g 1 -c 5 example_username
 *
 * @example 4: Adds users with usernames `example_username1`, `example_username2` enrolled in course with id 5
 * to the group with id 1
 * moosh group-memberadd -g 1 -c 5 example_username1, example_username2
 *
 * @package Moosh\Command\Moodle41\Group
 * @author Michal Chruscielski <michalch775@gmail.com>
 */
class GroupMemberAdd extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('memberadd', 'group');

        $this->addOption('g|group:', 'id of group');
        $this->addOption('c|course:', 'id of course');

        $this->addArgument('username');
        $this->maxArguments = 255;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once "$CFG->dirroot/group/lib.php";
        require_once "$CFG->dirroot/user/lib.php";
        require_once "$CFG->dirroot/enrol/externallib.php";

        $options = $this->expandedOptions;
        if (!empty($options['course'])) {
            $courseId = $options['course'];

            if($this->verbose) {
                mtrace("Course id was specified. It means that users will be added by usernames.");
            }
        } else if($this->verbose) {
            mtrace("Course id wasn't specified. It means that users will be added by ids.");
        }

        $groupId = $options['group'];

        // we must now to which group we're adding
        if(empty($groupId)) {
            cli_error("Group id (--group) must not be empty!");
        }

        // Array with access to all loaded users by its usernames. Probably less readable than searching array,
        // but more convenient and performant, probably.
        $usersByUsername = array();

        // If courseId provided we want to select users my their usernames, otherwise by ids.
        if (!empty($courseId)) {
            if($this->verbose) {
                mtrace("Loading users for course id $courseId.");
            }

            try {
                $users = \core_enrol_external::get_enrolled_users($courseId);
            } catch(\dml_exception $e) {
                if($e->errorcode === "invalidrecord") {
                    cli_error("Course with id $courseId does not exist.");
                } else {
                    cli_error("Course users can't be found. Use --verbose for more info.");
                }

                // suppresses IDE warnings, exit breaks execution anyway
                exit();
            }

            if($this->verbose) {
                $usersSize = count($users);
                mtrace("Loaded $usersSize users.");
            }

            foreach ($users as $user) {
                $usersByUsername[$user['username']] = $user;
            }
        }

        $addedUsers = [];
        foreach ($this->arguments as $argument) {

            $this->expandOptionsManually(array($argument));

            if (!empty($courseId) && count($usersByUsername) > 0)  {
                $expectedUsername = $argument;

                if(!array_key_exists($expectedUsername, $usersByUsername)) {
                    print("User with username $expectedUsername do not belong to course with id $courseId. Skipping.\n");
                    continue;
                }

                if($this->verbose) {
                    mtrace("User with username $expectedUsername belongs to selected course. Attempting addition to group.");
                }

                $user = $usersByUsername[$expectedUsername];

                $userAdded = \groups_add_member($groupId, $user["id"]);
                if ($userAdded) {
                    $addedUsers[] = $user;
                    $username = $user["username"];
                    print("User with username: $username successfully added.\n");
                } else {
                    print("User with username: $expectedUsername wasn't added to the selected group. Skipping.\n");
                }
            } else {
                try {
                    $user = $DB->get_record('user', array('id' => $argument), '*', MUST_EXIST);
                } catch(\Exception $e) {
                    print("User with id $argument can't be found. Skipping.\n");
                    continue;
                }

                if($this->verbose) {
                    mtrace("User with id $argument found. Attempting addition to the group.");
                }

                $userAdded = \groups_add_member($groupId, $argument);

                if ($userAdded) {
                    print("User with id: $argument successfully added.\n");
                    $addedUsers[] = $user;
                } else {
                    print("User with id: $argument wasn't added to the selected group. Skipping.\n");
                }
            }
        }

        $addedUsersCount = count($addedUsers);

        // Adding a bit of space
        print("\n");
        if($addedUsersCount > 0) {
            print("Added $addedUsersCount users to group with id $groupId.\n");
        } else {
            print("No user was added to the selected group. Run command with --verbose for more info.\n");
        }
    }
}
