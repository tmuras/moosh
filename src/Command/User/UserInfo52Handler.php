<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * user:info implementation for Moodle 5.1.
 */
class UserInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'userid',
            InputArgument::REQUIRED,
            'The ID of the user to inspect',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $userid = (int) $input->getArgument('userid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/user/lib.php';
        require_once $CFG->libdir . '/accesslib.php';

        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user) {
            $output->writeln("<error>User with ID $userid not found.</error>");
            return Command::FAILURE;
        }

        $usercontext = \context_user::instance($userid, MUST_EXIST);

        $data = [];

        // --- Profile ---
        $verbose->step('Collecting profile information');
        $data['User ID'] = $user->id;
        $data['Username'] = $user->username;
        $data['First name'] = $user->firstname;
        $data['Last name'] = $user->lastname;
        $data['Email'] = $user->email;
        $data['Auth method'] = $user->auth;
        $data['Confirmed'] = (int) $user->confirmed;
        $data['Suspended'] = (int) $user->suspended;
        $data['Deleted'] = (int) $user->deleted;
        $data['Language'] = $user->lang;
        $data['Timezone'] = $user->timezone;
        $data['First access'] = $user->firstaccess ? date('Y-m-d H:i:s', $user->firstaccess) : 'never';
        $data['Last access'] = $user->lastaccess ? date('Y-m-d H:i:s', $user->lastaccess) : 'never';
        $data['Last login'] = $user->lastlogin ? date('Y-m-d H:i:s', $user->lastlogin) : 'never';
        $data['Last IP'] = $user->lastip ?: '';
        $data['Time created'] = date('Y-m-d H:i:s', $user->timecreated);
        $data['Description length'] = strlen($user->description ?? '');

        // --- Enrolments ---
        $verbose->step('Counting enrolments');
        $enrolments = $DB->get_record_sql(
            "SELECT COUNT(DISTINCT e.courseid) AS c
               FROM {user_enrolments} ue
               JOIN {enrol} e ON e.id = ue.enrolid
              WHERE ue.userid = ?",
            [$userid],
        );
        $data['Courses enrolled'] = (int) $enrolments->c;

        // Enrolment methods breakdown.
        $enrolMethods = $DB->get_records_sql(
            "SELECT e.enrol, COUNT(*) AS c
               FROM {user_enrolments} ue
               JOIN {enrol} e ON e.id = ue.enrolid
              WHERE ue.userid = ?
              GROUP BY e.enrol
              ORDER BY e.enrol",
            [$userid],
        );
        foreach ($enrolMethods as $method) {
            $data["Enrolments via {$method->enrol}"] = (int) $method->c;
        }

        // --- Role assignments ---
        $verbose->step('Counting role assignments');
        $totalRoles = $DB->count_records('role_assignments', ['userid' => $userid]);
        $data['Total role assignments'] = $totalRoles;

        $roleNames = $DB->get_records_menu('role', null, '', 'id, shortname');
        $roleBreakdown = $DB->get_records_sql(
            "SELECT roleid, COUNT(*) AS c FROM {role_assignments} WHERE userid = ? GROUP BY roleid",
            [$userid],
        );
        foreach ($roleBreakdown as $ra) {
            $roleName = $roleNames[$ra->roleid] ?? "role-{$ra->roleid}";
            $data["Assignments as $roleName"] = (int) $ra->c;
        }

        // --- System role assignments ---
        $verbose->step('Checking system role assignments');
        $systemContext = \context_system::instance();
        $systemRoles = $DB->get_records_sql(
            "SELECT ra.roleid, r.shortname
               FROM {role_assignments} ra
               JOIN {role} r ON r.id = ra.roleid
              WHERE ra.userid = ? AND ra.contextid = ?",
            [$userid, $systemContext->id],
        );
        $sysRoleNames = array_map(fn($r) => $r->shortname, $systemRoles);
        $data['System roles'] = $sysRoleNames ? implode(', ', $sysRoleNames) : 'none';

        // --- Course last access ---
        $verbose->step('Counting course access records');
        $courseAccess = $DB->count_records('user_lastaccess', ['userid' => $userid]);
        $data['Courses accessed'] = $courseAccess;

        // --- Groups ---
        $verbose->step('Counting group memberships');
        $groups = $DB->count_records('groups_members', ['userid' => $userid]);
        $data['Group memberships'] = $groups;

        // --- Log entries ---
        $verbose->step('Counting log entries');
        $logs = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {logstore_standard_log} WHERE userid = ?",
            [$userid],
        );
        $data['Log entries'] = (int) $logs->c;

        // --- Logins in last month ---
        $verbose->step('Counting logins in last month');
        $monthAgo = time() - (30 * 24 * 60 * 60);

        $successfulLogins = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {logstore_standard_log}
              WHERE userid = ? AND eventname = ? AND timecreated >= ?",
            [$userid, '\\core\\event\\user_loggedin', $monthAgo],
        );
        $data['Successful logins (last 30 days)'] = (int) $successfulLogins->c;

        $failedLogins = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {logstore_standard_log}
              WHERE eventname = ? AND timecreated >= ?
                AND other LIKE ?",
            ['\\core\\event\\user_login_failed', $monthAgo, '%"username":"' . $DB->sql_like_escape($user->username) . '"%'],
        );
        $data['Failed logins (last 30 days)'] = (int) $failedLogins->c;

        // --- Forum posts ---
        $verbose->step('Counting forum posts');
        if ($DB->get_manager()->table_exists('forum_posts')) {
            $forumPosts = $DB->get_record_sql(
                "SELECT COUNT(*) AS c FROM {forum_posts} WHERE userid = ?",
                [$userid],
            );
            $data['Forum posts'] = (int) $forumPosts->c;

            $forumDiscussions = $DB->get_record_sql(
                "SELECT COUNT(*) AS c FROM {forum_discussions} WHERE userid = ?",
                [$userid],
            );
            $data['Forum discussions started'] = (int) $forumDiscussions->c;
        }

        // --- Assignments ---
        $verbose->step('Counting assignment submissions');
        if ($DB->get_manager()->table_exists('assign_submission')) {
            $submissions = $DB->get_record_sql(
                "SELECT COUNT(*) AS c FROM {assign_submission} WHERE userid = ? AND status = 'submitted'",
                [$userid],
            );
            $data['Assignment submissions'] = (int) $submissions->c;
        }

        // --- Grades ---
        $verbose->step('Counting grades');
        $grades = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {grade_grades} WHERE userid = ? AND finalgrade IS NOT NULL",
            [$userid],
        );
        $data['Graded items'] = (int) $grades->c;

        // --- Badges ---
        $verbose->step('Counting badges');
        $badges = $DB->count_records('badge_issued', ['userid' => $userid]);
        $data['Badges issued'] = $badges;

        // --- Messages ---
        $verbose->step('Counting messages');
        $messagesSent = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {messages} WHERE useridfrom = ?",
            [$userid],
        );
        $data['Messages sent'] = (int) $messagesSent->c;

        $messagesReceived = $DB->get_record_sql(
            "SELECT COUNT(*) AS c
               FROM {message_conversations} mc
               JOIN {message_conversation_members} mcm ON mcm.conversationid = mc.id
               JOIN {messages} m ON m.conversationid = mc.id
              WHERE mcm.userid = ? AND m.useridfrom <> ?",
            [$userid, $userid],
        );
        $data['Messages received'] = (int) $messagesReceived->c;

        // --- Notifications ---
        $verbose->step('Counting notifications');
        $notifications = $DB->count_records('notifications', ['useridto' => $userid]);
        $data['Notifications'] = $notifications;

        // --- Files in user context ---
        $verbose->step('Counting files');
        $files = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {files} WHERE filename <> '.' AND userid = ?",
            [$userid],
        );
        $data['Files uploaded'] = (int) $files->c;

        $fileSize = $DB->get_record_sql(
            "SELECT COALESCE(SUM(filesize), 0) AS s FROM {files} WHERE filename <> '.' AND userid = ?",
            [$userid],
        );
        $data['Total file size (bytes)'] = (int) $fileSize->s;

        // --- User preferences ---
        $verbose->step('Counting user preferences');
        $prefs = $DB->count_records('user_preferences', ['userid' => $userid]);
        $data['User preferences'] = $prefs;

        // --- Render output ---
        $verbose->step('Rendering output');

        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $headers = array_keys($data);
            $formatter->display($headers, [array_values($data)]);
        }

        return Command::SUCCESS;
    }
}
