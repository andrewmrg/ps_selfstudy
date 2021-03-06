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
 * This files display the table of all courses created.
 * @package     block_ps_selfstudy
 * @copyright   2015 Andres Ramos
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managecourses_table extends table_sql {

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id,  this is used
     *      as a key when storing table properties like sort order in the session.
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('course_code', 'course_platform', 'course_name', 'course_description',
                'course_hours', 'course_type', 'course_status', 'date_created', 'actions');
        $this->sortable(true, 'course_code',  SORT_ASC);
        $this->collapsible(false);
        $this->no_sorting('actions');
        $this->no_sorting('course_description');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array(
            get_string('coursecode',  'block_ps_selfstudy'),
            get_string('platform',  'block_ps_selfstudy'),
            get_string('coursename',  'block_ps_selfstudy'),
            get_string('description',  'block_ps_selfstudy'),
            get_string('hours',  'block_ps_selfstudy'),
            get_string('coursetype',  'block_ps_selfstudy'),
            get_string('status',  'block_ps_selfstudy'),
            get_string('datecreated',  'block_ps_selfstudy'),
            get_string('action',  'block_ps_selfstudy')
        );
        $this->define_headers($headers);
    }

    /**
     * Generate the display of the course code.
     * @param object $values the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_course_code($values) {
        // If the data is being downloaded than we don't want to show HTML.

        global $DB;
        $descriptionlink = $DB->get_record('block_ps_selfstudy_course', array('id' => $values->id), $fields = 'description_link');
        $url = $descriptionlink->description_link;

        if (!preg_match("~^(?:f|ht)tps?://~i",  $url)) {
            $url = "http://" . $url;
        }

        if (!empty($descriptionlink) && $descriptionlink->description_link !== null && $descriptionlink->description_link !== "") {
            return '<a href="'.$descriptionlink->description_link.'" target="_blank">'.$values->course_code.'</a>';
        } else {
            return $values->course_code;
        }

    }

    /**
     * Generate the display of the course type.
     * @param object $values the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_course_type($values) {
        // If the value is 0,  show Phisical copy,  else,  Link course.
        if ($values->course_type == 0) {
            return get_string('physicalcourse',  'block_ps_selfstudy');
        } else {
            return get_string('linkcourse', 'block_ps_selfstudy');
        }
    }

    /**
     * Generate the display of the course status.
     * @param object $values the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_course_status($values) {
        // If the value is 0,  show Active copy,  else,  Disable.
        if ($values->course_status == 0) {
            return get_string('active', 'block_ps_selfstudy');
        } else {
            return get_string('disable', 'Disable');
        }
    }

    /**
     * Generate the display of the date's course creation.
     * @param object $values the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_date_created($values) {
        // Show readable date from timestamp.
        $date = $values->date_created;
        return date("M d,  Y", $date);
    }

    /**
     * Generate the display of the action's links.
     * @param object $values the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($values) {

        return '<a href="editcourse.php?id=' . $values->id . '">Edit</a>
        - <a href="action.php?action=deletecourse&courseid=' . $values->id . '" onclick="return checkConfirm()">
        ' . get_string('delete', 'block_ps_selfstudy') . '</a>';

    }
}