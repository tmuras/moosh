<?php
/**
 * moosh - Moodle Shell - RoleImport helper form.
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle33\Role\RoleImport;

use core_role_define_role_table_advanced;

class core_role_define_role_table_advanced_extended extends core_role_define_role_table_advanced {
    /**
     * Clear self reference keys for an existing role. Otherwise, a DML insert exception will occur because fo duplicate entries.
     *
     * @return void
     */
    public function clear_self_references() {
        if (empty($this->roleid)) {
            return;
        }

        $this->clear_self_reference_for_type('assign');
        $this->clear_self_reference_for_type('override');
        $this->clear_self_reference_for_type('switch');
        $this->clear_self_reference_for_type('view');
    }

    public function mark_changed_permissions($xmldata) {
        $this->changed = array();

        foreach ($this->capabilities as $cap) {
            if ($cap->locked || $this->skip_row($cap)) {
                // The user is not allowed to change the permission for this capability.
                continue;
            }

            $permission = array_key_exists($cap->name, $this->permissions) ? $this->permissions[$cap->name] : null;
            if (null === $permission) {
                $permission = array_key_exists($cap->name, $xmldata->permissions) ? $xmldata->permissions[$cap->name] : null;
            }
            if (null === $permission) {
                // A permission was not specified in XML data or inherited by base role or archetype.
                continue;
            }

            // If the permission has changed, update $this->permissions and
            if (!array_key_exists($cap->name, $this->permissions) || $this->permissions[$cap->name] != $permission) {
                $this->permissions[$cap->name] = $permission;
            }

            // Record the fact there is data to save. Here is too late to detect changes because parent class has already
            // overwritten data based on base role, archetype or XML data so just mark all as changed.
            $this->changed[] = $cap->name;
        }
    }

    public function clear_self_reference_for_type($type) {
        $wanted = $this->{'allow' . $type};
        foreach ($wanted as $idx => $roleid) {
            if ($roleid == -1) {
                unset($this->{'allow' . $type}[$idx]);
            }
        }
    }
}
