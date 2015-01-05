<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contains definition of cutsom user profile field.
 *
 * @package    profilefield_myprofilefield
 * @category   profilefield
 * @copyright  2012 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class profile_define_myprofilefield extends profile_define_base {

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field specific to the current data type
     *
     * @param moodleform $form reference to moodleform for adding elements.
     */
    function define_form_specific(&$form) {
        //Add elements, set defualt value and define type of data
        $form->addElement('radio', 'defaultdata', get_string('mystring', 'profilefield_myprofilefield'));
        $form->setDefault('defaultdata', 1); // defaults to 'yes'
        $form->setType('defaultdata', PARAM_BOOL);
    }

    /**
     * Validate the data from the add/edit profile field form
     * that is specific to the current data type
     *
     * @param object $data from the add/edit profile field form
     * @param object $files files uploaded
     * @return array associative array of error messages
     */
    function define_validate_specific($data, $files) {
        // overwrite if necessary
    }

    /**
     * Alter form based on submitted or existing data
     *
     * @param moodleform $mform reference to moodleform
     */
    function define_after_data(&$mform) {
        // overwrite if necessary
    }

    /**
     * Preprocess data from the add/edit profile field form
     * before it is saved.
     *
     * @param object $data from the add/edit profile field form
     * @return object processed data object
     */
    function define_save_preprocess($data) {
        // overwrite if necessary
    }

}


