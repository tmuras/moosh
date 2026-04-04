<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Auth;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * auth:info implementation for Moodle 5.1.
 */
class AuthInfo51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'plugin',
            InputArgument::REQUIRED,
            'Auth plugin name (e.g. manual, email, ldap, oauth2)',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $pluginName = $input->getArgument('plugin');

        // Validate plugin exists.
        if (!exists_auth_plugin($pluginName)) {
            $output->writeln("<error>Authentication plugin '$pluginName' not found.</error>");
            return Command::FAILURE;
        }

        $authPlugin = get_auth_plugin($pluginName);
        $enabledList = !empty($CFG->auth) ? explode(',', $CFG->auth) : [];
        $isEnabled = in_array($pluginName, $enabledList, true) || $pluginName === 'manual';

        $data = [];

        // --- Basic info ---
        $verbose->step('Collecting plugin information');
        $data['Plugin'] = $pluginName;
        $data['Name'] = $authPlugin->get_title();
        $data['Description'] = $authPlugin->get_description();
        $data['Enabled'] = $isEnabled ? 'yes' : 'no';

        // Position in order.
        if ($isEnabled) {
            $pos = array_search($pluginName, $enabledList, true);
            if ($pluginName === 'manual' && $pos === false) {
                $data['Order position'] = 'always active';
            } else {
                $data['Order position'] = $pos !== false ? $pos + 1 : 'N/A';
            }
        }

        // --- Capabilities ---
        $verbose->step('Checking capabilities');
        $data['Can signup'] = $authPlugin->can_signup() ? 'yes' : 'no';
        $data['Can change password'] = $authPlugin->can_change_password() ? 'yes' : 'no';
        $data['Can reset password'] = $authPlugin->can_reset_password() ? 'yes' : 'no';
        $data['Can confirm'] = $authPlugin->can_confirm() ? 'yes' : 'no';
        $data['Can be manually set'] = $authPlugin->can_be_manually_set() ? 'yes' : 'no';
        $data['Is internal'] = $authPlugin->is_internal() ? 'yes' : 'no';
        $data['Prevent local passwords'] = $authPlugin->prevent_local_passwords() ? 'yes' : 'no';

        // --- User statistics ---
        $verbose->step('Counting users');
        $totalUsers = $DB->count_records('user', ['auth' => $pluginName, 'deleted' => 0]);
        $data['Total users'] = $totalUsers;

        $suspendedUsers = $DB->count_records('user', ['auth' => $pluginName, 'deleted' => 0, 'suspended' => 1]);
        $data['Suspended users'] = $suspendedUsers;

        $confirmedUsers = $DB->count_records('user', ['auth' => $pluginName, 'deleted' => 0, 'confirmed' => 1]);
        $data['Confirmed users'] = $confirmedUsers;

        $neverLoggedIn = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {user} WHERE auth = ? AND deleted = 0 AND lastlogin = 0",
            [$pluginName],
        );
        $data['Never logged in'] = $neverLoggedIn;

        // --- Configuration settings ---
        $verbose->step('Reading configuration');
        $configs = $DB->get_records('config_plugins', ['plugin' => 'auth_' . $pluginName]);
        $configCount = 0;
        $lockedFields = [];

        foreach ($configs as $config) {
            // Count field locks.
            if (str_starts_with($config->name, 'field_lock_') && $config->value !== 'unlocked') {
                $fieldName = substr($config->name, strlen('field_lock_'));
                $lockedFields[] = "$fieldName ($config->value)";
            }
            $configCount++;
        }

        $data['Configuration settings'] = $configCount;
        $data['Locked fields'] = $lockedFields ? implode(', ', $lockedFields) : 'none';

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
