<?php
/**
 * moosh - Moodle Shell
 *
 * @auhtor  2021 Céline Pervès <cperves@unistra.fr>
 * @copyright Université de Strasbourg unistra.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Plugin;
use Moosh\MooshCommand;

class PluginHideShow extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('hideshow', 'plugin');
        $this->addArgument('plugintype');
        $this->addArgument('pluginname');
        $this->addArgument('show');
    }

    public function execute()
    {
        global $CFG,$DB;

        if(!is_numeric($this->arguments[2])){
            echo "hideshow must be an integer\n";
            die;
        }
        $show=(int)$this->arguments[2];
        $plugintype = $this->arguments[0];
        $pluginname = $this->arguments[1];
        switch($plugintype){
            case 'block':
                self::hideshow_values_check($pluginname,array(0,1));
                if (!$block = $DB->get_record('block', array('name'=>$pluginname))) {
                    echo "$plugintype $pluginname not exists";
                    break;
                }
                $DB->set_field('block', 'visible', $show, array('id'=>$block->id));
                add_to_config_log('block_visibility', $block->visible, ''.$show, $block->name);
                \core_plugin_manager::reset_caches();
                break;
            case 'mod':
                self::hideshow_values_check($show,array(0,1));
                if (!$module = $DB->get_record("modules", array("name"=>$pluginname))) {
                    echo "$plugintype $pluginname not exists";
                    break;
                }
                $DB->set_field("modules", "visible", $show, array("id"=>$module->id)); // Hide main module
                if($show==0){
                    // Remember the visibility status in visibleold.
                    // And hide...
                    $sql = "UPDATE {course_modules}
                                  SET visibleold=visible, visible=0
                                WHERE module=?";
                    $DB->execute($sql, array($module->id));
                    // Increment course.cacherev for courses where we just made something invisible.
                    // This will force cache rebuilding on the next request.
                    increment_revision_number('course', 'cacherev',
                        "id IN (SELECT DISTINCT course
                                               FROM {course_modules}
                                              WHERE visibleold=1 AND module=?)",
                        array($module->id));
                    \core_plugin_manager::reset_caches();
                }else{
                    $DB->set_field('course_modules', 'visible', '1', array('visibleold'=>1, 'module'=>$module->id)); // Get the previous saved visible state for the course module.
                    // Increment course.cacherev for courses where we just made something visible.
                    // This will force cache rebuilding on the next request.
                    increment_revision_number('course', 'cacherev',
                        "id IN (SELECT DISTINCT course
                                               FROM {course_modules}
                                              WHERE visible=1 AND module=?)",
                        array($module->id));
                    \core_plugin_manager::reset_caches();
                }
                break;
            case 'assignfeedback':
            case 'assignsubmission':
                self::hideshow_values_check($show,array(0,1));
                //check pluginname
                if(count((array)get_config($plugintype.'_'.$pluginname))==0){
                    cli_error(get_string('hideshow_plugin_cli_pluginnotexists','tool_cmdlinetools',array('name'=>$pluginname, 'type'=> $plugintype)));
                }
                set_config('disabled', $show==0?1:0, $plugintype . '_' . $pluginname);
                break;
            case 'qtype':
                require_once($CFG->dirroot.'/question/engine/bank.php');
                self::hideshow_values_check($show,array(0,1));
                //check qtype
                if(!array_key_exists ($pluginname, \question_bank::get_all_qtypes())){
                    echo "qtype $pluginname does not exist";
                }
                if($show==0){
                    set_config($pluginname . '_disabled',1, 'question');
                }else{
                    $qtype = \question_bank::get_qtype($pluginname,true);
                    if (!$qtype->menu_name()) {
                        echo "cannot enable qtype $pluginname";
                        break;
                    }
                    unset_config($pluginname . '_disabled', 'question');
                }
                break;
            case 'qbehaviour':
                self::hideshow_values_check($show,array(0,1));
                require_once($CFG->dirroot.'/question/engine/lib.php');
                //check qbehaviour
                if(!array_key_exists ($pluginname, core_component::get_plugin_list('qbehaviour'))){
                    echo "qbehaviour $pluginname does not exist";
                    break;
                }
                if(!question_engine::is_behaviour_archetypal($pluginname)){
                    echo "can't enable/disable qbehaviour $pluginname";
                    break;
                }
                $config = get_config('question');
                if (!empty($config->disabledbehaviours)) {
                    $disabledbehaviours = explode(',', $config->disabledbehaviours);
                } else {
                    $disabledbehaviours = array();
                }
                $disabledbehaviours_index = array_search($pluginname,$disabledbehaviours);
                if($disabledbehaviours_index!==false && $show==1){
                    unset($disabledbehaviours[$disabledbehaviours_index]);
                    set_config('disabledbehaviours', implode(',', $disabledbehaviours), 'question');
                }else if ($disabledbehaviours_index === false && $show ==0){
                    $disabledbehaviours[] = $pluginname;
                    set_config('disabledbehaviours', implode(',', $disabledbehaviours), 'question');
                }
                \core_plugin_manager::reset_caches();
                break;
            case 'enrol':
                self::hideshow_values_check($show,array(0,1));
                $syscontext = \context_system::instance();
                $enabled = enrol_get_plugins(true);
                $all     = enrol_get_plugins(false);
                if(!array_key_exists($pluginname,$all)){
                    echo "enrol method $pluginname not exists";
                    break;
                }
                if($show==0 && array_key_exists($pluginname,$enabled)){
                    unset($enabled[$pluginname]);
                    set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));
                    \core_plugin_manager::reset_caches();
                    $syscontext->mark_dirty(); // resets all enrol caches
                }else if($show == 1  && !array_key_exists($pluginname, $enabled)){
                    $enabled = array_keys($enabled);
                    $enabled[] = $pluginname;
                    set_config('enrol_plugins_enabled', implode(',', $enabled));
                    \core_plugin_manager::reset_caches();
                    $syscontext->mark_dirty(); // resets all enrol caches
                }
                break;
            case 'filter':
                require_once($CFG->libdir.'/filterlib.php');
                self::hideshow_values_check($show,array(1,-1,-9999));
                //check if filter exists
                if(!array_key_exists($pluginname,filter_get_all_installed())){
                    echo "filter $pluginname does not exist";
                    break;
                }
                filter_set_global_state('filter/'.$pluginname, $show);
                if ($show == TEXTFILTER_DISABLED) {
                    filter_set_applies_to_strings('filter/'.$pluginname, false);
                }
                reset_text_filters_cache();
                break;
            case 'editor':
                self::hideshow_values_check($show,array(0,1));
                $editors=editors_get_available();
                if(!array_key_exists($pluginname,$editors)){
                    echo "editor $pluginname doesn't exists";
                    break;
                }
                $active_editors = explode(',', $CFG->texteditors);
                $active_editors_index = array_search($pluginname,$active_editors);
                if($show==0 && $active_editors_index !== false){
                    unset($active_editors[$active_editors_index]);
                    set_config('texteditors', implode(',', $active_editors));

                }else if($show == 1  && $active_editors_index === false){
                    $active_editors[]=$pluginname;
                    set_config('texteditors', implode(',', $active_editors));
                }
                break;
            case 'auth':
                self::hideshow_values_check($show, array(0,1));
                if(!exists_auth_plugin($pluginname)){
                    echo "auth plugin $pluginname doesn't exists";
                    break;
                }
                get_enabled_auth_plugins(true); // fix the list of enabled auths
                if (empty($CFG->auth)) {
                    $authsenabled = array();
                } else {
                    $authsenabled = explode(',', $CFG->auth);
                }
                if($show==1){
                    if (!in_array($pluginname, $authsenabled)) {
                        $authsenabled[] = $pluginname;
                        $authsenabled = array_unique($authsenabled);
                        set_config('auth', implode(',', $authsenabled));
                    }
                    \core\session\manager::gc(); // Remove stale sessions.
                    \core_plugin_manager::reset_caches();
                }else{
                    $key = array_search($pluginname, $authsenabled);
                    if ($key !== false) {
                        unset($authsenabled[$key]);
                        set_config('auth', implode(',', $authsenabled));
                    }

                    if ($pluginname == $CFG->registerauth) {
                        set_config('registerauth', '');
                    }
                    \core\session\manager::gc(); // Remove stale sessions.
                    \core_plugin_manager::reset_caches();
                }
                break;
            case 'license':
                self::hideshow_values_check($show, array(0,1));
                require_once($CFG->libdir.'/licenselib.php');
                if(license_manager::get_license_by_shortname($pluginname)==null){
                    echo "license plugin $pluginname doesn't exists";
                    break;
                }
                if($pluginname == $CFG->sitedefaultlicense){
                    echo "can\'t enable/disable license $pluginname";
                    break;
                }
                if($show==1){
                    license_manager::enable($pluginname);
                }else{
                    license_manager::disable($pluginname);
                }
                break;
            case 'repository':
                echo 'not implemented because require repository setting form';
                break;
            case 'courseformat':
                self::hideshow_values_check($show, array(0,1));
                require_once($CFG->libdir.'/classes/plugin_manager.php');
                $formatplugins = \core_plugin_manager::instance()->get_plugins_of_type('format');
                if (!isset($formatplugins[$pluginname])) {
                    echo "cours eformat $pluginname doesn't exits";
                    break;
                }
                if (get_config('moodlecourse', 'format') === $pluginname){
                    echo "course foramt $pluginname can't be enabled/disabled";
                    break;
                }
                if($show == 0){
                    set_config('disabled', 1, 'format_'. $pluginname);
                    \core_plugin_manager::reset_caches();
                }else{
                    unset_config('disabled', 'format_'. $pluginname);
                    \core_plugin_manager::reset_caches();
                }
                break;
            case 'availability':
                if($show==0){
                    set_config('disabled', 1, 'availability_' . $pluginname);
                }else{
                    unset_config('disabled', 'availability_' . $pluginname);
                }
                \core_plugin_manager::reset_caches();
                break;
            default:
                echo "bad plugin type $plugintype or not taken in charge";
                break;
        }
    }
    private static function hideshow_values_check($hideshow,$possiblevalues){
        if(!in_array($hideshow, $possiblevalues)){
            cli_error(get_string('hideshow_plugin_cli_parametervalues','tool_cmdlinetools',implode(',', $possiblevalues)));
        }
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command enable to hide show a plugin";
        $help .= "\n\nplugintype can be block, mod, assignfeedback, assignsubmission, qtype, qbehaviour, enrol, filter, editor, auth, license, repository, courseformat or avaibility";
        return $help;
    }
}
