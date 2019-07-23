<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Language;
use Moosh\MooshCommand;

class LanguagesUpdate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('update', 'languages');

        $this->addOption('l|lang', 'use two letters language code (-l=he)'); // Not implemented, yet.
        $this->addOption('v|version:', 'version');
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL;
    }

    private function update_all_languages($lang) {

        // TODO: Use above $lang to update a single language pack.

        global $CFG;

        require_once($CFG->libdir.'/filelib.php');
        require_once($CFG->libdir.'/componentlib.class.php');

        \core_php_time_limit::raise();

        $installer = new \lang_installer();

        if (!$availablelangs = $installer->get_remote_list_of_languages()) {
            print_error('cannotdownloadlanguageupdatelist', 'error');
        }
        $md5array = array();    // (string)langcode => (string)md5
        foreach ($availablelangs as $alang) {
            $md5array[$alang[0]] = $alang[1];
        }

        // filter out unofficial packs
        $currentlangs = array_keys(get_string_manager()->get_list_of_translations(true));
        $updateablelangs = array();
        foreach ($currentlangs as $clang) {
            if (!array_key_exists($clang, $md5array)) {
                $notice_ok[] = get_string('langpackupdateskipped', 'tool_langimport', $clang);
                continue;
            }
            $dest1 = $CFG->dataroot.'/lang/'.$clang;
            $dest2 = $CFG->dirroot.'/lang/'.$clang;

            if (file_exists($dest1.'/langconfig.php') || file_exists($dest2.'/langconfig.php')){
                $updateablelangs[] = $clang;
            }
        }

        // then filter out packs that have the same md5 key
        $neededlangs = array();   // all the packs that needs updating
        foreach ($updateablelangs as $ulang) {
            if (!$this->is_installed_lang($ulang, $md5array[$ulang])) {
                $neededlangs[] = $ulang;
            }
        }

        make_temp_directory('');
        make_upload_directory('lang');

        // clean-up currently installed versions of the packs
        foreach ($neededlangs as $packindex => $pack) {
            if ($pack == 'en') {
                continue;
            }

            // delete old directories
            $dest1 = $CFG->dataroot.'/lang/'.$pack;
            $dest2 = $CFG->dirroot.'/lang/'.$pack;
            $rm1 = false;
            $rm2 = false;
            if (file_exists($dest1)) {
                if (!remove_dir($dest1)) {
                    $notice_error[] = 'Could not delete old directory '.$dest1.', update of '.$pack.' failed, please check permissions.';
                    unset($neededlangs[$packindex]);
                    continue;
                }
            }
            if (file_exists($dest2)) {
                if (!remove_dir($dest2)) {
                    $notice_error[] = 'Could not delete old directory '.$dest2.', update of '.$pack.' failed, please check permissions.';
                    unset($neededlangs[$packindex]);
                    continue;
                }
            }
        }

        // install all needed language packs
        $installer->set_queue($neededlangs);
        $results = $installer->run();
        $updated = false;    // any packs updated?
        foreach ($results as $langcode => $langstatus) {
            switch ($langstatus) {
                case \lang_installer::RESULT_DOWNLOADERROR:
                    $a       = new stdClass();
                    $a->url  = $installer->lang_pack_url($langcode);
                    $a->dest = $CFG->dataroot.'/lang';
                    print_error('remotedownloaderror', 'error', 'index.php', $a);
                    break;
                case \lang_installer::RESULT_INSTALLED:
                    $updated = true;
                    $notice_ok[] = get_string('langpackinstalled', 'tool_langimport', $langcode);
                    break;
                case \lang_installer::RESULT_UPTODATE:
                    $notice_ok[] = get_string('langpackuptodate', 'tool_langimport', $langcode);
                    break;
            }
        }

        if ($updated) {
            $notice_ok[] = get_string('langupdatecomplete', 'tool_langimport');
        } else {
            $notice_ok[] = get_string('nolangupdateneeded', 'tool_langimport');
        }

        unset($installer);

        get_string_manager()->reset_caches();

    }

    /**
     * checks the md5 of the zip file, grabbed from download.moodle.org,
     * against the md5 of the local language file from last update
     * @param string $lang
     * @param string $md5check
     * @return bool
     */
    private function is_installed_lang($lang, $md5check) {
        global $CFG;
        $md5file = $CFG->dataroot.'/lang/'.$lang.'/'.$lang.'.md5';
        if (file_exists($md5file)){
            return (file_get_contents($md5file) == $md5check);
        }
        return false;
    }

    public function execute()
    {
        $options = $this->expandedOptions;
        if (empty($options['lang'])) $options['lang'] = 'en';

        $this->update_all_languages($options['lang']);
    }
}
