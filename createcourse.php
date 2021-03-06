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
 * This file display and process the form to create a course for selfstudy.
 * @package    block_ps_selfstudy
 * @copyright  2015 Andres Ramos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once('createcourse_form.php');

require_login();
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

global $OUTPUT, $PAGE, $CFG;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/createcourse.php');
$PAGE->set_pagelayout('standard');
$formpage = new createcourse_form();

$today = time();
$toform['date_created'] = $today;
$formpage->set_data($toform);

// Define headers.
$PAGE->set_title(get_string('title_createcourse', 'block_ps_selfstudy'));
// Nav breadcump.
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('title_managecourses', 'block_ps_selfstudy'),
                    new moodle_url('/blocks/ps_selfstudy/managecourses.php'));
$PAGE->navbar->add(get_string('title_createcourse', 'block_ps_selfstudy'));

if ($formpage->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
    redirect($courseurl);

} else if ($fromform = $formpage->get_data()) {

    if (!$DB->insert_record('block_ps_selfstudy_course', $fromform)) {
        print_error('inserterror', 'block_ps_selfstudy');
    }

    $courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
    redirect($courseurl);

} else {
    // ...form didn't validate or this is the first display.
    echo $OUTPUT->header();
    if (has_capability('block/ps_selfstudy:managecourses', $context, $USER->id)) {
        $formpage->display();
    } else {
        print_error('nopermissiontoviewpage', 'error', '');
    }
    echo $OUTPUT->footer();

}